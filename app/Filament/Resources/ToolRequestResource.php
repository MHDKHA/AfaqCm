<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ToolRequestResource\Pages;
use App\Models\ToolRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class ToolRequestResource extends Resource
{
    protected static ?string $model = ToolRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?int $navigationSort =9;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.resources.tool_request.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.tool_request.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.tool_request.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.tool_request.plural_label');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.fields.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament.fields.email')),
                Tables\Columns\TextColumn::make('tool.name')
                    ->label(__('filament.fields.tool'))
                    ->getStateUsing(fn ($record) => App::getLocale() === 'ar' ? $record->tool->name_ar : $record->tool->name_en),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn($state) => __('filament.status.' . $state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.fields.status'))
                    ->options([
                        'pending' => __('filament.status.pending'),
                        'quoted' => __('filament.status.quoted'),
                        'done'    => __('filament.status.done'),
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListToolRequests::route('/'),
            'view' => Pages\ViewToolRequest::route('/{record}'),
        ];
    }
}
