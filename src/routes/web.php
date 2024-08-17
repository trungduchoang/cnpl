<?php

use App\Http\Controllers\Api\Auth\XidCallbackController;
use App\Http\Controllers\Api\Callback\CallbackController;
use App\Http\Controllers\Auth\ConfirmController;
use App\Http\Controllers\Liff\LiffLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Liff\LiffRedirectController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/**
 * auth route
 */
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function() {
    
    Route::get('/test', [TestController::class, 'test']);
    Route::group(['prefix' => 'liff', 'as' => 'liff.'], function() {
        Route::get('/login', LiffLoginController::class);
        Route::get('/redirect', LiffRedirectController::class);
    });
    Route::get('callback', CallbackController::class);
    Route::get('callback/xid', XidCallbackController::class);


    
    Route::group(['prefix' => 'signin-form', 'as' => 'signin-form.'], function() {
        Route::get('/confirm', [ConfirmController::class, 'signinConfirmForm']);
    });

    Route::group(['prefix' => 'signup', 'as' => 'signup.'], function() {
        Route::post('/confirm', [ConfirmController::class, 'confirmSignup']);
    });

    Route::group(['prefix' => 'signin', 'as' => 'signin.'], function() {
        Route::post('/confirm', [ConfirmController::class, 'confirmSignin']);
    });
});
