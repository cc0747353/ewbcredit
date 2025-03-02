<?php

namespace App\Filament\Resources;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions;
use Illuminate\Support\Collection;


class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('payable_name')
                ->label('User Name')
                ->required()
                ->default('Default User Name'),
            Forms\Components\TextInput::make('wallet_id')
                ->label('Wallet ID')
                ->required()
                ->numeric(),
            Forms\Components\Select::make('type')
                ->label('Type')
                ->options([
                    'withdraw' => 'Withdraw',
                    'deposit' => 'Deposit',
                ])
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->label('Amount')
                ->required()
                ->numeric()
                ->minValue(0),
            Forms\Components\Toggle::make('confirmed')
                ->label('Confirmed'),
            Forms\Components\DateTimePicker::make('created_at')
                ->label('Created At')
                ->required(),
            Forms\Components\DateTimePicker::make('updated_at')
                ->label('Updated At')
                ->required(),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payable.name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('wallet_id')
                    ->label('Wallet ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => '$' . number_format($state, 2)),
                BooleanColumn::make('confirmed')
                    ->label('Confirmed')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->searchable()
                    ->sortable()
                    ->dateTime('F j, Y, g:i a'),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->searchable()
                    ->sortable()
                    ->dateTime('F j, Y, g:i a'),
            ])
            ->filters([
                Filter::make('confirmed')
                    ->query(fn (Builder $query): Builder => $query->where('confirmed', true))
                    ->label('Confirmed Transactions'),
                Filter::make('unconfirmed')
                    ->query(fn (Builder $query): Builder => $query->where('confirmed', false))
                    ->label('Unconfirmed Transactions'),
                Filter::make('recent')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subMonth()))
                    ->label('Recent Transactions'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\Action::make('view')
                //     ->label('View')
                //     ->url(fn (Transaction $record): string => route('transactions.show', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('confirm')
                        ->label('Confirm')
                        ->action(fn (Collection $records) => $records->each->update(['confirmed' => true])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
