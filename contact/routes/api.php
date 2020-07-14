<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::group(['middleware' => 'auth:external'], function(){
//     Route::get('test', function(){
//         dd(\Auth::user()->id);
//     });
// });
Route::namespace('Api')->middleware(['auth:external'])->group(function () {
    Route::group([
        'prefix' => 'contact'
    ], function () {
        Route::post('searchUser', 'ContactController@searchUser')->name('searchUser');
        Route::post('addFriend', 'ContactController@addFriend')->name('addFriend');
        Route::post('checkFriend', 'ContactController@checkFriend')->name('checkFriend');
        Route::get('getFriends', 'ContactController@getFriends')->name('getFriends');
        Route::get('getRequests', 'ContactController@getRequests')->name('getRequests');
        Route::post('acceptFriend', 'ContactController@acceptFriend')->name('acceptFriend');
    });
});
