<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\BancoInterService;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    public function created(Invoice $invoice)
    {
        try {
            Log::info("🔔 Nova fatura criada [#{$invoice->id}] — iniciando geração de cobrança.");

            $inter = new BancoInterService();
            $result = $inter->gerarCobranca($invoice);

            if (!empty($result['success'])) {
                Log::info("✅ Cobrança gerada com sucesso para fatura [#{$invoice->id}]");
            } else {
                Log::warning("⚠️ Falha ao gerar cobrança para fatura [#{$invoice->id}]", ['response' => $result]);
            }
        } catch (\Throwable $e) {
            Log::error("❌ Erro no InvoiceObserver ao gerar cobrança [#{$invoice->id}]: {$e->getMessage()}");
        }
    }
}
