<?php

use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\Public\RnbController::class, 'home'])->name('home');
Route::get('/history', [\App\Http\Controllers\Public\RnbController::class, 'history'])->name('rnb.history');
Route::get('/archive', [\App\Http\Controllers\Public\RnbController::class, 'archive'])->name('rnb.archive');
Route::view('/audio', 'rnb.audio')->name('rnb.audio');

Route::redirect('/history.php', '/history', 301);
Route::redirect('/archive.php', '/archive', 301);
Route::redirect('/audio.php', '/audio', 301);

Route::any('{fallbackPlaceholder}', [PageController::class, 'show'])
    ->where('fallbackPlaceholder', '.*')
    ->fallback()
    ->name('public.page');
