<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TodoController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'Register']);
Route::post('/login', [AuthController::class,'Login']);
Route::delete('/logout', [AuthController::class, 'Logout'])->middleware('auth:sanctum');
Route::delete('/delete/{id}', [AuthController::class,'destroy']);



Route::prefix('product')->group(function () {
    Route::get('/',[ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::post('/', [ProductController::class, 'store']);
    Route::post('/{id}', [ProductController::class, 'uploadImage']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    
}); 

Route::prefix('size')->group(function (){
    Route::put('/{id}', [ProductController::class, 'updatesize']);
    Route::delete('/{id}', [ProductController::class, 'deleteSize']);
});

Route::prefix('filter')->group(function () {
Route::get('/', [ProductController::class, 'searchProduct']);
Route::get('/{category}', [ProductController::class,'filterProduct']);
});

Route::prefix('cart')->group(function (){
Route::post('/', [CartController::class, 'store']);
Route::get('/{id}', [CartController::class, 'show']);
Route::delete('/', [CartController::class, 'destroy']);
});

Route::prefix('todo')->group(function () {
    Route::post('/', [TodoController::class, 'store']);
    Route::get('/{id}', [TodoController::class, 'show']);
    Route::put('/{id}', [TodoController::class, 'update']);
    Route::patch('/{id}', [TodoController::class, 'is_complete']);
    Route::delete('/{id}', [TodoController::class, 'destroy']);
});
