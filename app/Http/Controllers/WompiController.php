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

        $session = [
            'user_id'   => Auth::id(),
            'plan_id'   => $plan->id,
            'reference' => (string) Str::uuid(),
        ];
        session(['data' => $session]);

        $publicKey    = getSuperAdminSettingKeyValue('wompi_public_key');
        $privateKey   = getSuperAdminSettingKeyValue('wompi_private_key');
        $eventsSecret = getSuperAdminSettingKeyValue('wompi_events_secret');

        // Inicializa (opcional si usas RestClient directo)
        Wompi::initialize([
            'public_key'        => $publicKey,
            'private_key'       => $privateKey,
            'private_event_key' => $eventsSecret,
        ]);

        // Usa RestClient directamente para poder inspeccionar la respuesta
        $client = new RestClient([
            'public_key'        => $publicKey,
            'private_key'       => $privateKey,
            'private_event_key' => $eventsSecret,
        ]);

        $payload = [
            'name'             => $plan->name,
            'description'      => 'Suscripción ' . ($plan->name ?? 'Plan'),
            'single_use'       => true,
            'amount_in_cents'  => (int) ($plan->payable_amount * 100),
            'currency'         => strtoupper($plan->currency->code ?? 'COP'),
            'redirect_url'     => route('wompi.success'),
            'reference'        => $session['reference'],
            'customer_email'   => Auth::user()->email,
        ];

  $result = $client->post('/payment_links', $payload);

// Log completo para depurar
\Log::channel('daily')->info('Wompi /payment_links', ['result' => $result]);

// Mensaje claro para el usuario (si viene error)
if (!isset($result->data->id)) {
    $msg = $result->error->messages[0]->text
        ?? $result->error->reason
        ?? (is_string($result) ? $result : json_encode($result));

    Notification::make()->danger()->title($msg ?: 'Error creando link de pago')->send();
    return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
}

return redirect("https://checkout.wompi.co/l/{$result->data->id}");


        // Si llegamos acá, la API devolvió error. Muéstralo y no intentes leer ->data
        $msg = $result->error->reason ?? $result->error->messages[0]->text ?? 'Error creando link de pago';
        Notification::make()->danger()->title($msg)->send();
        return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');

    } catch (\Throwable $e) {
        \Log::error('Wompi exception', ['e' => $e]);
        Notification::make()->danger()->title($e->getMessage())->send();
        return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
    }
}


    public function success(Request $request)
{


    $data = session('data');
    if (!$data) {
        return $this->failed($request);
    }

    $plan = SubscriptionPlan::find($data['plan_id']);

    // Re-inicializa por si el proceso cambió de request
    Wompi::initialize([
        'public_key'        => getSuperAdminSettingKeyValue('wompi_public_key'),
        'private_key'       => getSuperAdminSettingKeyValue('wompi_private_key'),
        'private_event_key' => getSuperAdminSettingKeyValue('wompi_events_secret'),
    ]);

    // Consulta por reference (lo más seguro tras el redirect)
    $result = Wompi::find_transaction($data['reference']); // stdClass o null
    if (!$result || empty($result->data[0])) {
        \Log::warning('Wompi find_transaction vacío', ['result' => $result]);
        return $this->failed($request);
    }

    $trx = $result->data[0];
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
            'meta'           => json_encode($result),
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
