<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Api\Auth\ConfirmController;
use App\Http\Controllers\Api\Auth\SigninWithEmailController;
use App\Http\Controllers\Api\Auth\SignupWithPhoneNumberController;
use App\Http\Controllers\Api\Auth\SecretLoginCodeController;
use App\Http\Controllers\Api\Auth\SigninWithPhoneNumberController;
use App\Http\Controllers\Api\Auth\EmailVerifyController;
use App\Http\Controllers\Api\Auth\SendSmsController;
use App\Http\Controllers\Api\Auth\DeleteUserController;
use App\Http\Controllers\Api\Auth\LineLoginCallbackController;
use App\Http\Controllers\Api\Auth\LineLoginUrlController;
use App\Http\Controllers\Api\Auth\SignupAttestationOptionsController;
use App\Http\Controllers\Api\Auth\SignupAssertionResultController;
use App\Http\Controllers\Api\Auth\SigninAttestationOptionsController;
use App\Http\Controllers\Api\Auth\SigninAssertionResultController;
use App\Http\Controllers\Api\Auth\LiffCryptoController;
use App\Http\Controllers\Api\Auth\SigninWithEmailPasswordLessController;
use App\Http\Controllers\Api\Auth\SignupWithEmailPassWordLessController;
use App\Http\Controllers\Api\Auth\XidLoginUrlController;
use App\Http\Controllers\Api\Callback\CallbackController;
use App\Http\Controllers\Api\ConfirmSigninController;
use App\Http\Controllers\Api\IsLogin\IsLoginController;
use App\Http\Controllers\Api\Library\SendEmailController;
//use App\Http\Controllers\Api\Line\LineMessageController;
use App\Http\Controllers\Api\Signin\SigninWithOidcController;
use App\Http\Controllers\Api\Signup\SignupWithEmailController;
use App\Http\Controllers\Api\Signup\SignupWithOidcController;
use App\Http\Controllers\Api\Url\AuthUrlController;
use App\Http\Controllers\Api\UserInfo\UserInfoController;

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

Route::group(['middleware' => ['api']], function () {
    Route::get('line-callback', [TestController::class, 'callback']);

    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function() {
        Route::get('test', [TestController::class, 'test']);
        Route::get('test/callback', [TestController::class, 'testCallback']);

        Route::get('auth-url', AuthUrlController::class);
        Route::get('auth-url/line', [LineLoginUrlController::class, 'index']);
        Route::get('auth-url/xid', XidLoginUrlController::class);
        Route::get('auth-url/xid-develop', XidLoginUrlController::class);
        Route::post('userInfo', UserInfoController::class);
        Route::get('islogin', IsLoginController::class);
        Route::get('callback', CallbackController::class);
        Route::get('callback/line', [LineLoginCallbackController::class, 'index']);
        Route::get('verify-email', [EmailVerifyController::class, 'index']);
        Route::post('sms', [SendSmsController::class, 'index']);
//        Route::post('line-message', LineMessageController::class);
        Route::post('delete-user', [DeleteUserController::class, 'index']);
        Route::post('send-email', SendEmailController::class);


        Route::group(['prefix' => 'signup', 'as' => 'signup.'], function() {
            Route::post('email', SignupWithEmailController::class);
            Route::post('email/less', SignupWithEmailPassWordLessController::class)->middleware('cookieHandler');
            Route::post('phone', SignupWithPhoneNumberController::class);
            Route::get('confirm', [ConfirmController::class, 'index']);
            Route::post('attestation/options', [SignupAttestationOptionsController::class, 'index']);
            Route::post('assertion/result', [SignupAssertionResultController::class, 'index']);
            Route::get('openid-connect', SignupWithOidcController::class);
        });

        Route::group(['prefix' => 'signin', 'as' => 'signin.'], function() {
            Route::post('email', SigninWithEmailController::class);
            Route::post('email/less', SigninWithEmailPasswordLessController::class);
            Route::post('phone', SigninWithPhoneNumberController::class);
            Route::get('confirm', ConfirmSigninController::class);
            Route::post('attestation/options', [SigninAttestationOptionsController::class, 'index']);
            Route::post('assertion/result', [SigninAssertionResultController::class, 'index']);
            Route::get('openid-connect', SigninWithOidcController::class);
        });

        Route::group(['prefix' => 'liff', 'as' => 'liff.'], function() {
            Route::post('/crypto', LiffCryptoController::class);
        });
    });
});
