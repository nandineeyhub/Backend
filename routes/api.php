<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Route::get('/student',function(){
//     return "Laravel API";
// });

// Route::get('/students',[StudentController::class, 'index']);

// Route::get('/students/{id}',[StudentController::class, 'show']);

// Route::put('/students/{id}',[StudentController::class, 'update']);

// Route::post('/students',[StudentController::class, 'store']);

// Route::get('/students/search/{city}',[StudentController::class, 'search']);

// Route::delete('/students/{id}',[StudentController::class, 'destroy']);

Route::post('/register',[UserController::class,'register']);

Route::post('/login',[UserController::class,'login']);

Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/students',[StudentController::class, 'index']);

    Route::get('/students/{id}',[StudentController::class, 'show']);

    Route::put('/students/{id}',[StudentController::class, 'update']);

    Route::post('/students',[StudentController::class, 'store']);

    Route::get('/students/search/{city}',[StudentController::class, 'search']);

    Route::delete('/students/{id}',[StudentController::class, 'destroy']);

    Route::post('/logout',[UserController::class, 'logout']);

    Route::get('/loggeduser',[UserController::class, 'loggedUser']);

    Route::post('/changepassword',[UserController::class, 'changePassword']);
});