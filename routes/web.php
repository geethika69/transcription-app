<?php

use App\Http\Controllers\TranscriptionFileController;
use App\Http\Controllers\SegmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TranscriptionFileController::class, 'index'])->name('files.index');
Route::post('/files', [TranscriptionFileController::class, 'store'])->name('files.store');
Route::get('/files/{file}', [TranscriptionFileController::class, 'show'])->name('files.show');
Route::post('/files/{file}/segments', [SegmentController::class, 'store'])->name('segments.store');
Route::delete('/files/{file}/segments/{segment}', [SegmentController::class, 'destroy'])->name('segments.destroy');