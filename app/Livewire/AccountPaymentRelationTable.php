<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Payment;
use Livewire\Component;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class AccountPaymentRelationTable extends Component implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $record;

    public function GetRecord()
    {
        $id = Route::current()->parameter('record');

        $payments = Account::with('payments')->where('id', $id)->get();

        foreach ($payments as $item) {
            $this->record = $item->payments;
        }

        $payment_ids = $this->record->pluck('account_id')->toArray();

        $data = Payment::whereIn('account_id', $payment_ids)->orderBy('id', 'desc');

        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Self::GetRecord())
            ->paginated([10,25,50])
            ->columns([
                TextColumn::make('payment_date')
                    ->label(__('messages.payment.payment_date'))
                    ->formatStateUsing(fn($record) =>  \Carbon\Carbon::parse($record->payment_date)->translatedFormat('jS M, Y'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('messages.payment.description'))
                    ->words(10)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pay_to')
                    ->searchable()
                    ->sortable()
                    ->label(__('messages.payment.pay_to')),
                TextColumn::make('status')
                    ->label(__('messages.payment.amount'))
                    ->getStateUsing(fn($record) => getCurrencyFormat($record->amount))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public function render()
    {
        return view('livewire.account-payment-relation-table');
    }
}
