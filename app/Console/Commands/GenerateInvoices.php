<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\Invoice;
use Carbon\Carbon;

class GenerateInvoices extends Command
{
    protected $signature = 'invoices:generate';
    protected $description = 'Gera faturas automáticas para assinaturas ativas, com base no ciclo de cobrança.';

    public function handle()
    {
        $today = Carbon::today();
        $subscriptions = Subscription::whereIn('status', ['active', 'ativa'])->get();
        $count = 0;

        foreach ($subscriptions as $subscription) {
            // Define quantidade de faturas de acordo com o ciclo
            $months = match ($subscription->billing_cycle) {
                'mensal' => 1,
                'trimestral' => 3,
                'semestral' => 6,
                'anual' => 12,
                default => 1,
            };

            // Data inicial para gerar as faturas
            $startDate = Carbon::parse($subscription->start_date);

            // Gera todas as faturas até o final do ciclo
            for ($i = 0; $i < $months; $i++) {
                $dueDate = $startDate->copy()->addMonths($i);

                // Evita duplicar se já existir fatura com mesmo mês/ano
                $exists = Invoice::where('subscription_id', $subscription->id)
                    ->whereMonth('due_date', $dueDate->month)
                    ->whereYear('due_date', $dueDate->year)
                    ->exists();

                if (! $exists) {
                    Invoice::create([
                        'subscription_id' => $subscription->id,
                        'invoice_number'  => 'INV-' . strtoupper(uniqid()),
                        'due_date'        => $dueDate,
                        'amount'          => $subscription->amount ?? 0,
                        'status'          => 'pending',
                    ]);
                    $count++;
                }
            }
        }

        $this->info("✅ {$count} faturas foram geradas com sucesso para contratos ativos!");
    }
}
