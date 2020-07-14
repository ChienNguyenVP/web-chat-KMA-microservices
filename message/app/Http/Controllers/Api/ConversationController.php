<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Conversation;
use App\Events\AcceptCall;
use App\Events\CallVideo;
use App\Events\ConversationEvent;
use App\Events\SendMessage;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\MessageResource;
use App\MessageKey;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    public function getConversations(Request $request)
    {    
        $user = Auth::user();        

        $conversations = $this->getContactId($user);
        $users = [];
        foreach ($conversations as $key => $conversation) {
            $unreadId = Message::select(\DB::raw("sender_id as sender_id, count(sender_id) as message_count"))
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->where('conversation_id', $conversation->id)
            ->groupBy('sender_id')
            ->first();
            $id = array_diff($conversation->users, [$user->id]);

            $userInfo = $this->getUser(array_values($id)[0]);
            if ($userInfo == []) {
                return response(['message' => 'You dont have any conversation']);
            }
            array_push($users, [
                                'conversation_id' => $conversation->id,
                                'my_id' => $user->id,
                                'contact_id' => $userInfo[0]->id,
                                'contact_name' => $userInfo[0]->name,
                                'avatar' => $userInfo[0]->avatar,
                                'unread' => $unreadId['message_count'],
                                'is_encrypt' => $conversation->is_encrypt
                                ]);             
        }

        return response()->json($users);
    }

    public function getUser($id) {
        $contactResponse = Http::get('http://api.webchat.com:8000/getall/'. $id);
        return json_decode($contactResponse->body());
    }
    
    public function getContactId($user) 
    {
        return Conversation::where('users', 'like', '%'.$user->id.'%')->orderBy('updated_at', 'desc')->orderBy('created_at')->get();
    }

    public function getMessage($conversationId)
    {
        $message = MessageResource::collection(Message::where('conversation_id', $conversationId)
                                                ->orderBy('created_at')->get());
        return $message;
    }

    public function sendMessage(Request $request) 
    {
        $userId = Auth::user()->id;
        if ($request->call == true) {
            $receiver =  $request->receiverId;
            $sender_id = $userId;
            broadcast(new CallVideo($receiver, $sender_id));
            return $sender_id;
        }

        $message = Message::create([
            'conversation_id' => $request->conversationId,
            'content' => $request->message,
            'sender_id' => $userId,
            'receiver_id' => $request->receiverId,
            'is_encrypt' => $request->isEncrypt,
            'file_id' => $request->file_id,
            'read' => 0
        ]);
        $message['file_type'] = $request->file_type;
        $message['file_path'] = $request->file_path;
        $message['file_name'] = $request->file_name;
        Conversation::find($request->conversationId)->touch();
        broadcast(new SendMessage($message));

        return response()->json($message);
    }
    public function makeRead(Request $request) 
    {
        $readMessage = Message::where('conversation_id', $request->conversation_id)->update(['read'=> true]);
        return $readMessage;
    }

    public function startConversation(Request $request) {
        $user = Auth::user();
        $userId = $user->id;
        $messageKey = $this->genMessageKey();
        $messKeyHashUser = $this->hashMessageKey($messageKey, Auth::user()->public_key);
        $contactId = $request->contact_id;
        $contactUser = $this->getUser($contactId);
        $messKeyContactHash = $this->hashMessageKey($messageKey, $contactUser[0]->public_key);
        $conversation = Conversation::where('users', 'like', '%'.$userId.'%')
                                    ->where('users', 'like', '%'.$contactId.'%')->first();

        if ($conversation) {
            return [
                'conversation_id' => $conversation->id,
                'contact_id' => $contactId,
            ];
        }
        $conversation = Conversation::create([
            'users' => [$userId, $contactId],
            'is_encrypt' => 0
        ]);

        MessageKey::create([
            'contact_id' => $userId,
            'conversation_id' => $conversation->id,
            'message_key_hash' => base64_encode($messKeyHashUser)
        ]);
        $contactUser = $this->getUser($contactId);
        MessageKey::create([
            'contact_id' => $contactId,
            'conversation_id' => $conversation->id,
            'message_key_hash' => base64_encode($messKeyContactHash)
        ]);

        $conDetail = [
            'conversation_id' => $conversation->id,
            'contact_id' => $contactId,
            'contact_name' => $contactUser[0]->name,
            'avatar' => $contactUser[0]->avatar,
            'my_id' => $userId,
            'unread' => null,
            'is_encrypt' => $conversation->is_encrypt
        ];  
        $conEvent = [
            'conversation_id' => $conversation->id,
            'contact_id' => $userId,
            'contact_name' => $user->name,
            'avatar' => $user->avatar,
            'my_id' => $contactId,
            'unread' => null,
            'is_encrypt' => $conversation->is_encrypt
        ];
        broadcast(new ConversationEvent($conEvent));

        return response()->json($conDetail);
    }

    public function acceptCall(Request $request) {
        $idCall = $request->idCall;
        $inviter = $request->inviter;
        broadcast(new AcceptCall($idCall, $inviter));
    }
    public function genMessageKey() {
        return $messageKey = Str::random(10);
    }
    public function hashMessageKey($messageKey, $public_key) {
        openssl_public_encrypt($messageKey, $encrypted, $public_key);
        return $encrypted;
    }

    public function pbkdf2($password)
    {
        $iterations = 1000;
        $salt = "261cf07ed6818288c492";
        $key = hash_pbkdf2("sha256", $password, $salt, $iterations, 20);
        return $key;
    }

    public function decryptPrivateKey($password, $privateKeyHash) {
        $cipher = "aes-256-cbc";
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
            $iv = "1234567891234567";
            $key = $this->pbkdf2($password);
            $privateKey = openssl_decrypt($privateKeyHash, $cipher, $key, $options=0, $iv);
            return $privateKey;
        }
    }
    public function getKey(Request $request) 
    {
        $hash_password = Auth::user()->password;
        $password = $request->password;
        $conversationId = $request->conversation_id;
        $privateKeyHash = Auth::user()->private_key_hash;
        
        if(Hash::check($password, $hash_password)){
            $privateKey = $this->decryptPrivateKey($password, $privateKeyHash);
            $messageKeyObj = MessageKey::where('contact_id', Auth::user()->id)
                                        ->where('conversation_id', $conversationId)->first();
            $messageKeyHash = base64_decode($messageKeyObj->message_key_hash); 
            openssl_private_decrypt($messageKeyHash, $messageKey, $privateKey);

            $conversation = Conversation::find($conversationId);
            if ($conversation->is_encrypt == 0) {
                Conversation::find($conversationId)->update(['is_encrypt' => 1]);
            }
            return $messageKey;
        }
        return response()->json(['message' => 'Sai mật khẩu']);
    }
    public function checkEncrypt(Request $request) {
        $conversation = Conversation::find($request->conversation_id);
        if($conversation->is_encrypt == false) {
            return response()->json(['message' => false]);
        }
        return response()->json(['message' => true]);
    }
    public function checkPassword(Request $request) {
        $hash_password = Auth::user()->password;
        $password = $request->password;
        if(Hash::check($password, $hash_password)) {
            return response()->json(['message' => 'correct password']);
        }
        return response(['message' => 'Sai mật khẩu']);
    }
}
