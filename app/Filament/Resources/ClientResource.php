<?php


namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Resources\Resource;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Clientes';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nome / Razão Social')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('document')
                ->label('CPF / CNPJ')
                ->mask(fn($get) => strlen(preg_replace('/\D/', '', (string) $get('document'))) > 11
                    ? '99.999.999/9999-99'
                    : '999.999.999-99')
                ->unique(ignoreRecord: true)
                ->required(),

            Forms\Components\TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required(),

            Forms\Components\TextInput::make('phone')
                ->label('Telefone')
                ->tel()
                ->mask('(99) 99999-9999'),

            Forms\Components\Textarea::make('address')
                ->label('Endereço'),

            Forms\Components\TextInput::make('city')->label('Cidade'),
            Forms\Components\TextInput::make('state')->label('Estado')->maxLength(2),
            Forms\Components\TextInput::make('cep')->label('CEP')->mask('99999-999'),
            Forms\Components\Textarea::make('notes')->label('Observações'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('document')
                    ->label('CNPJ/CPF'),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone'),

                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])

            // ✅ ações por registro (linha)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            // ✅ ações em massa (selecionar vários registros)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\ClientResource\RelationManagers\SubscriptionsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
            'view' => Pages\ViewClient::route('/{record}'),
        ];
    }
}
