<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class RecentClients extends BaseWidget
{
    protected static ?string $heading = 'Novos Clientes';

    /**
     * Consulta: últimos 5 clientes cadastrados
     */
    protected function getTableQuery(): Builder|Relation|null
    {
        return Client::query()->latest()->limit(5);
    }

    /**
     * Define as colunas da tabela
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nome')
                ->sortable()
                ->searchable(),

            TextColumn::make('document')
                ->label('Documento')
                ->sortable(),

            TextColumn::make('city')
                ->label('Cidade')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Cadastrado em')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ];
    }
}
