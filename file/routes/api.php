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
Route::namespace('Api')->middleware(['auth:external'])->group(function () {
    Route::group([
        'prefix' => 'file'
    ], function () {
        Route::post('upload', 'FileController@upload');
        Route::get('fileinfo/{id}', 'FileController@fileInfo');
        Route::post('getfile', 'FileController@getFileByConversation');
    });
});