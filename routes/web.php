<?php

use App\Http\Controllers\WarisanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerumahanController;
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\PenggunaController;

Route::get('/', function () {
    return view('welcome');
});


// Admin auth

Route::post('/auth/admin/login', [AdminsController::class, 'login'])->name('admin.login');
Route::post('/auth/admin/logout', [AdminsController::class, 'logout'])->name('admin.logout');
Route::post('/auth/admin/register', [AdminsController::class, 'register'])->name('admin.register');

// User auth
Route::post('/auth/user/register', [PenggunaController::class, 'register'])->name('user.register');
Route::post('/auth/user/login', [PenggunaController::class, 'login'])->name('user.login');
Route::post('/auth/user/logout', [PenggunaController::class, 'logout'])->name('user.logout');

// route perumahan
Route::post('/api/perumahan/create', [PerumahanController::class, 'create'])->name('perumahan.create');
Route::get('/api/perumahan/show', [PerumahanController::class, 'index'])->name('perumahan.index');
Route::put('/api/perumahan/update/{id}', [PerumahanController::class, 'update'])->name('perumahan.update');
Route::get('/api/perumahan/show/{id}', [PerumahanController::class, 'show'])->name('perumahan.show');
Route::delete('/api/perumahan/delete/{id}', [PerumahanController::class, 'delete'])->name('perumahan.delete');

// route warisan
Route::post('/api/warisan/create', [WarisanController::class, 'create'])->name('warisan.create');
Route::get('/api/warisan/show', [WarisanController::class, 'index'])->name('warisan.index');
Route::get('/api/warisan/show/{id}', [WarisanController::class, 'show'])->name('warisan.show');
Route::put('/api/warisan/update/{id}', [WarisanController::class, 'update'])->name('warisan.update');
Route::delete('/api/warisan/delete/{id}', [WarisanController::class, 'delete'])->name('warisan.delete');
