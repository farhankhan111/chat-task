<?php

use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [UserController::class, 'login']);
Route::post('/users', [UserController::class, 'index']);
Route::post('/sendSms', [SmsController::class, 'store']);

Route::post('/get-user-messages', [UserController::class, 'getUserMessages']);

