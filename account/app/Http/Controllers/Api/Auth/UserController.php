<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function edit(Request $request) {
        if($request->get('image'))
        {
          $image = $request->get('image');
          $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
          \Image::make($request->get('image'))->save(public_path('images/').$name);
        }

       $user = new User();
       $user->avatar = $name;
       $user->name = $request->name;
       $user->gender = $request->gender;
       $user->phone = $request->phone;
       $user->address = $request->address;
       $user->save();

       return response()->json(['success' => 'You have successfully uploaded an image'], 200);
    }
}
