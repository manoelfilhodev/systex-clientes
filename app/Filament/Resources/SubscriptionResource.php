<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Assinaturas / Contratos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->required(),

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),

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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
