<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\PassportAuthController;
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



Route::post('register', [PassportAuthController::class, 'register'])->name('register');
Route::post('login', [PassportAuthController::class, 'login'])->name('login');

Route::group( ['middleware' => [] ],function(){
    Route::post('/get', [ItemController::class, 'index']);
    Route::post('/create', [ItemController::class, 'store']);
    Route::post('/edit/{id}', [ItemController::class, 'update']);
    Route::delete('/delete/{id}', [ItemController::class, 'destroy']);
    Route::get('/status/{id}', [ItemController::class, 'updateStatus']);
    Route::post('/order', [ItemController::class, 'order']);
});