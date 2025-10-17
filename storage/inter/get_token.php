<?php

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Carrega o .env da raiz do projeto
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$client_id     = $_ENV['INTER_CLIENT_ID'];
$client_secret = $_ENV['INTER_CLIENT_SECRET'];
$base_url      = $_ENV['INTER_BASE_URL'];
$cert_file     = base_path($_ENV['INTER_CERT_PATH']);
$key_file      = base_path($_ENV['INTER_KEY_PATH']);
$chain_file    = base_path($_ENV['INTER_CHAIN_PATH']);

// Endpoint do token
$url = "{$base_url}/oauth/v2/token";

// Dados do corpo
$data = [
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'scope' => 'pix.read pix.write',
    'grant_type' => 'client_credentials',
];

$headers = ['Content-Type: application/x-www-form-urlencoded'];

// Requisição cURL
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_SSLCERT => $cert_file,
    CURLOPT_SSLKEY => $key_file,
    CURLOPT_CAINFO => $chain_file,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_RETURNTRANSFER => true,
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    die("❌ Erro cURL: $error\n");
}

echo "📦 HTTP $http_code\n";
echo "🔑 Resposta:\n$response\n";
