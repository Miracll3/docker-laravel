<?php

use App\Http\Controllers\PhoneNumberValidationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/validate-phone-numbers', [PhoneNumberValidationController::class, 'validatePhoneNumbers']);

