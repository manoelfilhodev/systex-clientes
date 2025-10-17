<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Client;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;
    protected static ?string $title = 'Lista de Faturas';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('gerar_manual')
                ->label('Gerar Fatura Manual')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->button()
                ->form([
                    Forms\Components\Select::make('client_id')
                        ->label('Cliente')
                        ->relationship('client', 'name')
                        ->searchable()
                        ->required(),


                    Forms\Components\TextInput::make('descricao')
                        ->label('Descrição do Serviço')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('amount')
                        ->label('Valor (R$)')
                        ->numeric()
                        ->required(),

                    Forms\Components\Select::make('condicao_pagamento')
                        ->label('Condição de Pagamento')
                        ->options([
                            'avista' => 'À vista',
                            '30' => '30 dias',
                            '60' => '60 dias',
                            '90' => '90 dias',
                        ])
                        ->default('avista')
                        ->required(),

                    Forms\Components\DatePicker::make('due_date')
                        ->label('Data de Vencimento')
                        ->default(now()->addDays(5))
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Define o vencimento baseado na condição de pagamento
                    $dueDate = match ($data['condicao_pagamento']) {
                        '30' => now()->addDays(30),
                        '60' => now()->addDays(60),
                        '90' => now()->addDays(90),
                        default => $data['due_date'],
                    };

                    Invoice::create([
                        'subscription_id' => null,
                        'client_id' => $data['client_id'],
                        'invoice_number' => 'MAN-' . strtoupper(uniqid()),
                        'due_date' => $dueDate,
                        'amount' => $data['amount'],
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);



                    Notification::make()
                        ->title('Fatura manual criada com sucesso!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
