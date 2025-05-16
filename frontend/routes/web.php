<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhoneNumberController;

Route::get('/', [PhoneNumberController::class, 'index'])->name('phone.index');
Route::post('/generate-and-validate', [PhoneNumberController::class, 'generateAndValidate'])->name('phone.generateAndValidate');

