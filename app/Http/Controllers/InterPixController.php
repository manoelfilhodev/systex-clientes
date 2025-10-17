<?php

namespace App\Http\Controllers;

use App\Services\InterPixService;
use Illuminate\Http\Request;

class InterPixController extends Controller
{
    public function teste()
    {
        $pix = new InterPixService();

        try {
            $res = $pix->gerarCobrancaPixComQrCode(
                10.00,
                '12345678909',
                'Manoel Filho',
                'Cobrança de Teste'
            );

            return view('inter.pix', [
                'txid' => $res['txid'] ?? '',
                'valor' => number_format($res['valor'], 2, ',', '.'),
                'descricao' => $res['descricao'] ?? '',
                'imagem' => $res['imagem'] ?? '',
                'qrcode' => $res['qrcode'] ?? '',
                'raw_resposta' => $res['raw_resposta'] ?? [],
            ]);
        } catch (\Exception $e) {
            return view('inter.pix', ['erro' => $e->getMessage()]);
        }
    }
}
