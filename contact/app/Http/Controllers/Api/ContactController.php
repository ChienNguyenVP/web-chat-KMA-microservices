<?php

namespace App\Http\Controllers\Api;

use App\Contact;
use App\Events\RequestFriend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function addFriend(Request $request) {
        $userId = Auth::user()->id;
        $contact = [
            'user_id' => $userId,
            'contact_id' => $request->contact_id,
            'status' => null,
        ];
        $contact = Contact::create($contact);
        $contactDetail = $this->getUser($userId)[0];
        $contactResult = [
            'id' => $contact->id,
            'user_id' => $contact->user_id,
            'contact_id' => $contact->contact_id,
            'status' => $contact->status,
            'name' => $contactDetail->name,
            'avatar' => $contactDetail->avatar
        ];

        broadcast(new RequestFriend($contactResult));

        return response()->json($contactResult);
    }

    public function checkFriend(Request $request) {
        $contactPending = Contact::where('user_id', Auth::user()->id)
        ->where('contact_id', $request->contact_id)
        ->where('status', null)->get();
        if (!$contactPending->isEmpty()) {
            return response()->json([ 'message' => 'pending' ]);
        }
        $contact = Contact::where('user_id', Auth::user()->id)
                            ->where('contact_id', $request->contact_id)
                            ->where('status', true)->get();
        if($contact->isEmpty()) {
            return response()->json([ 'message' => false ]);
        }else {
            return response()->json([ 'message' => true ]);
        }     
    }

    public function getFriends() {
        $userId = Auth::user()->id;
        $list = [];
        $friends = Contact::where('user_id', $userId)->orWhere('contact_id', $userId)->where('status', true)->orderBy('created_at')->get();

        foreach ($friends as $key => $friend) {
            $idFriend = $friend->contact_id;
            if ($friend->contact_id == $userId) {
                $idFriend = $friend->user_id;
            }
            $userInfo = $this->getUser($idFriend);
            if($userInfo == []) {
                return response(['message' => 'You dont have any friend' ]);
            }
            array_push($list, [
                'id' => $friend->id,
                'user_id' => $userId,
                'contact_id' => $idFriend,
                'name' => $userInfo[0]->name,
                'avatar' => $userInfo[0]->avatar,
                ]);
        }
        return response()->json($list);
    }
    public function getUser($id) {
        $contactResponse = Http::get('http://api.webchat.com:8000/getall/'. $id);
        return json_decode($contactResponse->body());
    }

    public function getRequests() {
        $list = [];
        $requests = Contact::where('contact_id', Auth::user()->id)->where('status', null)->orderBy('created_at')->get();
        foreach ($requests as $key => $request) {
            $userInfo = $this->getUser($request->user_id);
            array_push($list, [
                'id' => $request->id,
                'user_id' => $request->contact_id,
                'contact_id' => $request->user_id,
                'name' => $userInfo[0]->name,
                'avatar' => $userInfo[0]->avatar,
                ]);
        }

        return response()->json($list);
    }

    public function acceptFriend(Request $request) {
        $acceptFriend = Contact::find($request->id)->update([
            'status' => true
        ]);
        $contact = Contact::find($request->id);
        $friendDetail = $this->getUser($contact->user_id)[0];
        $friend = [
            'name' => $friendDetail->name,
            'avatar' => $friendDetail->avatar,
            'id' => $contact->id,
            'contact_id' => $contact->user_id,
            'user_id' => $contact->contact_id
        ];

        return $friend;
    }
}
