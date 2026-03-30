<?php

namespace App\Filament\App\Resources\ItemCatalogues\Tables;

use App\Models\Loan;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ItemCataloguesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 5,
            ])
            ->columns([
                self::getTableColumns(),
            ])
            ->filters([
                self::getTableFilters(),
            ])
            ->recordActions([
                //
            ])
            ->actions([
                ViewAction::make(),
                self::addToCartAction(),
            ])
            ->headerActions([
                self::getCart()
                    ->action(self::requestAction()),
            ])
            ->toolbarActions([
                //
            ]);
    }

    protected static function getTableColumns()
    {
        return Stack::make([
                    ImageColumn::make('image_path')
                        ->imageSize(200)
                        ->defaultImageUrl('/images/no-image.png')
                        ->alignCenter(),
                    TextColumn::make('category.name')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('name')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('available_stock')
                        ->formatStateUsing(function ($record): string 
                        {
                            if ($record->available_stock > 1) {
                                return $record->available_stock . ' units available';
                            } else if ($record->available_stock == 1) {
                                return $record->available_stock . ' unit available';
                            }
                            else {
                                return 'Out of Stock';
                            }
                        })
                        ->badge(fn($record): string => $record->available_stock === 0)
                        ->color(fn($record): string => $record->available_stock > 0 ? 'success' : 'danger')
                ])
                ->space(2);
    }

    protected static function getTableFilters()
    {
        return TernaryFilter::make('available')
                    ->label('Availability')
                    ->placeholder('All Items')
                    ->trueLabel('Available')
                    ->falseLabel('Unavailable')
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('units', fn($q) => $q->where('status', 'available')),
                        false: fn(Builder $query) => $query->whereDoesntHave('units', fn($q) => $q->where('status', 'available')),
                    )
                    ->default(true);
    }

    protected static function addToCartAction()
    {
        return Action::make('addToCart')
                    ->label('Add to Cart')
                    ->icon('heroicon-m-shopping-cart')
                    ->color('success')
                    ->visible(fn($record) => $record->available_stock > 0)
                    ->form([
                        TextInput::make('qty_request')
                            ->label('Request Quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(fn($record) => $record->available_stock)
                            ->required()
                            ->helperText(fn($record) => "Available: {$record->available_stock}" . str(' unit')->plural($record->available_stock)),
                    ])
                    ->action(function ($record, array $data) {
                        $cart = session()->get('cart', []);
                        $cart[$record->id] = [
                            'id' => $record->id,
                            'name' => $record->name,
                            'qty_request' => $data['qty_request'],
                            'available_stock' => $record->available_stock,
                        ];
                        session()->put('cart', $cart);

                        Notification::make()
                            ->title('Successfully added to cart')
                            ->success()
                            ->send();
                    });
    }

    protected static function getCart()
    {
        return Action::make('viewCart')
                    ->label(fn() => 'View Cart (' . count(session()->get('cart', [])) . ')')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('primary')
                    ->modalHeading('Cart')
                    ->modalSubmitActionLabel('Request')
                    ->fillForm(fn() => [
                        'items' => array_values(session()->get('cart', [])),
                        'user_id' => auth()->id(), 
                    ])
                    ->form([
                        Section::make('Loan Details')
                            ->columns(2)
                            ->schema([
                                Hidden::make('user_id'), 
                                DatePicker::make('due_at') 
                                    ->label('Return Date')
                                    ->required()
                                    ->native(false)
                                    ->minDate(now()),
                                Textarea::make('reason') 
                                    ->label('Reason')
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Item List')
                            ->schema([
                                Repeater::make('items')
                                    ->label('')
                                    ->schema([
                                        Hidden::make('id'),
                                        TextInput::make('name')
                                            ->label('Item Name')
                                            ->disabled(),
                                        TextInput::make('qty_request')
                                            ->label('Quantity')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1)
                                            ->maxValue(fn($get) => $get('available_stock'))
                                            ->helperText(fn($get) => "Available: {$get('available_stock')}" . str(' unit')->plural($get('available_stock'))),
                                    ])
                                    ->addable(false) 
                                    ->deletable(true)
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $newCart = [];
                                        foreach ($state as $item) {
                                            $newCart[$item['id']] = $item;
                                        }
                                        session()->put('cart', $newCart);
                                    })
                                    ->columns(2),
                            ]),
                    ]);
    }

    protected static function requestAction()
    {
        // Kita harus mengembalikan Closure agar Filament bisa menyuntikkan array $data
        return function (array $data) {
            if (empty($data['items'])) {
                Notification::make()->title('Your cart is empty!')->danger()->send();
                return;
            }

            try {
                DB::transaction(function () use ($data) {
                    $loan = Loan::create([
                        'user_id' => $data['user_id'],
                        'due_at'  => $data['due_at'],
                        'reason'  => $data['reason'],
                        'status'  => 'pending', // Pastikan ada status default
                    ]);

                    foreach ($data['items'] as $item) {
                        $loan->loanItems()->create([
                            'item_id'     => $item['id'],
                            'qty_request' => $item['qty_request'],
                        ]);
                    }
                });

                session()->forget('cart');
                
                Notification::make()
                    ->title('Your Request has been sent')
                    ->success()
                    ->send();

            } catch (Exception $e) {
                Notification::make()
                    ->title('Failed to send request')
                    ->body($e->getMessage())
                    ->danger()
                    ->persistent()
                    ->send();
            }
        };
    }
}