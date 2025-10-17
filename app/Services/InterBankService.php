<?php

namespace App\Services;

use Inter\Sdk\sdkLibrary\InterSdk;
use Exception;

class InterBankService
{
    protected $sdk;

    public function __construct()
    {
        $certPath = storage_path('inter/Sandbox_InterAPI_Certificado.crt');
        $keyPath  = storage_path('inter/Sandbox_InterAPI_Chave.key');

        if (!file_exists($certPath) || !file_exists($keyPath)) {
            throw new Exception("Certificados do Banco Inter não encontrados em storage/inter/");
        }

        // ✅ O SDK espera o nome do ambiente em maiúsculas: "SANDBOX" ou "PRODUCTION"
        $this->sdk = new InterSdk(
            'SANDBOX',
            env('INTER_CLIENT_ID'),
            env('INTER_CLIENT_SECRET'),
            storage_path('inter/Sandbox_InterAPI.pfx'),
            '' // senha vazia
        );
    }

    public function getExtrato()
    {
        return [
            "dataInicio" => "2025-10-17",
            "dataFim" => "2025-10-17",
            "saldoInicial" => 1000,
            "saldoFinal" => 1885,
            "transacoes" => [
                ["data" => "2025-10-17", "descricao" => "PIX CLIENTE TESTE", "valor" => 500, "tipo" => "CREDITO"],
                ["data" => "2025-10-17", "descricao" => "TARIFA MANUTENÇÃO", "valor" => -15, "tipo" => "DEBITO"],
                ["data" => "2025-10-17", "descricao" => "PAGAMENTO CLIENTE X", "valor" => 600, "tipo" => "CREDITO"],
            ],
        ];
    }
}
