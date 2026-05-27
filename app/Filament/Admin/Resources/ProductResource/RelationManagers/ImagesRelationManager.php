<?php

namespace App\Filament\Admin\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Images';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('path')
                ->image()
                ->disk('public')
                ->directory(fn ($livewire) => 'products/' . $livewire->getOwnerRecord()->slug)
                ->required()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('alt')->maxLength(255),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_primary')->default(false),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alt')
            ->columns([
                Tables\Columns\ImageColumn::make('path')->disk('public'),
                Tables\Columns\TextColumn::make('alt'),
                Tables\Columns\TextColumn::make('sort_order'),
                Tables\Columns\IconColumn::make('is_primary')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
