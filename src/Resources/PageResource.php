<?php

namespace Z3d0X\FilamentFabricator\Resources;

use Closure;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Page;
use Z3d0X\FilamentFabricator\Resources\PageResource\Pages;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()
                    ->schema([
                        Builder::make('blocks')
                            ->blocks(FilamentFabricator::getPageBlocks()),
                    ])
                    ->columnSpan(2),

                Card::make()
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('title')
                            ->afterStateUpdated(function (Closure $get, Closure $set, ?string $state, ?Model $record) {
                                if (! $get('is_slug_changed_manually') && filled($state) && blank($record)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->debounce(500)
                            ->required(),

                        Hidden::make('is_slug_changed_manually')
                            ->default(false)
                            ->dehydrated(false),

                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->afterStateUpdated(function (Closure $set) {
                                $set('is_slug_changed_manually', true);
                            })
                            ->required(),

                        Select::make('layout')
                            ->options(FilamentFabricator::getLayouts())
                            ->default('default')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'view' => Pages\ViewPage::route('/{record}'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
