<?php

namespace Z3d0X\FilamentFabricator\Resources;

use Closure;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Forms\Components\TagsInput;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;
use Z3d0X\FilamentFabricator\Resources\PageResource\Pages;


class PageResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModel(): string
    {
        return FilamentFabricator::getPageModel();
    }

    public static function form(Form $form): Form
    {

        // Nvan

        return $form
            ->columns(3)
            ->schema([
                Group::make()
                    ->schema([
                        Group::make()->schema(FilamentFabricator::getSchemaSlot('blocks.before')),

                        TextInput::make('title')->required()->maxLength(65)->label('Titolo')->reactive()->label('Titolo (massimo 65 caratteri spazi inclusi)')
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('slug', Str::slug($state));
                            })->required(),


                        TextInput::make('sottotitolo')->required(),


                        Select::make('categoria')
                            ->options([
                                'prima_squadra' => 'Prima squadra',
                                'settore_giovanile' => 'Settore giovanile',
                                'societa' => 'Società',
                                'stampa' => 'Biglietti',
                                'partner' => 'Partner',
                                'Progetti_speciali' => 'Progetti speciali',
                            ])->required(),


                        MarkdownEditor::make('meta')->required()->maxLength(155)->label('Meta description (massimo 155 caratteri spazi inclusi)')->toolbarButtons([]),

                        PageBuilder::make('blocks')
                            ->label(__('filament-fabricator::page-resource.labels.blocks')),

                        Group::make()->schema(FilamentFabricator::getSchemaSlot('blocks.after')),
                    ])
                    ->columnSpan(2),

                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Group::make()->schema(FilamentFabricator::getSchemaSlot('sidebar.before')),

                        Card::make()
                            ->schema([
                                // Placeholder::make('page_url')
                                //     ->visible(fn (?PageContract $record) => config('filament-fabricator.routing.enabled') && filled($record))
                                //     ->content(fn (?PageContract $record) => FilamentFabricator::getPageUrlFromId($record?->id)),

                                Select::make('autore')
                                    ->options([
                                        'Feralpisalò Media house' => 'Feralpisalò Media house',
                                    ])->disabled()->default('Feralpisalò Media house'),


                                DateTimePicker::make('published_at'),


                                TagsInput::make('tag')->separator(','),



                                // Hidden::make('is_slug_changed_manually')
                                //     ->default(false)
                                //     ->dehydrated(false),



                                // TextInput::make('slug')
                                //  ->label(__('filament-fabricator::page-resource.labels.slug'))
                                //   ->unique(ignoreRecord: true, callback: fn (Unique $rule, Closure $get) => $rule->where('parent_id', $get('parent_id'))),


                                TextInput::make('slug')
                                    ->label(__('filament-fabricator::page-resource.labels.slug'))
                                    ->unique(ignoreRecord: true, callback: fn (Unique $rule, Closure $get) => $rule->where('parent_id', $get('parent_id')))
                                    ->afterStateUpdated(function (Closure $set) {
                                        $set('is_slug_changed_manually', true);
                                    })
                                    ->rule(function ($state) {
                                        return function (string $attribute, $value, Closure $fail) use ($state) {
                                            if ($state !== '/' && (Str::startsWith($value, '/') || Str::endsWith($value, '/'))) {
                                                $fail(__('filament-fabricator::page-resource.errors.slug_starts_or_ends_with_slash'));
                                            }
                                        };
                                    })
                                    ->required(),
                                //     ->afterStateUpdated(function (Closure $set) {
                                //         $set('is_slug_changed_manually', true);
                                //     })
                                //     ->rule(function ($state) {
                                //         return function (string $attribute, $value, Closure $fail) use ($state) {
                                //             if ($state !== '/' && (Str::startsWith($value, '/') || Str::endsWith($value, '/'))) {
                                //                 $fail(__('filament-fabricator::page-resource.errors.slug_starts_or_ends_with_slash'));
                                //             }
                                //         };
                                //     })
                                //     ->required(),

                                Toggle::make('is_published')->label('Attivo'),

                                Select::make('layout')
                                    ->label(__('filament-fabricator::page-resource.labels.layout'))
                                    ->options(FilamentFabricator::getLayouts())
                                    ->default('default')
                                    ->required(),

                                Select::make('parent_id')
                                    ->label(__('filament-fabricator::page-resource.labels.parent'))
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->suffixAction(
                                        fn ($get, $context) => FormAction::make($context . '-parent')
                                            ->icon('heroicon-o-external-link')
                                            ->url(fn () => PageResource::getUrl($context, ['record' => $get('parent_id')]))
                                            ->openUrlInNewTab()
                                            ->visible(fn () => filled($get('parent_id')))
                                    )
                                    ->relationship(
                                        'parent',
                                        'title',
                                        function (Builder $query, ?PageContract $record) {
                                            if (filled($record)) {
                                                $query->where('id', '!=', $record->id);
                                            }
                                        }
                                    ),
                            ]),

                        Group::make()->schema(FilamentFabricator::getSchemaSlot('sidebar.after')),
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

                TextColumn::make('url')
                    ->label(__('filament-fabricator::page-resource.labels.url'))
                    ->toggleable()
                    ->getStateUsing(fn (?PageContract $record) => FilamentFabricator::getPageUrlFromId($record->id) ?: null)
                    ->url(fn (?PageContract $record) => FilamentFabricator::getPageUrlFromId($record->id) ?: null, true)
                    ->visible(config('filament-fabricator.routing.enabled')),

                BooleanColumn::make('is_published')->searchable()->label('Attivo')->default(false),



                TextColumn::make('parent.title')
                    ->label(__('filament-fabricator::page-resource.labels.parent'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => $state ?? '-')
                    ->url(fn (?PageContract $record) => filled($record->parent_id) ? PageResource::getUrl('edit', ['record' => $record->parent_id]) : null),
            ])

            ->actions([
                ViewAction::make()
                    ->visible(config('filament-fabricator.enable-view-page')),
                EditAction::make(),
                Action::make('visit')
                    ->label(__('filament-fabricator::page-resource.actions.visit'))
                    ->url(fn (?PageContract $record) => FilamentFabricator::getPageUrlFromId($record->id, true) ?: null)
                    ->icon('heroicon-o-external-link')
                    ->openUrlInNewTab()
                    ->color('success')
                    ->visible(config('filament-fabricator.routing.enabled')),
            ])
            ->bulkActions([]);
    }

    public static function getLabel(): string
    {
        return __('filament-fabricator::page-resource.labels.page');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-fabricator::page-resource.labels.pages');
    }

    public static function getPages(): array
    {
        return array_filter([
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'view' => config('filament-fabricator.enable-view-page') ? Pages\ViewPage::route('/{record}') : null,
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ]);
    }
}
