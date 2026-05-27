<?php

namespace App\Filament\Admin\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Variants (Size / Color)';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('size')
                ->options([
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                ])
                ->required(),
            Forms\Components\TextInput::make('color')
                ->maxLength(255),
            Forms\Components\TextInput::make('sku')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('price_override')
                ->label('Price override (₹)')
                ->numeric()
                ->prefix('₹')
                ->nullable(),
            Forms\Components\TextInput::make('stock')
                ->numeric()
                ->default(0)
                ->required(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                Tables\Columns\TextColumn::make('size')->sortable(),
                Tables\Columns\TextColumn::make('color'),
                Tables\Columns\TextColumn::make('sku')->copyable(),
                Tables\Columns\TextColumn::make('price_override')->money('INR'),
                Tables\Columns\TextColumn::make('stock'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
