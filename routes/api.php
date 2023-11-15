<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;



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
//==========================================Staff Routes==================================================================//
Route::post('/staff/auth/register', [\App\Http\Controllers\Admin\Auth\AuthController::class, 'createStaff']);
Route::post('/staff/auth/login', [\App\Http\Controllers\Admin\Auth\AuthController::class, 'login']);
Route::post('/staff/auth/logout', [\App\Http\Controllers\Admin\Auth\AuthController::class, 'logout']);

//email verification
//Route::get('/staff/email/verify/{id}/{hash}', [\App\Http\Controllers\Admin\Auth\AuthController::class, 'verifyEmail'])->name('verification.verify');
//Route::post('/staff/email/verification-notification', [\App\Http\Controllers\Admin\Auth\AuthController::class, 'resendVerification'])->middleware(['auth:sanctum','abilities:admin', 'throttle:6,1'])->name('verification.send');


Route::middleware(['auth:sanctum','abilities:admin'])->get('/staff/user', [\App\Http\Controllers\Admin\Auth\AuthController::class, 'getCurrentUser']);


//==========================================Customer Routes==================================================================//
Route::post('/customer/auth/register', [\App\Http\Controllers\Customer\Auth\AuthController::class, 'createCustomer']);
Route::post('/customer/auth/login', [\App\Http\Controllers\Customer\Auth\AuthController::class, 'login']);
Route::post('/customer/auth/logout', [\App\Http\Controllers\Customer\Auth\AuthController::class, 'logout']);

Route::get('/customer/email/verify/{id}/{hash}', [\App\Http\Controllers\Customer\Auth\AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/customer/email/verification-notification', [\App\Http\Controllers\Customer\Auth\AuthController::class, 'resendVerification'])->middleware(['auth:sanctum','abilities:customer', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth:sanctum','abilities:customer'])->get('/customer/user', [\App\Http\Controllers\Customer\Auth\AuthController::class, 'getCurrentUser']);
