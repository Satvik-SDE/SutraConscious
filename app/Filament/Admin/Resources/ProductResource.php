<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Filament\Admin\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Product')
                ->schema([
                    Forms\Components\Select::make('category_id')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('base_price')
                        ->label('Base price (₹)')
                        ->numeric()
                        ->prefix('₹')
                        ->required()
                        ->minValue(0),
                    Forms\Components\TextInput::make('currency')
                        ->default('INR')
                        ->maxLength(3),
                    Forms\Components\Select::make('sleeve')
                        ->options([
                            'Full Sleeve' => 'Full Sleeve',
                            'Short Sleeve' => 'Short Sleeve',
                            'Half Sleeve' => 'Half Sleeve',
                        ])
                        ->nullable(),
                    Forms\Components\TextInput::make('color_label')
                        ->maxLength(255)
                        ->placeholder('e.g. Indigo, Wine'),
                    Forms\Components\TextInput::make('fabric')
                        ->default('100% Premium Cotton')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(2),

            Forms\Components\Section::make('Descriptions')
                ->schema([
                    Forms\Components\Textarea::make('short_description')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Visibility')
                ->schema([
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\Toggle::make('is_featured')->default(false),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                ])
                ->columns(3),

            Forms\Components\Section::make('SEO')
                ->schema([
                    Forms\Components\TextInput::make('seo_title')->maxLength(255),
                    Forms\Components\Textarea::make('seo_description')->rows(2)->maxLength(500),
                ])
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->money('INR', divideBy: 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Variants'),
                Tables\Columns\TextColumn::make('images_count')
                    ->counts('images')
                    ->label('Images'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->defaultSort('sort_order')
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
            RelationManagers\VariantsRelationManager::class,
            RelationManagers\ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
