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

Route::post('/login', ['as' => 'login', 'uses' => 'Api\WalletController@login']);

Route::group(['middleware' => ['auth:api','log-activity']], function() {
    Route::group(['prefix' => 'hcs', 'as' => 'hcs.'], function() {
        Route::get('/mywallet', ['as' => 'myWallet', 'uses' => 'Api\WalletController@wallet']);
        Route::post('/send', ['as' => 'send', 'uses' => 'Api\WalletController@send']);
        Route::get('/history', ['as' => 'history', 'uses' => 'Api\WalletController@history']);
    });
});
