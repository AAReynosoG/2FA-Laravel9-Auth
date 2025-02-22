<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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



/** Routes with 'internal.redirect' middleware, which ensures the session contains the 'internal_redirect' flag before proceeding.
 *  These routes are also protected with the header settings to prevent caching.
*/
Route::middleware('internal.redirect')->group(function () {

    Route::get('/enable/2fa', function () {
        return response()
            ->view('auth.enable2fa')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    })->name('enable.2fa');

    Route::get('/email_sent_notice', function() {
        return response()
            ->view('notices.email_sent_notice')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    })->name('email.notice');

    Route::get('/totp/validation', function () {
        return response()
            ->view('auth.validate_authcode')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    })->name('totp.validation');

    Route::get('/totp/link', function () {
        return response()
            ->view('auth.link_google_authenticator')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    })->name('totp.link');
});


/**
 *  Routes protected by 'auth' middleware, ensuring the user is authenticated.
 */
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


/**
 *  Routes for guests (unauthenticated users).
 */
Route::middleware('guest')->group(function () {

    // Redirect guest users to sign-in page
    Route::get('/', function () {
        return redirect('/sign_in');
    })->name('login');

    Route::get('/sign_in', function () {
        return view('auth.login');
    })->name('auth.signin');

    Route::get('/sign_up', function () {
        return view('auth.register');
    })->name('auth.signup');

    Route::get('/resend/verification_email', function (){
        return view('auth.resend_verification_email');
    })->name('resend_email.view');
});


/**
 *  Routes for guest users with '/user' prefix,
 * such as account verification and user registration.
 */
Route::middleware('guest')->prefix('/user')->group(function () {

    Route::get('/verify-email', [UserController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/auth', [AuthController::class, 'authenticate'])->name('user.authenticate');
    Route::post('/store', [UserController::class, 'store'])->name('user.store');
    Route::post('/validate/data', [AuthController::class, 'validateUserData'])->name('user.validate');
    Route::post('/link-google-authenticator', [AuthController::class, 'linkGoogleAuthenticator'])->name('user.link.2fa');
    Route::post('/resend/verification_email', [UserController::class, 'resendVerificationEmail'])->name('user.resend');

});

