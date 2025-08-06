<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ToolPerformanceLevelResource\Pages;
use App\Filament\Resources\ToolPerformanceLevelResource\RelationManagers;
use App\Models\ToolPerformanceLevel;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\App;

class ToolPerformanceLevelResource extends Resource
{
    protected static ?string $model = ToolPerformanceLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.tool_performance_level.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.tool_performance_level.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.tool_performance_level.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tool_id')
                    ->label(__('filament.fields.tool'))
                    ->relationship('tool', 'id') // just use 'id' since we customize the label
                    ->getOptionLabelFromRecordUsing(fn ($record) => App::getLocale() === 'ar' ? $record->name_ar : $record->name_en)
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->label(__('filament.fields.code'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('min_percentage')
                    ->label(__('filament.fields.min_percentage'))
                    ->required()
                    ->numeric(),
                Forms\Components\ColorPicker::make('color')
                    ->label(__('filament.fields.color'))
                    ->required(),
                Forms\Components\TextInput::make('name_en')
                    ->label(__('filament.fields.name_en'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name_ar')
                    ->label(__('filament.fields.name_ar'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('text_en')
                    ->label(__('filament.fields.text_en'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('text_ar')
                    ->label(__('filament.fields.text_ar'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('recommendation_en')
                    ->label(__('filament.fields.recommendation_en'))
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('recommendation_ar')
                    ->label(__('filament.fields.recommendation_ar'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tool.name')
                    ->label(__('filament.fields.tool'))
                    ->getStateUsing(fn ($record) => App::getLocale() === 'ar' ? $record->tool->name_ar : $record->tool->name_en)
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('filament.fields.code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('min_percentage')
                    ->label(__('filament.fields.min_percentage'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->label(__('filament.fields.color'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->label(__('filament.fields.name_en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_ar')
                    ->label(__('filament.fields.name_ar'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('text_en')
                    ->label(__('filament.fields.text_en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('text_ar')
                    ->label(__('filament.fields.text_ar'))
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
            'index' => Pages\ListToolPerformanceLevels::route('/'),
            'create' => Pages\CreateToolPerformanceLevel::route('/create'),
            'edit' => Pages\EditToolPerformanceLevel::route('/{record}/edit'),
        ];
    }
}
