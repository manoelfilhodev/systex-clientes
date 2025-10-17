<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;

class InadimplenciaChart extends ChartWidget
{
    protected static ?string $heading = 'Inadimplência Mensal';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $total = Invoice::whereMonth('created_at', $i)->sum('amount');
            $pendentes = Invoice::where('status', 'pending')
                ->whereMonth('created_at', $i)
                ->sum('amount');
            $data[$i] = $total > 0 ? round(($pendentes / $total) * 100, 1) : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Inadimplência (%)',
                    'data' => array_values($data),
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => array_map(
                fn ($m) => date('M', mktime(0, 0, 0, $m, 1)),
                range(1, 12)
            ),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
