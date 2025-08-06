<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.account.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.account.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.account.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament.fields.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_name')
                    ->label(__('filament.fields.bank_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('account_number')
                    ->label(__('filament.fields.account_number'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('iban')
                    ->label(__('filament.fields.iban'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('swift_code')
                    ->label(__('filament.fields.swift_code'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('currency')
                    ->label(__('filament.fields.currency'))
                    ->required()
                    ->maxLength(255)
                    ->default('SAR'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.fields.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->label(__('filament.fields.bank_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->label(__('filament.fields.account_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('iban')
                    ->label(__('filament.fields.iban'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('swift_code')
                    ->label(__('filament.fields.swift_code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label(__('filament.fields.currency'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
