<?php

use App\Http\Controllers\GuestController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\WeddingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Wedding');
});
Route::get('/api/guests/{code}', [GuestController::class, 'show']);
Route::post('/rsvp/{code}', [GuestController::class, 'updateRsvp']);
Route::get('/', [WeddingController::class, 'index'])->name('wedding.index');

// Grouping biar rapi, nanti bisa lo kasih middleware 'auth' kalau mau diproteksi
Route::prefix('admin')->group(function () {
    // Halaman List Tamu
    Route::get('/guests', [GuestController::class, 'index'])->name('admin.guests.index');
    
    // Action Import Excel
    Route::post('/guests/import', [GuestController::class, 'import'])->name('admin.guests.import');
    Route::delete('/guests/{id}', [GuestController::class, 'destroy'])->name('admin.guests.destroy');
    Route::delete('/guests-truncate', [GuestController::class, 'truncate'])->name('admin.guests.truncate');

    Route::get('/gallery', [GalleryController::class, 'index'])->name('admin.gallery.index');
    Route::post('/gallery', [GalleryController::class, 'store'])->name('admin.gallery.store');
    Route::delete('/gallery/{id}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');
   

Route::get('/guests/print-labels', [GuestController::class, 'printLabels'])->name('admin.guests.print');
});