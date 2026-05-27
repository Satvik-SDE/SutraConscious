<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order')
                ->schema([
                    Forms\Components\TextInput::make('number')->disabled(),
                    Forms\Components\Select::make('status')
                        ->options([
                            Order::STATUS_PENDING => 'Pending',
                            Order::STATUS_PROCESSING => 'Processing',
                            Order::STATUS_SHIPPED => 'Shipped',
                            Order::STATUS_DELIVERED => 'Delivered',
                            Order::STATUS_CANCELLED => 'Cancelled',
                        ])
                        ->required(),
                    Forms\Components\Select::make('payment_status')
                        ->options([
                            Order::PAYMENT_UNPAID => 'Unpaid',
                            Order::PAYMENT_PAID => 'Paid',
                            Order::PAYMENT_FAILED => 'Failed',
                            Order::PAYMENT_REFUNDED => 'Refunded',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Order')
                ->schema([
                    Infolists\Components\TextEntry::make('number')->copyable(),
                    Infolists\Components\TextEntry::make('status')->badge(),
                    Infolists\Components\TextEntry::make('payment_status')->badge(),
                    Infolists\Components\TextEntry::make('total')->money('INR'),
                    Infolists\Components\TextEntry::make('created_at')->dateTime(),
                    Infolists\Components\TextEntry::make('paid_at')->dateTime()->placeholder('—'),
                ])->columns(3),

            Infolists\Components\Section::make('Customer')
                ->schema([
                    Infolists\Components\TextEntry::make('customer_name'),
                    Infolists\Components\TextEntry::make('customer_email')->copyable(),
                    Infolists\Components\TextEntry::make('customer_phone')->copyable(),
                ])->columns(3),

            Infolists\Components\Section::make('Shipping Address')
                ->schema([
                    Infolists\Components\TextEntry::make('shipping_line1'),
                    Infolists\Components\TextEntry::make('shipping_line2')->placeholder('—'),
                    Infolists\Components\TextEntry::make('shipping_city'),
                    Infolists\Components\TextEntry::make('shipping_state'),
                    Infolists\Components\TextEntry::make('shipping_postal_code'),
                    Infolists\Components\TextEntry::make('shipping_country'),
                ])->columns(3),

            Infolists\Components\Section::make('Items')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('items')
                        ->schema([
                            Infolists\Components\TextEntry::make('product_name'),
                            Infolists\Components\TextEntry::make('variant_label'),
                            Infolists\Components\TextEntry::make('sku'),
                            Infolists\Components\TextEntry::make('quantity'),
                            Infolists\Components\TextEntry::make('unit_price')->money('INR'),
                            Infolists\Components\TextEntry::make('line_total')->money('INR'),
                        ])
                        ->columns(6),
                ]),

            Infolists\Components\Section::make('Payment')
                ->schema([
                    Infolists\Components\TextEntry::make('payment_provider')->placeholder('—'),
                    Infolists\Components\TextEntry::make('razorpay_order_id')->placeholder('—')->copyable(),
                    Infolists\Components\TextEntry::make('razorpay_payment_id')->placeholder('—')->copyable(),
                ])->columns(3)->collapsed(),

            Infolists\Components\Section::make('Notes')
                ->schema([
                    Infolists\Components\TextEntry::make('notes')->placeholder('—'),
                ])
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->searchable()->copyable()->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->searchable(),
                Tables\Columns\TextColumn::make('total')->money('INR')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors([
                        'gray' => Order::STATUS_PENDING,
                        'warning' => Order::STATUS_PROCESSING,
                        'info' => Order::STATUS_SHIPPED,
                        'success' => Order::STATUS_DELIVERED,
                        'danger' => Order::STATUS_CANCELLED,
                    ]),
                Tables\Columns\TextColumn::make('payment_status')->badge()
                    ->colors([
                        'gray' => Order::PAYMENT_UNPAID,
                        'success' => Order::PAYMENT_PAID,
                        'danger' => Order::PAYMENT_FAILED,
                        'warning' => Order::PAYMENT_REFUNDED,
                    ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    Order::STATUS_PENDING => 'Pending',
                    Order::STATUS_PROCESSING => 'Processing',
                    Order::STATUS_SHIPPED => 'Shipped',
                    Order::STATUS_DELIVERED => 'Delivered',
                    Order::STATUS_CANCELLED => 'Cancelled',
                ]),
                Tables\Filters\SelectFilter::make('payment_status')->options([
                    Order::PAYMENT_UNPAID => 'Unpaid',
                    Order::PAYMENT_PAID => 'Paid',
                    Order::PAYMENT_FAILED => 'Failed',
                    Order::PAYMENT_REFUNDED => 'Refunded',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
