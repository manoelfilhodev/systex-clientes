<?php

namespace App\Http\Controllers;

use App\Services\InterBankService;

class InterTestController extends Controller
{
    public function pix()
    {
        $inter = new InterBankService();
        $res = $inter->criarCobrancaPix(10.00, 'Cliente Teste', '12345678909', 'Cobrança de Teste');
        return response()->json($res);
    }
}
