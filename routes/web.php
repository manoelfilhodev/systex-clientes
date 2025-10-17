<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InterTestController;
use App\Http\Controllers\InterPixController;
use App\Http\Controllers\BancoInterWebhookController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/', function () {
    return view('welcome');
});

// 🚀 Rota de teste do Banco Inter (sandbox)
Route::get('/inter/teste', [InterTestController::class, 'pix']);


Route::get('/inter/teste', [InterPixController::class, 'teste'])->name('inter.teste');

Route::post('/webhook/inter', [BancoInterWebhookController::class, 'handle'])
    ->name('webhook.inter');

Route::get('/invoice/{id}/qr.png', function ($id) {
    $invoice = \App\Models\Invoice::findOrFail($id);
    $svg = Storage::get($invoice->pix_qr_code);
    return response(QrCode::format('png')->size(300)->generate($invoice->pix_copia_cola))
        ->header('Content-Type', 'image/png')
        ->setSharedMaxAge(3600);
})->name('invoice.qr.png');


