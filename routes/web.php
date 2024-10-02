<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;


Route::get('/', function(){
    return redirect('/login');
});

Route::get('/home', function(){
    return redirect('/visitors');
});

Route::get('/visitors', [VisitorController::class, 'index'])->name('visitor.index'); // Route untuk menampilkan daftar pengunjung
Route::get('/visitors/create', [VisitorController::class, 'create'])->name('visitor.create'); // Route untuk menampilkan form pendaftaran pengunjung
Route::post('/visitors', [VisitorController::class, 'store'])->name('visitor.store'); // Route untuk menyimpan data pengunjung
Route::get('/visitors/{id}', [VisitorController::class, 'show'])->name('visitor.show'); // Route untuk menampilkan detail pengunjung
Route::get('/visitors/{id}/qr', [VisitorController::class, 'showQr'])->name('visitor.showQr'); // Route untuk menampilkan QR code pengunjung
Route::get('/visitor/undangan/{id}', [VisitorController::class, 'invitation'])->name('visitor.undangan');
Route::get('/visitor/scan', [VisitorController::class, 'showScanPage'])->name('visitor.scan');
Route::post('/check-in', [VisitorController::class, 'checkIn']);

// session
Route::get('session/{name}', [VisitorController::class, 'session'])->name('session');

Route::get('visitor/{id}/download-qr-code', [VisitorController::class, 'downloadQrCode'])->name('visitor.downloadQrCode');
Route::get('/scan', [VisitorController::class, 'showScanPage'])->name('visitor.scan');


Route::get('/visitor/{id}/download-invitation', [VisitorController::class, 'downloadInvitation'])->name('visitor.downloadInvitation');
Route::get('/visitor/{id}/download-pdf', [VisitorController::class, 'downloadPDF'])->name('visitor.downloadPDF');
Route::get('/visitor/{id}/download-qrcode', [VisitorController::class, 'downloadQRCode'])->name('visitor.downloadQRCode');
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
