<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Financeiro';
    protected static ?string $pluralModelLabel = 'Pagamentos';
    protected static ?string $modelLabel = 'Pagamento';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('invoice_id')
                    ->label('Fatura')
                    ->relationship('invoice', 'invoice_number')
                    ->required(),

                Forms\Components\DatePicker::make('payment_date')
                    ->label('Data do Pagamento'),

                Forms\Components\TextInput::make('amount')
                    ->label('Valor (R$)')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('method')
                    ->label('Método')
                    ->options([
                        'pix' => 'Pix',
                        'boleto' => 'Boleto',
                        'cartao' => 'Cartão',
                    ])
                    ->default('pix'),

                Forms\Components\TextInput::make('transaction_id')
                    ->label('ID da Transação'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'confirmed' => 'Confirmado',
                        'failed' => 'Falhou',
                    ])
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.subscription.client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Fatura'),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Data')
                    ->date(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('method')
                    ->label('Método'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'failed',
                    ])
                    ->label('Status'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'confirmed' => 'Confirmado',
                        'failed' => 'Falhou',
                    ]),
            ])
            ->actions([
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
