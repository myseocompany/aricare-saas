<?php

namespace App\Filament\Clusters\Billings\Resources\TransactionResource\Pages;

use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Clusters\Billings\Resources\TransactionResource;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
    public function changePaymentStatus($record, $state)
    {
        $transaction = Transaction::with('transactionSubscription', 'user')->findOrFail($record);
        if ($state == Transaction::APPROVED) {
            $subscription = $transaction->transactionSubscription;
            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update([
                    'is_manual_payment' => $state,
                    'status' => Subscription::ACTIVE,
                    'tenant_id' => $transaction->user->tenant_id,
                ]);

            Subscription::findOrFail($subscription->id)->update(['status' => Subscription::ACTIVE]);
            // De-Active all other subscription
            Subscription::whereUserId($subscription->user_id)
                ->where('id', '!=', $subscription->id)
                ->update([
                    'status' => Subscription::INACTIVE,
                ]);

            $subscription->update(['status', Subscription::ACTIVE]);

            $mailData = [
                'amount' => $subscription->plan_amount,
                'user_name' => $subscription->user->full_name,
                'plan_name' => $subscription->subscriptionPlan->name,
                'start_date' => $subscription->starts_at,
                'end_date' => $subscription->ends_at,
            ];

            Notification::make()
                ->title(__('messages.flash.manual_payment_approved'))
                ->success()
                ->send();
        } else {
            if ($state == Transaction::DENIED) {
                $subscription = $transaction->transactionSubscription;

                DB::table('transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'is_manual_payment' => $state,
                        'status' => Subscription::INACTIVE,
                        'tenant_id' => $transaction->user->tenant_id,
                    ]);
                $subscription->delete();

                $this->resetTable();
            }
        }
    }
}
