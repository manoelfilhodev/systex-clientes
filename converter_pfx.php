<?php

// Caminhos dos arquivos
$basePath = __DIR__ . '/storage/inter/';
$crtPath  = $basePath . 'Sandbox_InterAPI_Certificado.crt';
$keyPath  = $basePath . 'Sandbox_InterAPI_Chave.key';
$pfxPath  = $basePath . 'Sandbox_InterAPI.pfx';

// Verifica se os arquivos existem
if (!file_exists($crtPath) || !file_exists($keyPath)) {
    die("❌ Certificado (.crt) ou chave (.key) não encontrados em $basePath\n");
}

// Lê o conteúdo
$cert = file_get_contents($crtPath);
$pkey = file_get_contents($keyPath);

// Prepara o container
$pfxContent = null;
$password = ''; // Sem senha (pode preencher se quiser proteger o arquivo)

// Cria o PFX direto em memória
$ok = openssl_pkcs12_export_to_file(
    $cert,              // Certificado público
    $pfxPath,           // Caminho de saída
    $pkey,              // Chave privada
    $password           // Senha
);

if ($ok) {
    echo "✅ Arquivo PFX criado com sucesso:\n$pfxPath\n";
} else {
    echo "❌ Falha ao criar o arquivo PFX.\n";
    print_r(error_get_last());
}
