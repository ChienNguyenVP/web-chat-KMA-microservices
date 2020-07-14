<?php

namespace App\Http\Controllers\Api\Conversation;

use App\Http\Controllers\Controller;
use App\Model\Message;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ConversationController extends Controller
{
    public function getConversations(Request $request)
    {
        // $user = $request->user()
        $messages = Message::all();
        return response()->json($messages);
    }
    public function getToken(Request $request)
    {
        dd($request->all());
    }
}
