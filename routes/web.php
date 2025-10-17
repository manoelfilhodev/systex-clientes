<?php

use Illuminate\Support\Facades\Route;
use App\Services\InterBankService;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/inter/test', function (InterBankService $inter) {
    $hoje = now()->format('Y-m-d');
    return $inter->getExtrato($hoje, $hoje);
});
