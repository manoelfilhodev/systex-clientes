<?php

$clientId = "ef6d7a74-851f-474c-a17c-562c9fca4ebb";
$clientSecret = "4402c6c0-5c54-4f01-80ae-e3b9f96ad553";
$pfxPath = __DIR__ . "/Sandbox_InterAPI_Certificado.pfx";
$conta = "123456789"; // número da conta sandbox

echo "🔐 Iniciando autenticação...\n";

// === 1️⃣ Gerar token OAuth2 ===
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://cdpj-sandbox.partners.uatinter.co/oauth/v2/token",
    CURLOPT_POST => true,
    CURLOPT_SSLCERTTYPE => "P12",
    CURLOPT_SSLCERT => $pfxPath,
    CURLOPT_SSLCERTPASSWD => "", // senha vazia
    CURLOPT_POSTFIELDS => http_build_query([
        "client_id" => $clientId,
        "client_secret" => $clientSecret,
        "scope" => "boleto-cobranca.read boleto-cobranca.write",
        "grant_type" => "client_credentials",
    ]),
    CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
    CURLOPT_RETURNTRANSFER => true,
]);

$response = curl_exec($ch);
if (!$response) die("❌ Erro ao obter token: " . curl_error($ch));
curl_close($ch);

$data = json_decode($response, true);
if (empty($data['access_token'])) die("❌ Erro ao autenticar: " . $response);

$token = $data['access_token'];
echo "✅ Token gerado com sucesso!\n";

// === 2️⃣ Criar cobrança (boleto) ===
$payload = json_encode([
    "seuNumero" => "SYS" . rand(10000, 99999),
    "valorNominal" => 10.00,
    "dataVencimento" => "2025-10-22",
    "numDiasAgenda" => 30,
    "geraPix" => true,
    "pix" => [
        "chave" => "seuemail@inter.com.br"
    ],
    "atualizarPagador" => false,
    "pagador" => [
        "cpfCnpj" => "12345678909",
        "tipoPessoa" => "FISICA",
        "nome" => "Cliente Teste",
        "email" => "teste@systex.com.br",
        "endereco" => "Rua Exemplo",
        "cidade" => "Jundiaí",
        "uf" => "SP",
        "cep" => "13200000",
    ],
    "mensagem" => [
        "linha1" => "Cobrança de teste via Sandbox (com PIX)",
    ],
]);


echo "💸 Enviando cobrança para Banco Inter...\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://cdpj-sandbox.partners.uatinter.co/cobranca/v3/cobrancas",
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_SSLCERTTYPE => "P12",
    CURLOPT_SSLCERT => $pfxPath,
    CURLOPT_SSLCERTPASSWD => "",
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "x-inter-conta-corrente: $conta",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_RETURNTRANSFER => true,
]);

$result = curl_exec($ch);
if (!$result) die("❌ Erro ao gerar boleto: " . curl_error($ch));
curl_close($ch);

echo "📄 Resposta Inter (Criação):\n$result\n";

$data = json_decode($result, true);

// === 3️⃣ Consultar detalhes da cobrança ===
if (!empty($data['codigoSolicitacao'])) {
    $codigo = $data['codigoSolicitacao'];
    $consultaUrl = "https://cdpj-sandbox.partners.uatinter.co/cobranca/v3/cobrancas/" . $codigo;

    echo "\n🔍 Consultando detalhes da cobrança [$codigo]...\n";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $consultaUrl,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSLCERTTYPE => "P12",
        CURLOPT_SSLCERT => $pfxPath,
        CURLOPT_SSLCERTPASSWD => "",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "x-inter-conta-corrente: $conta",
        ],
        CURLOPT_RETURNTRANSFER => true,
    ]);

    $consulta = curl_exec($ch);
    if (!$consulta) die("❌ Erro ao consultar cobrança: " . curl_error($ch));
    curl_close($ch);

    echo "📦 Detalhes da cobrança:\n$consulta\n";
} else {
    echo "⚠️ Nenhum código de solicitação retornado.\n";
}

echo "✅ Processo concluído.\n";
