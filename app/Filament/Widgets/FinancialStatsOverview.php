<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Client;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;

        // Totais gerais
        $totalReceita = Invoice::where('status', 'paid')->sum('amount');
        $totalPendentes = Invoice::where('status', 'pending')->sum('amount');
        $totalClientes = Client::count();

        // KPIs do mês atual
        $receitaMes = Invoice::where('status', 'paid')
            ->whereMonth('created_at', $mesAtual)
            ->whereYear('created_at', $anoAtual)
            ->sum('amount');

        $inadimplencia = ($totalReceita + $totalPendentes) > 0
            ? round(($totalPendentes / ($totalReceita + $totalPendentes)) * 100, 1)
            : 0;

        $ticketMedio = $totalClientes > 0
            ? round($totalReceita / $totalClientes, 2)
            : 0;

        return [
            Stat::make('Receita Mês Atual', 'R$ ' . number_format($receitaMes, 2, ',', '.'))
                ->description('Receita recebida em ' . Carbon::now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('success'),

            Stat::make('Inadimplência', "{$inadimplencia}%")
                ->description('Percentual de faturas pendentes')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($inadimplencia > 10 ? 'danger' : 'warning'),

            Stat::make('Ticket Médio', 'R$ ' . number_format($ticketMedio, 2, ',', '.'))
                ->description('Receita média por cliente')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('info'),
        ];
    }
}
