<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Receita Mensal';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = Invoice::selectRaw('MONTH(created_at) as mes, SUM(amount) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Receita (R$)',
                    'data' => array_values($data),
                    'backgroundColor' => '#16A34A',
                    'borderColor' => '#15803D',
                ],
            ],
            'labels' => array_map(fn ($m) => date('M', mktime(0, 0, 0, $m, 1)), array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
