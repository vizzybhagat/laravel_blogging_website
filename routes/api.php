<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [UserController::class, 'loginApi']);
Route::post('/create-post', [PostController::class, 'storeNewPostApi'])->middleware('auth:sanctum');
Route::delete('/delete-post/{post}', [PostController::class, 'deleteApi'])->middleware('auth:sanctum', 'can:delete,post');

