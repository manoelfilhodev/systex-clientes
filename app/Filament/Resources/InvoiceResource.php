<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Financeiro';
    protected static ?string $pluralModelLabel = 'Faturas';
    protected static ?string $modelLabel = 'Fatura';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subscription_id')
                    ->label('Assinatura')
                    ->relationship('subscription', 'plan_name')
                    ->required(),

                Forms\Components\TextInput::make('invoice_number')
                    ->label('Número da Fatura')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\DatePicker::make('due_date')
                    ->label('Vencimento')
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Valor (R$)')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'paid' => 'Pago',
                        'overdue' => 'Vencido',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->getStateUsing(function ($record) {
                        return $record->client?->name
                            ?? $record->subscription?->client?->name
                            ?? '—';
                    })
                    ->sortable(query: function ($query, string $direction): void {
                        // Ordena considerando os dois possíveis relacionamentos
                        $query
                            ->leftJoin('clients as c1', 'invoices.client_id', '=', 'c1.id')
                            ->leftJoin('subscriptions as s', 'invoices.subscription_id', '=', 's.id')
                            ->leftJoin('clients as c2', 's.client_id', '=', 'c2.id')
                            ->orderByRaw("COALESCE(c1.name, c2.name) {$direction}")
                            ->select('invoices.*');
                    })
                    ->searchable(query: function ($query, string $search): void {
                        // Busca tanto pelo cliente direto quanto pelo cliente da assinatura
                        $query
                            ->whereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('subscription.client', fn($q) => $q->where('name', 'like', "%{$search}%"));
                    }),




                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Fatura')
                    ->searchable(),

                Tables\Columns\TextColumn::make('subscription.plan_name')
                    ->label('Plano'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'overdue',
                    ])
                    ->label('Status'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'paid' => 'Pago',
                        'overdue' => 'Vencido',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'view' => Pages\ViewInvoice::route('/{record}'), // 👈 ADICIONAR ESTA LINHA
        ];
    }
}
