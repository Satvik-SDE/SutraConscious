<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ShippingZoneResource\Pages;
use App\Models\ShippingZone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingZoneResource extends Resource
{
    protected static ?string $model = ShippingZone::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 25;

    protected static ?string $navigationLabel = 'Shipping zones';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Zone')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('India — Maharashtra'),
                    Forms\Components\TextInput::make('country_code')
                        ->label('Country code')
                        ->required()
                        ->length(2)
                        ->placeholder('IN')
                        ->helperText('ISO 2-letter code, e.g. IN, US, GB'),
                    Forms\Components\Select::make('match_type')
                        ->options(ShippingZone::matchTypes())
                        ->required()
                        ->live(),
                    Forms\Components\TextInput::make('match_value')
                        ->label('Match value')
                        ->maxLength(255)
                        ->placeholder(fn (Get $get) => match ($get('match_type')) {
                            ShippingZone::MATCH_STATE => 'Maharashtra',
                            ShippingZone::MATCH_POSTAL_PREFIX => '400',
                            ShippingZone::MATCH_POSTAL_EXACT => '400001',
                            default => 'Leave empty for entire country',
                        })
                        ->helperText(fn (Get $get) => match ($get('match_type')) {
                            ShippingZone::MATCH_COUNTRY => 'Not needed — applies to the whole country.',
                            ShippingZone::MATCH_STATE => 'State or region name (case insensitive).',
                            ShippingZone::MATCH_POSTAL_PREFIX => 'Pin / postal code starts with this prefix.',
                            ShippingZone::MATCH_POSTAL_EXACT => 'Exact pin / postal code.',
                            default => null,
                        })
                        ->hidden(fn (Get $get) => $get('match_type') === ShippingZone::MATCH_COUNTRY),
                    Forms\Components\TextInput::make('priority')
                        ->numeric()
                        ->default(0)
                        ->helperText('Higher priority wins when multiple zones match.'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Rates & availability')
                ->schema([
                    Forms\Components\TextInput::make('shipping_fee')
                        ->label('Shipping fee (₹)')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    Forms\Components\TextInput::make('free_shipping_min')
                        ->label('Free shipping above (₹)')
                        ->numeric()
                        ->minValue(0)
                        ->placeholder('Optional'),
                    Forms\Components\Toggle::make('is_serviceable')
                        ->label('Serviceable')
                        ->default(true)
                        ->helperText('Turn off to block checkout for this location.'),
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
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('country_code')->label('Country')->sortable(),
                Tables\Columns\TextColumn::make('match_type')
                    ->formatStateUsing(fn (string $state) => ShippingZone::matchTypes()[$state] ?? $state),
                Tables\Columns\TextColumn::make('match_value')->placeholder('—'),
                Tables\Columns\TextColumn::make('shipping_fee')
                    ->label('Fee')
                    ->formatStateUsing(fn (int $state) => '₹' . number_format($state)),
                Tables\Columns\TextColumn::make('free_shipping_min')
                    ->label('Free above')
                    ->formatStateUsing(fn (?int $state) => $state ? '₹' . number_format($state) : '—'),
                Tables\Columns\IconColumn::make('is_serviceable')->boolean()->label('OK'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('country_code')
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
            'index' => Pages\ListShippingZones::route('/'),
            'create' => Pages\CreateShippingZone::route('/create'),
            'edit' => Pages\EditShippingZone::route('/{record}/edit'),
        ];
    }
}
