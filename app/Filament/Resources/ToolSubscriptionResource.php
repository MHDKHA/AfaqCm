<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ToolSubscriptionResource\Pages;
use App\Models\ToolSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class ToolSubscriptionResource extends Resource
{
    protected static ?string $model = ToolSubscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.resources.tool_subscription.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.tool_subscription.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.tool_subscription.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.tool_subscription.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label(__('filament.fields.user'))
                ->relationship('user', 'name')
//                ->searchable()
                ->required()
                ->native(false),
            Forms\Components\Select::make('tool_id')
                ->label(__('filament.fields.tool'))
                ->relationship('tool', 'id')
                ->getOptionLabelFromRecordUsing(fn ($record) => App::getLocale() === 'ar' ? $record->name_ar : $record->name_en)
//                ->searchable()
                ->required()
                ->native(false),
            Forms\Components\Select::make('plan_type')
                ->label(__('filament.fields.plan_type'))
                ->options([
                    'free' => __('filament.plans.free'),
                    'premium' => __('filament.plans.premium'),
                ])
                ->required()->native(false),
            Forms\Components\Select::make('status')
                ->label(__('filament.fields.status'))
                ->options([
                    'active' => __('filament.status.active'),
                    'inactive' => __('filament.status.inactive'),
                    'expired' => __('filament.status.expired'),
                ])->native(false)
                ->required(),
            Forms\Components\DateTimePicker::make('started_at')
                ->label(__('filament.fields.started_at'))
                ->required()->native(false),
            Forms\Components\DateTimePicker::make('expires_at')
                ->label(__('filament.fields.expires_at'))
                ->native(false),
            Forms\Components\KeyValue::make('features')
                ->label(__('filament.fields.features'))
                ->columnSpanFull(),
            Forms\Components\TextInput::make('amount')
                ->label(__('filament.fields.amount'))
                ->numeric()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('currency')
                ->label(__('filament.fields.currency'))
                ->maxLength(3)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tool.name')
                    ->label(__('filament.fields.tool'))
                    ->getStateUsing(fn ($record) => App::getLocale() === 'ar' ? $record->tool->name_ar : $record->tool->name_en)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('plan_type')
                    ->label(__('filament.fields.plan_type'))
                    ->formatStateUsing(fn($state) => __('filament.plans.' . $state))
                    ->colors([
                        'primary' => 'free',
                        'success' => 'premium',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->formatStateUsing(fn($state) => __('filament.status.' . $state))
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'expired',
                    ]),
                Tables\Columns\TextColumn::make('started_at')
                    ->label(__('filament.fields.started_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label(__('filament.fields.expires_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan_type')
                    ->label(__('filament.fields.plan_type'))
                    ->options([
                        'free' => __('filament.plans.free'),
                        'premium' => __('filament.plans.premium'),
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.fields.status'))
                    ->options([
                        'active' => __('filament.status.active'),
                        'inactive' => __('filament.status.inactive'),
                        'expired' => __('filament.status.expired'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListToolSubscriptions::route('/'),
            'create' => Pages\CreateToolSubscription::route('/create'),
            'edit' => Pages\EditToolSubscription::route('/{record}/edit'),
        ];
    }
}
