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
        'prefix' => 'conversation'
    ], function () {
        Route::get('conversations', 'ConversationController@getConversations')->name('conversation');
        Route::get('messages/{conversation_id}', 'ConversationController@getMessage')->name('message');
        Route::post('sendMessage', 'ConversationController@sendMessage')->name('sendMessage');
        Route::post('makeRead', 'ConversationController@makeRead')->name('makeRead');
        Route::post('startConversation', 'ConversationController@startConversation')->name('startConversation');
        Route::post('acceptCall', 'ConversationController@acceptCall')->name('acceptCall');
        Route::post('getKey', 'ConversationController@getKey')->name('getKey');
        Route::post('checkEncrypt', 'ConversationController@checkEncrypt')->name('checkEncrypt');
        Route::post('checkPassword', 'ConversationController@checkPassword')->name('checkPassword');
    });
});
