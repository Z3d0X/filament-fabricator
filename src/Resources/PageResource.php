<?php

namespace Z3d0X\FilamentFabricator\Resources;

use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;
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
                        PageBuilder::make('blocks')
                            ->label(__('filament-fabricator::page-resource.labels.blocks'))
                            ->blocks(FilamentFabricator::getPageBlocks()),
                    ])
                    ->columnSpan(2),

                Card::make()
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('filament-fabricator::page-resource.labels.title'))
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
                            ->label(__('filament-fabricator::page-resource.labels.slug'))
                            ->unique(ignoreRecord: true)
                            ->afterStateUpdated(function (Closure $set) {
                                $set('is_slug_changed_manually', true);
                            })
                            ->required(),

                        Select::make('layout')
                            ->label(__('filament-fabricator::page-resource.labels.layout'))
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
                    ->label(__('filament-fabricator::page-resource.labels.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('filament-fabricator::page-resource.labels.slug'))
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('layout')
                    ->label(__('filament-fabricator::page-resource.labels.layout'))
                    ->sortable()
                    ->enum(FilamentFabricator::getLayouts()),
            ])
            ->filters([
                SelectFilter::make('layout')
                    ->label(__('filament-fabricator::page-resource.labels.layout'))
                    ->options(FilamentFabricator::getLayouts()),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('visit')
                    ->label(__('filament-fabricator::page-resource.actions.visit'))
                    ->url(fn (Page $record) => '/' . $record->slug)
                    ->icon('heroicon-o-external-link')
                    ->openUrlInNewTab()
                    ->color('success')
                    ->visible(config('filament-fabricator.routing.enabled')),
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
