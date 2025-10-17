<?php

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Carrega .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$token = 'SEU_TOKEN_AQUI'; // cole o access_token do get_token.php
$base_url = env('INTER_BASE_URL');
$cert_path = base_path(env('INTER_CERT_PATH'));
$key_path = base_path(env('INTER_KEY_PATH'));
$chain_path = base_path(env('INTER_CHAIN_PATH'));

// Gera TXID único
$txid = 'TESTE' . uniqid();

$data = [
    "calendario" => ["expiracao" => 3600],
    "devedor" => [
        "cpf" => "12345678909",
        "nome" => "Cliente Teste"
    ],
    "valor" => ["original" => "10.00"],
    "chave" => "SUA_CHAVE_PIX_SANDBOX",
    "solicitacaoPagador" => "Pagamento teste Systex CRM"
];

$url = "$base_url/pix/v2/cob/$txid";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_SSLCERT => $cert_path,
    CURLOPT_SSLKEY => $key_path,
    CURLOPT_CAINFO => $chain_path,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ],
    CURLOPT_RETURNTRANSFER => true,
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Erro cURL: $error\n";
} else {
    echo "📦 HTTP $http_code\n";
    echo "💬 Resposta:\n$response\n";
}
