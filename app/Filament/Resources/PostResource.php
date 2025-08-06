<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.post.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.post.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.post.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('filament.fields.title'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->label(__('filament.fields.slug'))
                    ->disabled()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\RichEditor::make('content')
                    ->label(__('filament.fields.content'))
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('thumbnail')
                    ->label(__('filament.fields.thumbnail'))
                    ->image()
                    ->directory('thumbnails')
                    ->disk('public'),

                Forms\Components\FileUpload::make('image_gallery')
                    ->label(__('filament.fields.image_gallery'))
                    ->image()
                    ->multiple()
                    ->directory('gallery')
                    ->disk('public'),

                Forms\Components\FileUpload::make('audio')
                    ->label(__('filament.fields.audio'))
//                    ->acceptedFileTypes(['audio/mpeg','audio/ogg'])
                    ->directory('audio'),
                Forms\Components\FileUpload::make('video')
                    ->label(__('filament.fields.video'))
//                    ->acceptedFileTypes(['video/mp4'])
                    ->directory('video'),
                Forms\Components\TextInput::make('youtube_url')
                    ->label(__('filament.fields.youtube_url'))
                    ->url()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label(__('filament.fields.published_at')),
                Forms\Components\Select::make('status')
                    ->label(__('filament.fields.status'))
                    ->options([
                        'draft' => __('filament.status.draft'),
                        'published' => __('filament.status.published'),
                    ])
                    ->default('draft')
                    ->required(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn($state) => __('filament.status.' . $state)),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('filament.fields.published_at'))
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
