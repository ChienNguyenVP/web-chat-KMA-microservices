<?php

use App\Model\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Auth;

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

Route::namespace('Api')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::group([
            'prefix' => 'auth'
        ], function () {
            Route::post('login', 'AuthController@login');
            Route::post('signup', 'AuthController@signup');
            Route::group(['middleware' => ['web']], function () {
                Route::get('login/{provider}', 'AuthController@redirectToProvider');
                Route::get('login/{provider}/callback', 'AuthController@handleProviderCallback');
            });

            Route::group([
                'middleware' => 'auth:api'
            ], function () {
                Route::get('logout', 'AuthController@logout');
                Route::get('user', 'AuthController@user');
                Route::get('email/verify', 'VerificationController@resend')->name('verification.resend');
                Route::post('editUser', 'AuthController@edit');
                Route::post('avatar', 'AuthController@avatar');
                Route::get('test', 'AuthController@test');
                Route::post('changepassword', 'AuthController@changePassword');
            });
            Route::get('getall/{userId}', 'ContactController@getAll');
            Route::post('searchUser', 'ContactController@searchUser');
            Route::get('/email/verify/{id}/{hash}', 'AuthController@verify')->name('verification.verify');
        });
    });
    
    Route::namespace('Conversation')->group(function () {
        Route::group(['middleware' => ['auth:api']], function () {
        Route::group([
            'prefix' => 'conversation'
        ], function () {
                Route::get('conversations', 'ConversationController@getConversations')->name('conversation');
                Route::post('getToken', 'ConversationController@getToken');       
            });        
        });
    });
});

