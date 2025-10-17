<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

// ✅ importe todos os widgets usados
use App\Filament\Widgets\FinancialStatsOverview;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\InadimplenciaChart;
use App\Filament\Widgets\RecentInvoices;
use App\Filament\Widgets\RecentClients;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard de Gestão';
    protected static ?string $title = 'Dashboard de Gestão';
    protected static ?int $navigationSort = 1;

    /**
     * Layout moderno e balanceado.
     */
    public function getWidgets(): array
    {
        return [
            // === Linha 1: KPIs ===
            FinancialStatsOverview::class,

            // === Linha 2: Gráficos lado a lado ===
            RevenueChart::class,
            InadimplenciaChart::class,

            // === Linha 3: Tabelas lado a lado ===
            RecentClients::class,
            RecentInvoices::class,



        ];
    }

    /**
     * Organiza os widgets em colunas responsivas.
     */
    public function getColumns(): int|array
    {
        return [
            'default' => 2,
            'md' => 2,
            'lg' => 3,
        ];
    }
}
