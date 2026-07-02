<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Promo codes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Code')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->required()
                        ->maxLength(64)
                        ->unique(ignoreRecord: true)
                        ->placeholder('WELCOME10')
                        ->helperText('Stored uppercase. Customers can enter any case.'),
                    Forms\Components\Select::make('type')
                        ->options(PromoCode::types())
                        ->required()
                        ->live(),
                    Forms\Components\TextInput::make('value')
                        ->label(fn (Get $get) => match ($get('type')) {
                            PromoCode::TYPE_PERCENT => 'Discount (%)',
                            PromoCode::TYPE_FIXED => 'Discount (₹)',
                            default => 'Value',
                        })
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(fn (Get $get) => $get('type') === PromoCode::TYPE_PERCENT ? 100 : null)
                        ->required(fn (Get $get) => in_array($get('type'), [PromoCode::TYPE_PERCENT, PromoCode::TYPE_FIXED], true))
                        ->hidden(fn (Get $get) => $get('type') === PromoCode::TYPE_FREE_SHIPPING),
                    Forms\Components\TextInput::make('description')
                        ->maxLength(255)
                        ->placeholder('Internal note, e.g. Instagram launch campaign')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Rules')
                ->schema([
                    Forms\Components\TextInput::make('min_order_amount')
                        ->label('Minimum order (₹)')
                        ->numeric()
                        ->minValue(0)
                        ->placeholder('Optional'),
                    Forms\Components\TextInput::make('max_uses')
                        ->label('Maximum uses')
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('Unlimited'),
                    Forms\Components\TextInput::make('used_count')
                        ->label('Times used')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(false)
                        ->visibleOn('edit'),
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Valid from')
                        ->seconds(false),
                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Valid until')
                        ->seconds(false),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable()->copyable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn (string $state) => PromoCode::types()[$state] ?? $state),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(fn (PromoCode $record) => $record->valueLabel()),
                Tables\Columns\TextColumn::make('min_order_amount')
                    ->label('Min order')
                    ->formatStateUsing(fn (?int $state) => $state ? '₹'.number_format($state) : '—'),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->formatStateUsing(fn (PromoCode $record) => $record->max_uses
                        ? "{$record->used_count} / {$record->max_uses}"
                        : (string) $record->used_count),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
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
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
