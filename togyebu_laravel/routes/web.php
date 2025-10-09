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
    Route::post('/store', [RecordController::class, 'store'])->name('store');
    // 베팅 확정처리 ajax
    Route::post('/betConfirm', [RecordController::class, 'betConfirm'])->name('betConfirm');
    Route::get('/edit/{id}', [RecordController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [RecordController::class, 'update'])->name('update');
    // 삭제처리
    Route::post('/delete/{id}', [RecordController::class, 'delete'])->name('delete');
    // 입출금 내역기록 페이지 
    Route::get('/transaction', [RecordController::class, 'transaction'])->name('transaction');
});  