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
Route::post('/login', [AuthController::class, 'Login']);
Route::patch('/logout', [AuthController::class, 'Logout'])->middleware('auth:sanctum');
Route::delete('/delete', [AuthController::class, 'destroy'])->middleware('auth:sanctum');



Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/', [ProductController::class, 'store'])->middleware(['auth:sanctum', 'SaffandAdmin']);
    Route::post('/{id}', [ProductController::class, 'uploadImage'])->middleware(['auth:sanctum', 'SaffandAdmin']);
    Route::put('/{id}', [ProductController::class, 'update'])->middleware(['auth:sanctum', 'SaffandAdmin']);
    Route::delete('/{id}', [ProductController::class, 'destroy'])->middleware(['auth:sanctum', 'SaffandAdmin']);
});

Route::prefix('size')->group(function () {
    Route::put('/{id}', [ProductController::class, 'updatesize']);
    Route::delete('/{id}', [ProductController::class, 'deleteSize']);
});

Route::prefix('filter')->group(function () {
    Route::get('/', [ProductController::class, 'searchProduct']);
    Route::get('/{category}', [ProductController::class, 'filterProduct']);
});

Route::prefix('cart')->group(function () {
    Route::post('/', [CartController::class, 'store']);
    Route::get('/{id}', [CartController::class, 'show']);
    Route::delete('/', [CartController::class, 'destroy']);
});

Route::prefix('todo')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [TodoController::class, 'store']);
    Route::get('/', [TodoController::class, 'show']);
    Route::put('/{id}', [TodoController::class, 'update']);
    Route::patch('/{id}', [TodoController::class, 'is_complete']);
    Route::delete('/{id}', [TodoController::class, 'destroy']);
});
