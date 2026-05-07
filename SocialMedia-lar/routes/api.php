<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'Logout']);
});
Route::middleware('auth:sanctum')->group(function (){
    Route::post('/posts/create',[MainController::class,'CreatePost']);
    Route::get('/GetPosts',[MainController::class,'GetPosts']);
    Route::get('GetPost/{id}',[MainController::class,'GetPost']);
    Route::get('/MyPosts',[MainController::class,'MyPosts']);
    Route::post('/LikePost/{user}/{post}',[MainController::class,'LikePost']);
    Route::get('/getLikes/{post}',[MainController::class,'GetLikes']);
    Route::post('/Comment/{user}/{post}',[MainController::class,'Comment']);
    Route::get('/GetComments/{post}',[MainController::class,'GetComments']);
    Route::post('/Follow/{user}',[MainController::class,'Follow']);
});
Route::middleware(['auth:sanctum','ifMe'])->group(function(){
    Route::put('/UpdateProfile/{id}',[MainController::class,'UpdateProfile']);
    Route::delete('/DeletePost/{id}',[MainController::class,'DeletePost']);
    Route::get('/GetFollowRequests',[MainController::class,'GetFollowRequests']);
    Route::put('/AcceptFollow/{id}',[MainController::class,'AcceptFollow']);
    Route::put('/RejectFollow/{id}',[MainController::class,'RejectFollow']);
    Route::get('/GetFollowers',[MainController::class,'GetFollowers']);
    Route::get('/GetFollowings',[MainController::class,'GetFollowings']);
    Route::delete('/Unfollow/{id}',[MainController::class,'Unfollow']);
});
