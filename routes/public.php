<?php

use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\RnbController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RnbController::class, 'home'])->name('home');
Route::get('/history', [RnbController::class, 'history'])->name('rnb.history');
Route::get('/archive', [RnbController::class, 'archive'])->name('rnb.archive');
Route::get('/audio', [RnbController::class, 'audio'])->name('rnb.audio');

Route::redirect('/history.php', '/history', 301);
Route::redirect('/archive.php', '/archive', 301);
Route::redirect('/audio.php', '/audio', 301);

Route::any('{fallbackPlaceholder}', [PageController::class, 'show'])
    ->where('fallbackPlaceholder', '.*')
    ->fallback()
    ->name('public.page');
