<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';
    protected static ?string $title = 'Assinaturas / Contratos';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('plan_name')
                    ->label('Plano / Contrato')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->label('Valor (R$)')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Início')
                    ->required(),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Término'),

                Forms\Components\Select::make('billing_cycle')
                    ->label('Ciclo de Cobrança')
                    ->options([
                        'mensal' => 'Mensal',
                        'trimestral' => 'Trimestral',
                        'semestral' => 'Semestral',
                        'anual' => 'Anual',
                    ])
                    ->default('mensal')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'ativa' => 'Ativa',
                        'suspensa' => 'Suspensa',
                        'cancelada' => 'Cancelada',
                        'encerrada' => 'Encerrada',
                    ])
                    ->default('ativa')
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Observações')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordTitleAttribute('plan_name')
            ->columns([
                Tables\Columns\TextColumn::make('plan_name')
                    ->label('Plano / Contrato')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL', true),

                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('Ciclo'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'ativa',
                        'warning' => 'suspensa',
                        'danger'  => 'cancelada',
                        'gray'    => 'encerrada',
                    ]),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Início')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Término')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nova Assinatura'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
