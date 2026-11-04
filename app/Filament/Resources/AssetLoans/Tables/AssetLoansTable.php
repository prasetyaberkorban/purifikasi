<?php

namespace App\Filament\Resources\AssetLoans\Tables;

use App\Enums\AssetCondition;
use App\Enums\LoanStatus;
use App\Models\AssetLoan;
use App\Services\LoanService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use RuntimeException;

class AssetLoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('borrowed_at', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Pinjam')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('sm'),
                TextColumn::make('asset.name')
                    ->label('Barang')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn (AssetLoan $record) => $record->asset?->code),
                TextColumn::make('borrower.name')
                    ->label('Peminjam')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('borrowed_at')
                    ->label('Pinjam')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label('Tenggat')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (AssetLoan $record) => $record->isOverdue() ? 'danger' : null),
                TextColumn::make('returned_at')
                    ->label('Kembali')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (LoanStatus $state) => $state->label())
                    ->color(fn (LoanStatus $state) => $state === LoanStatus::Dipinjam ? 'info' : 'success'),
                TextColumn::make('handler.name')
                    ->label('Petugas')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(LoanStatus::options()),
            ])
            ->recordActions([
                Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-down-left')
                    ->color('success')
                    ->visible(fn (AssetLoan $record) => $record->status === LoanStatus::Dipinjam)
                    ->form([
                        Select::make('condition_on_return')
                            ->label('Kondisi Saat Dikembalikan')
                            ->options(AssetCondition::options())
                            ->default(AssetCondition::Baik->value)
                            ->required(),
                        Textarea::make('note')
                            ->label('Catatan')
                            ->rows(2),
                    ])
                    ->action(function (AssetLoan $record, array $data): void {
                        try {
                            app(LoanService::class)->returnLoan(
                                $record,
                                auth()->user(),
                                $data['condition_on_return'],
                                $data['note'] ?? null,
                            );

                            Notification::make()
                                ->title("Pengembalian {$record->code} tercatat")
                                ->success()
                                ->send();
                        } catch (RuntimeException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}