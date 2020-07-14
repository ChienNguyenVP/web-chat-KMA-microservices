<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\UserResource;

class ContactController extends Controller
{
    public function getAll($userId) 
    {
        return User::where('id', $userId)->get();
    }
    public function searchUser(Request $request) 
    {
        $search = $request->email;
    	$dataUser = User::where('email', 'LIKE', "%$search%")->get();
    	return $dataUser;
    }
}
