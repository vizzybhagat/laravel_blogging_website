<?php

use App\Events\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;

Route::get('/', [UserController::class, 'showCorrectHomepage'])->name('login');
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('mustBeLoggedIn');
Route::get('/manage-avatar',[UserController::class, 'showAvatarForm'])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar',[UserController::class, 'storeAvatar'])->middleware('mustBeLoggedIn');

//follow related routes
Route::post('/create-follow/{user:username}',[FollowController::class,'createFollow'])->middleware('mustBeLoggedIn');
Route::post('/remove-follow/{user:username}',[FollowController::class,'removeFollow'])->middleware('mustBeLoggedIn');

//Blog post related routes

Route::get('/create-post',[PostController::class,'showCreateForm'])->middleware('mustBeLoggedIn');
Route::post('/create-post',[PostController::class,'storeNewPost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}',[PostController::class,'viewSinglePost']);

Route::delete('/post/{post}',[PostController::class,'delete'])->middleware('can:delete,post');
Route::get('/post/{post}/edit',[PostController::class,'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}',[PostController::class,'actuallyUpdate'])->middleware('can:update,post');

Route::get('/search/{term}',[PostController::class,'search']);


//Profile related routes

Route::get('/profile/{pizza:username}', [UserController::class, 'profile']);
Route::get('/profile/{pizza:username}/followers',[UserController::class,'profileFollowers']);
Route::get('/profile/{pizza:username}/following',[UserController::class,'profileFollowing']);

//Channel routes
Route::post('/send-chat-message', function (Request $request) {
    $formFields = $request->validate([
      'textvalue' => 'required'
    ]);
  
    if (!trim(strip_tags($formFields['textvalue']))) {
      return response()->noContent();
    }
  
    broadcast(new ChatMessage(['username' =>auth()->user()->username, 'textvalue' => strip_tags($request->textvalue), 'avatar' => auth()->user()->avatar]))->toOthers();
    return response()->noContent();
  
  })->middleware('mustBeLoggedIn');

  