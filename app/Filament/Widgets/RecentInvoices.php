<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class RecentInvoices extends BaseWidget
{
    protected static ?string $heading = 'Últimas Faturas';

    /**
     * Consulta para buscar as últimas faturas.
     */
    protected function getTableQuery(): Builder|Relation|null
    {
        return Invoice::query()
            ->with('client')
            ->latest()
            ->limit(5);
    }

    /**
     * Define as colunas da tabela.
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('invoice_number')
                ->label('Fatura')
                ->sortable()
                ->searchable(),

            TextColumn::make('client.name')
                ->label('Cliente')
                ->sortable()
                ->searchable(),

            TextColumn::make('amount')
                ->label('Valor')
                ->money('BRL', true)
                ->sortable(),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->colors([
                    'success' => fn ($state) => $state === 'paid',
                    'warning' => fn ($state) => $state === 'pending',
                    'danger' => fn ($state) => $state === 'overdue',
                ])
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Criada em')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ];
    }
}
