<?php

namespace App\Services;

use Exception;

class InterPixService
{
    /**
     * Gera uma cobrança PIX dinâmica e retorna o QR Code completo.
     */
    public function gerarCobrancaPixComQrCode($valor, $cpf, $nome, $descricao = 'Serviço realizado.')
    {
        $baseUrl  = env('INTER_BASE_URL', 'https://cdpj-sandbox.partners.uatinter.co');
        $clientId = env('INTER_CLIENT_ID');
        $clientSecret = env('INTER_CLIENT_SECRET');
        $pfxPath = base_path(env('INTER_PFX_PATH', 'storage/inter/Sandbox_InterAPI_Certificado.pfx'));
        $pixKey  = env('INTER_PIX_KEY', 'seuemail@inter.com.br');

        // === 1. Obter Token ===
        $tokenUrl = rtrim($baseUrl, '/') . '/oauth/v2/token';
        $postFields = http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'cob.write',
        ]);

        $tokenResponse = $this->curlRequestWithCert(
            $tokenUrl,
            'POST',
            ['Content-Type: application/x-www-form-urlencoded'],
            $postFields,
            $pfxPath
        );

        $tokenData = json_decode($tokenResponse['body'], true);
        $token = $tokenData['access_token'] ?? null;

        if (!$token) {
            throw new Exception("Falha ao obter token Inter: " . ($tokenResponse['body'] ?? 'Resposta vazia'));
        }

        // === 2. Criar cobrança PIX ===
        $txid = 'SYS' . substr(uniqid(), 0, 28);
        $payload = [
            "calendario" => ["expiracao" => 3600],
            "devedor" => [
                "cpf" => $cpf,
                "nome" => $nome,
            ],
            "valor" => [
                "original" => number_format($valor, 2, '.', ''),
                "modalidadeAlteracao" => 1,
            ],
            "chave" => $pixKey,
            "solicitacaoPagador" => $descricao,
        ];

        $cobUrl = rtrim($baseUrl, '/') . '/pix/v2/cob';
        $headers = [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json",
        ];

        $cobResponse = $this->curlRequestWithCert(
            $cobUrl,
            'POST',
            $headers,
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $pfxPath
        );

        $cobData = json_decode($cobResponse['body'], true);
        if (empty($cobData['loc']['id'])) {
            throw new Exception("Erro ao criar cobrança: " . json_encode($cobData));
        }

        // === 3. Obter QR Code ===
        $qrcodeUrl = "{$baseUrl}/pix/v2/loc/{$cobData['loc']['id']}/qrcode";

        $qrcodeResponse = $this->curlRequestWithCert(
            $qrcodeUrl,
            'GET',
            [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json",
            ],
            null,
            $pfxPath
        );

        $qrcodeData = json_decode($qrcodeResponse['body'], true);

        // === 4. Retornar resultado final ===
        return [
            "txid" => $cobData['txid'] ?? null,
            "valor" => $valor,
            "descricao" => $descricao,
            "qrcode" => $qrcodeData['qrcode'] ?? null,
            "imagem" => $qrcodeData['imagemQrcode'] ?? null,
            "raw_resposta" => $cobData,
        ];
    }

    /**
     * Executa requisições CURL com certificado PFX.
     */
    private function curlRequestWithCert($url, $method, $headers, $body, $pfxPath)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_SSLCERTTYPE => 'P12',
            CURLOPT_SSLCERT => $pfxPath,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        $httpCode = $info['http_code'] ?? 0;

        \Log::info('INTER CURL DEBUG', [
            'url' => $url,
            'http_code' => $httpCode,
            'errno' => $errno,
            'error' => $error,
            'info' => $info,
        ]);

        curl_close($ch);

        return [
            'http_code' => $httpCode,
            'body' => $response,
            'error' => $error,
        ];
    }
}
