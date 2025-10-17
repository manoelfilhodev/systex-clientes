<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BancoInterService
{
    private string $clientId;
    private string $clientSecret;
    private string $pfxPath;
    private string $conta;
    private string $pixKey;
    private string $baseUrl;
    private string $tokenPath;

    public function __construct()
    {
        $this->clientId = env('INTER_CLIENT_ID');
        $this->clientSecret = env('INTER_CLIENT_SECRET');
        $this->pfxPath = base_path(env('INTER_PFX_PATH'));
        $this->conta = env('INTER_CONTA_CORRENTE', '123456789');
        $this->pixKey = env('INTER_PIX_KEY');
        $this->baseUrl = env('INTER_BASE_URL', 'https://cdpj-sandbox.partners.uatinter.co');
        $this->tokenPath = storage_path('app/inter_token.json');
    }

    /**
     * ✅ Obtém ou renova o token OAuth2
     */
    private function getAccessToken(): ?string
    {
        // Reutiliza token se ainda for válido
        if (file_exists($this->tokenPath)) {
            $data = json_decode(file_get_contents($this->tokenPath), true);
            if (!empty($data['access_token']) && $data['expires_at'] > time()) {
                return $data['access_token'];
            }
        }

        Log::info('🔐 Solicitando novo token Banco Inter...');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "{$this->baseUrl}/oauth/v2/token",
            CURLOPT_POST => true,
            CURLOPT_SSLCERTTYPE => "P12",
            CURLOPT_SSLCERT => $this->pfxPath,
            CURLOPT_SSLCERTPASSWD => "",
            CURLOPT_POSTFIELDS => http_build_query([
                "client_id" => $this->clientId,
                "client_secret" => $this->clientSecret,
                "scope" => "boleto-cobranca.read boleto-cobranca.write",
                "grant_type" => "client_credentials",
            ]),
            CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (empty($data['access_token'])) {
            Log::error('❌ Falha ao obter token Banco Inter', ['response' => $response]);
            return null;
        }

        $data['expires_at'] = time() + ($data['expires_in'] ?? 3500);
        file_put_contents($this->tokenPath, json_encode($data));

        Log::info('✅ Novo token Banco Inter obtido com sucesso.');

        return $data['access_token'];
    }

    /**
     * 💸 Gera cobrança híbrida (BOLETO + PIX)
     */
    public function gerarCobranca(Invoice $invoice): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Falha ao obter token.'];
        }

        $cep = preg_replace('/\D/', '', $invoice->client->cep ?? '13200000');
        $documento = preg_replace('/\D/', '', $invoice->client->document ?? '');

        if (strlen($documento) === 11) {
            $tipoPessoa = 'FISICA';
        } elseif (strlen($documento) === 14) {
            $tipoPessoa = 'JURIDICA';
        } else {
            // fallback seguro para sandbox
            $documento = '39053344705'; // CPF válido
            $tipoPessoa = 'FISICA';
        }

        $payload = [
            "seuNumero" => "INV" . $invoice->id,
            "valorNominal" => (float) round($invoice->amount, 2),
            "dataVencimento" => date('Y-m-d', strtotime($invoice->due_date)),
            "numDiasAgenda" => 30,
            "geraPix" => true,
            "pix" => [
                "chave" => $this->pixKey
            ],
            "atualizarPagador" => false, // ✅ obrigatório para o sandbox
            "pagador" => [
                "cpfCnpj" => $documento,
                "tipoPessoa" => $tipoPessoa,
                "nome" => $invoice->client->name ?? 'Cliente Systex',
                "email" => $invoice->client->email ?? 'financeiro@systex.com.br',
                "endereco" => $invoice->client->address ?? 'Rua Exemplo',
                "cidade" => $invoice->client->city ?? 'Jundiaí',
                "uf" => 'SP',
                "cep" => $cep,
            ],
            "mensagem" => [
                "linha1" => "Cobrança automática via Systex Clientes",
            ],
        ];

        Log::info("💸 Enviando cobrança ao Banco Inter", [
            'invoice_id' => $invoice->id,
            'payload' => $payload
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "{$this->baseUrl}/cobranca/v3/cobrancas",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSLCERTTYPE => "P12",
            CURLOPT_SSLCERT => $this->pfxPath,
            CURLOPT_SSLCERTPASSWD => "",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "x-inter-conta-corrente: {$this->conta}",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Log::error('❌ Erro CURL Banco Inter', ['error' => $error]);
            return ['success' => false, 'error' => $error];
        }

        $response = json_decode($result, true);
        Log::debug('📥 Retorno da criação de cobrança', ['response' => $response]);

        if (empty($response['codigoSolicitacao'])) {
            Log::warning('⚠️ Falha ao gerar cobrança', ['response' => $response]);
            return ['success' => false, 'response' => $response];
        }

        Log::info('✅ Cobrança criada com sucesso', [
            'codigoSolicitacao' => $response['codigoSolicitacao']
        ]);

        // Consulta os detalhes da cobrança
        return $this->consultarCobranca(
            $response['codigoSolicitacao'],
            $token,
            $invoice
        );
    }

    /**
     * 🔍 Consulta cobrança e salva dados do PIX e BOLETO
     */
    private function consultarCobranca(string $codigoSolicitacao, string $token, Invoice $invoice): array
    {
        $url = "{$this->baseUrl}/cobranca/v3/cobrancas/{$codigoSolicitacao}";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSLCERTTYPE => "P12",
            CURLOPT_SSLCERT => $this->pfxPath,
            CURLOPT_SSLCERTPASSWD => "",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "x-inter-conta-corrente: {$this->conta}",
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result, true);

        if (empty($data['boleto']) && empty($data['pix'])) {
            Log::warning('⚠️ Erro ao consultar cobrança', ['response' => $data]);
            return ['success' => false, 'response' => $data];
        }

        // === PIX ===
        if (!empty($data['pix']['pixCopiaECola'])) {
            $qrContent = $data['pix']['pixCopiaECola'];
            $qrPath = "pix_qr_codes/invoice_{$invoice->id}.svg";

            Storage::disk('public')->put($qrPath, QrCode::format('svg')->size(300)->generate($qrContent));

            $invoice->update([
                'pix_copia_cola' => $qrContent,
                'pix_qr_code' => $qrPath,
                'pix_txid' => $data['pix']['txid'] ?? null,
            ]);

            Log::info('✅ PIX salvo com sucesso', [
                'invoice_id' => $invoice->id,
                'qr_path' => $qrPath,
            ]);
        }

        // === BOLETO ===
        if (!empty($data['boleto'])) {
            $boleto = $data['boleto'];

            $invoice->update([
                'boleto_linha_digitavel' => $boleto['linhaDigitavel'] ?? null,
                'boleto_url' => $boleto['urlBoleto'] ?? null,
                'boleto_barcode' => $boleto['codigoBarras'] ?? null,
                'boleto_nosso_numero' => $boleto['nossoNumero'] ?? null,
            ]);

            Log::info('💾 Boleto salvo com sucesso', [
                'invoice_id' => $invoice->id,
                'linha_digitavel' => $boleto['linhaDigitavel'] ?? null,
            ]);
        }


        return ['success' => true, 'data' => $data];
    }
}
