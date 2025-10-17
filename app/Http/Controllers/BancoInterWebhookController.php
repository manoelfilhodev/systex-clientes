<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BancoInterWebhookController extends Controller
{
    /**
     * Recebe notificações do Banco Inter (PIX ou BOLETO).
     * Atualiza o status da fatura conforme o pagamento.
     */
    public function handle(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('Webhook Banco Inter recebido', $data);

            // O Banco Inter envia o campo "txid" (para PIX)
            // ou "seuNumero" (para boletos)
            $invoice = Invoice::where('invoice_number', $data['seuNumero'] ?? '')
                ->orWhere('pix_txid', $data['txid'] ?? '')
                ->first();

            if (!$invoice) {
                Log::warning('Fatura não encontrada para webhook Inter', ['data' => $data]);
                return response()->json(['message' => 'Fatura não encontrada'], 200);
            }

            // Verifica o status vindo do Inter
            $status = strtolower($data['status'] ?? '');

            if (in_array($status, ['liquidado', 'pago', 'concluido'])) {
                $invoice->update(['status' => 'paid']);
                Log::info("Fatura {$invoice->invoice_number} marcada como paga.");
            } elseif (in_array($status, ['cancelado', 'baixado'])) {
                $invoice->update(['status' => 'canceled']);
                Log::info("Fatura {$invoice->invoice_number} marcada como cancelada.");
            } else {
                Log::info("Status não relevante recebido do Inter: {$status}");
            }

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('Erro ao processar webhook Banco Inter', [
                'erro' => $e->getMessage(),
                'body' => $request->all(),
            ]);
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }
}
