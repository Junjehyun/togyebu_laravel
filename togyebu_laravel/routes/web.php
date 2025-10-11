<?php

use App\Http\Controllers\AdminController;
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
    // 메인 화면 
    Route::get('/index', [MainController::class, 'index'])->name('index');
    // 차트 데이터 ajax
    Route::get('/chartData', [MainController::class, 'chartData'])->name('chartData');
});

// RecordController
Route::prefix('record')->name('record.')->group(function () {
    // 전체 기록 화면 
    Route::get('/history', [RecordController::class, 'history'])->name('history');
    // 신규 추가 화면
    Route::get('/add', [RecordController::class, 'add'])->name('add');
    // 신규등록 처리
    Route::post('/store', [RecordController::class, 'store'])->name('store');
    // 베팅 확정처리 ajax
    Route::post('/betConfirm', [RecordController::class, 'betConfirm'])->name('betConfirm');
    // 수정 화면
    Route::get('/edit/{id}', [RecordController::class, 'edit'])->name('edit');
    // 수정 처리
    Route::post('/update/{id}', [RecordController::class, 'update'])->name('update');
    // 삭제처리
    Route::post('/delete/{id}', [RecordController::class, 'delete'])->name('delete');
    // 입출금 내역기록 페이지 
    Route::get('/transaction', [RecordController::class, 'transaction'])->name('transaction');
    // 누적 수익 그래프 ajax
    Route::get('/chartData', [RecordController::class, 'chartData'])->name('chartData');
    // 폴더별 통계 ajax
    Route::get('/chartFolder', [RecordController::class, 'chartFolder'])->name('chartFolder');
});  

Route::prefix('admin')->name('admin.')->group(function () {
    // 개발일지 화면 
    Route::get('/devlog', [AdminController::class, 'devlog'])->name('devlog');
});