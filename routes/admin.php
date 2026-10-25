<?php

use App\\Http\\Controllers\\Admin\\RnbAudioTrackController;
use Illuminate\\Support\\Facades\\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/dashboard', '/admin')->name('dashboard');

    Route::prefix('admin')->group(function () {
        Route::redirect('archive', '/admin/posts')->name('admin.archive.index');

        Route::get('club-settings', [\\App\\Http\\Controllers\\Admin\\ClubSettingsController::class, 'edit'])->name('admin.club.edit');
        Route::put('club-settings', [\\App\\Http\\Controllers\\Admin\\ClubSettingsController::class, 'update'])->name('admin.club.update');

        Route::resource('audio-tracks', RnbAudioTrackController::class)
            ->except('show')->names('admin.rnb-audio');
    });
});
