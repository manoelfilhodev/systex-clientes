<?php

namespace App\Services;

use Exception;
use Inter\Sdk\sdkLibrary\InterSdk;
use Inter\Sdk\sdkLibrary\pix\immediatebillings\ImmediateBillingClient;
use Inter\Sdk\sdkLibrary\pix\models\PixBilling;
use Inter\Sdk\sdkLibrary\pix\models\Calendar;
use Inter\Sdk\sdkLibrary\pix\models\Debtor;
use Inter\Sdk\sdkLibrary\pix\models\PixValue;

class InterBankService
{
    protected $sdk;

    public function __construct()
    {
        $clientId     = env('INTER_CLIENT_ID');
        $clientSecret = env('INTER_CLIENT_SECRET');
        $env          = strtoupper(env('INTER_ENV', 'SANDBOX'));

        $pfxPath = storage_path('inter/Sandbox_InterAPI.pfx');

        if (!file_exists($pfxPath)) {
            throw new Exception("⚠️ Certificado PFX do Banco Inter não encontrado em storage/inter/");
        }

        // ✅ Instancia o SDK principal
        $this->sdk = new InterSdk(
            $env,
            $clientId,
            $clientSecret,
            $pfxPath,
            '' // senha do PFX (vazia no sandbox)
        );
    }

    public function criarCobrancaPix($valor, $nome, $cpf, $descricao = 'Pagamento Systex')
    {
        try {
            $config = $this->sdk->getConfig();
            $api = new ImmediateBillingClient();

            // 🔹 Formata e valida os dados
            $cpf = preg_replace('/\D/', '', $cpf);
            $txid = substr('SYS' . uniqid(), 0, 35);

            // 🔹 Cria os modelos conforme a SDK do Inter
            $calendar = new \Inter\Sdk\sdkLibrary\pix\models\Calendar(3600);
            $debtor   = new \Inter\Sdk\sdkLibrary\pix\models\Debtor($cpf, $nome);
            $value    = new \Inter\Sdk\sdkLibrary\pix\models\PixValue(number_format((float)$valor, 2, '.', ''));

            // 🔹 Monta cobrança Pix
            $billing = new \Inter\Sdk\sdkLibrary\pix\models\PixBilling();
            $billing->setTxid($txid);
            $billing->setCalendar($calendar);
            $billing->setDebtor($debtor);
            $billing->setValue($value);
            $billing->setKey(env('INTER_PIX_KEY')); // ⚠️ Use sua chave real
            $billing->setPayerRequest(substr($descricao, 0, 140));

            // 🔹 Depuração opcional — veja o JSON real
            // dd(json_decode($billing->toJson(), true));

            // 🔹 Envia requisição
            // $response = $api->includeImmediateBilling($config, $billing);

            dd($billing->toJson());


            return [
                'success' => true,
                'txid' => $txid,
                'data' => $response,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
