<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminsController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AdminsController::class, 'register']);