<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\IpdPayment;
use Filament\Tables\Table;
use App\Models\IpdTimeline;
use Filament\Tables\Actions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class IpdPatientBillPaymentTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $record;
    public $id;
    public $processedData = [];

    public function mount()
    {
        $this->id = Route::current()->parameter('record');
    }

    public function GetRecord()
    {
        $IpdPayment = IpdPayment::whereIpdPatientDepartmentId($this->id)->orderBy('id', 'desc');
        return $IpdPayment;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('messages.payments'))
            ->query($this->GetRecord())
            ->columns([
                TextColumn::make('payment_mode')
                    ->label(__('messages.ipd_payments.payment_mode'))
                    ->formatStateUsing(fn($state) => getIpdPaymentTypes()[$state]),
                TextColumn::make('date')
                    ->label(__('messages.ipd_patient_charges.date'))
                    ->default(__('messages.common.n/a'))
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->translatedFormat('jS M, Y')),
                TextColumn::make('amount')
                    ->label(__('messages.ambulance_call.amount'))
                    ->formatStateUsing(function ($record) {
                        if (!empty($record->amount)) {
                            return getCurrencyFormat($record->amount);
                        } else {
                            return __('messages.common.n/a');
                        }
                    })
                    ->alignEnd()
                    ->summarize([
                        Sum::make('amount')
                            ->label('')
                            ->formatStateUsing(fn($state) => getCurrencyFormat($state)),
                    ])
                    ->formatStateUsing(fn($state) => getCurrencySymbol() . $state),
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }


    public function render()
    {
        return view('livewire.ipd-patient-bill-payment-table');
    }
}
