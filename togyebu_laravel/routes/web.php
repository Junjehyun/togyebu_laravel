<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\RecordController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// MainController
Route::prefix('main')->name('main.')->group(function () {
    Route::get('/index', [MainController::class, 'index'])->name('index');
});

// RecordController
Route::prefix('record')->name('record.')->group(function () {
    Route::get('/history', [RecordController::class, 'history'])->name('history');
    Route::get('/add', [RecordController::class, 'add'])->name('add');
    Route::post('/addStore', [RecordController::class, 'addStore'])->name('addStore');
    // 베팅 확정처리 ajax
    Route::post('/betConfirm', [RecordController::class, 'betConfirm'])->name('betConfirm');
});  