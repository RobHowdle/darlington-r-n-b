<?php

use App\Http\Controllers\Admin\PageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::inertia('/', 'admin/Dashboard')->name('dashboard');

    Route::get('archive', [\App\Http\Controllers\Admin\ArchiveYearController::class, 'index'])->name('admin.archive.index');
    Route::get('archive/create', [\App\Http\Controllers\Admin\ArchiveYearController::class, 'create'])->name('admin.archive.create');
    Route::post('archive', [\App\Http\Controllers\Admin\ArchiveYearController::class, 'store'])->name('admin.archive.store');
    Route::get('archive/{archiveYear}/edit', [\App\Http\Controllers\Admin\ArchiveYearController::class, 'edit'])->name('admin.archive.edit');
    Route::put('archive/{archiveYear}', [\App\Http\Controllers\Admin\ArchiveYearController::class, 'update'])->name('admin.archive.update');
    Route::delete('archive/{archiveYear}', [\App\Http\Controllers\Admin\ArchiveYearController::class, 'destroy'])->name('admin.archive.destroy');
    Route::post('archive/{archiveYear}/restore', [\App\Http\Controllers\Admin\ArchiveYearController::class, 'restore'])->withTrashed()->name('admin.archive.restore');

    Route::get('club-settings', [\App\Http\Controllers\Admin\ClubSettingsController::class, 'edit'])->name('admin.club.edit');
    Route::put('club-settings', [\App\Http\Controllers\Admin\ClubSettingsController::class, 'update'])->name('admin.club.update');
    Route::get('pages', [PageController::class, 'index'])->name('admin.pages.index');
    Route::get('pages/create', [PageController::class, 'create'])->name('admin.pages.create');
    Route::post('pages', [PageController::class, 'store'])->name('admin.pages.store');
    Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('admin.pages.edit');
    Route::put('pages/{page}', [PageController::class, 'update'])->name('admin.pages.update');
    Route::get('pages/{page}/preview', [PageController::class, 'preview'])->name('admin.pages.preview');
});
