<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Filament\Notifications\Notification;
use App\Actions\Subscription\CreateSubscription;
use Symfony\Component\HttpKernel\Exception\HttpException;


use Bancolombia\Wompi;
use Bancolombia\RestClient;
use Illuminate\Support\Str;

class WompiController extends Controller
{

public function purchase(Request $request)
{
    try {
        $plan = json_decode($request->plan);
        if (!$plan || !isset($plan->id, $plan->name, $plan->payable_amount)) {
            throw new \RuntimeException('Plan inválido.');
        }

        $session = [
            'user_id'   => Auth::id(),
            'plan_id'   => $plan->id,
        ];
        session(['data' => $session]);

        $publicKey    = getSuperAdminSettingKeyValue('wompi_public_key');
        $privateKey   = getSuperAdminSettingKeyValue('wompi_private_key');
        $eventsSecret = getSuperAdminSettingKeyValue('wompi_events_secret');

        \Bancolombia\Wompi::initialize([
            'public_key'        => $publicKey,
            'private_key'       => $privateKey,
            'private_event_key' => $eventsSecret,
        ]);

        $client = new \Bancolombia\RestClient([
            'public_key'        => $publicKey,
            'private_key'       => $privateKey,
            'private_event_key' => $eventsSecret,
        ]);

        // ⚠️ NO dd() aquí
        $amountInCents = (int) round($plan->payable_amount * 100);

        $payload = [
            'amount_in_cents'  => $amountInCents,
            'currency'         => 'COP',
            'name'             => $plan->name,
            'description'      => 'Suscripción ' . ($plan->name ?? 'Plan'),
            'single_use'       => true,
            'collect_shipping' => false,
            'redirect_url'     => route('wompi.success'),
        ];

\Log::info('Wompi keys', [
  'pub_first6' => substr($publicKey ?? '', 0, 6),
  'prv_first6' => substr($privateKey ?? '', 0, 6),
]);

// ping público: no requiere auth, pero tu cliente enviará el bearer igual
$merchant = $client->get('/merchants/' . $publicKey);
\Log::info('Wompi /merchants resp', ['merchant' => $merchant]);


$result = $client->post('/payment_links', $payload);
\Log::info('Wompi /payment_links', ['payload' => $payload, 'result' => $result]);

if (!$result || !isset($result->data->id)) {
    $msg = 'Error creando link de pago';
    \Log::error('Wompi /post', ['msg' => $msg]);
    if (is_object($result) && isset($result->error)) {
        $msg = $result->error->messages[0]->text ?? ($result->error->reason ?? $msg);
        \Log::error('Wompi /post con detalle', ['msg' => $msg]);
    }
    Notification::make()->danger()->title($msg)->send();
    return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
}

return redirect("https://checkout.wompi.co/l/{$result->data->id}");


    } catch (\Throwable $e) {
        \Log::error('Wompi exception', ['e' => $e]);
        \Filament\Notifications\Notification::make()->danger()->title($e->getMessage())->send();
       return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
    }
}



    public function success(Request $request)
{
    $data = session('data');
    if (!$data) return $this->failed($request);

    $plan = SubscriptionPlan::find($data['plan_id']);

    Wompi::initialize([
        'public_key'        => getSuperAdminSettingKeyValue('wompi_public_key'),
        'private_key'       => getSuperAdminSettingKeyValue('wompi_private_key'),
        'private_event_key' => getSuperAdminSettingKeyValue('wompi_events_secret'),
    ]);

    $transactionId = $request->query('id');
    if (!$transactionId) {
        \Log::warning('Wompi redirect sin id', ['query' => $request->query()]);
        return $this->failed($request);
    }

    $trxResp = Wompi::transaction($transactionId); // o el método equivalente del wrapper
    if (!$trxResp || empty($trxResp->data)) {
        \Log::warning('Wompi transaction vacía', ['resp' => $trxResp]);
        return $this->failed($request);
    }

    $trx = $trxResp->data;
    if (($trx->status ?? null) !== 'APPROVED') {
        return $this->failed($request);
    }

    $amount = (int)($trx->amount_in_cents ?? 0) / 100;

    try {
        DB::beginTransaction();

        $transaction = Transaction::create([
            'transaction_id' => $trx->id ?? '',
            'payment_type'   => Transaction::TYPE_WOMPI,
            'amount'         => $amount,
            'status'         => Subscription::ACTIVE,
            'user_id'        => $data['user_id'],
            'meta'           => json_encode($trxResp),
        ]);

        $payload = [
            'plan'           => $plan->toArray(),
            'user_id'        => $data['user_id'],
            'payment_type'   => Subscription::TYPE_WOMPI,
            'transaction_id' => $transaction->id,
        ];

        $subscription = CreateSubscription::run($payload);
        DB::commit();

        if ($subscription) {
            setPlanFeatures();
            Notification::make()
                ->success()
                ->title(getLoggedInUser()->first_name . ' ' . __('messages.new_change.subscribed_success'))
                ->send();
        }

    } catch (HttpException $ex) {
        DB::rollBack();
        throw $ex;
    }

    return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
}


    public function failed(Request $request)
    {
        Notification::make()->danger()->title(__('messages.payment.payment_failed'))->send();
        return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
    }
}
