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
use Illuminate\Support\Facades\Log;


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
            'redirect_url' => route('wompi.success', [
                'plan' => $plan->id,
                // opcional si lo necesitas:
                'uid'  => Auth::id(),
            ]),
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

    //http://127.0.0.1:8000/wompi-success?id=1127623-1754757775-30907&env=test
    // 0) Traer datos de la compra guardados antes de redirigir
    // 0) Tomar plan_id y user_id primero de la URL y como fallback de session
    $planId = (int) $request->query('plan', 0);
    $userId = (int) $request->query('uid', 0);

    $data = session('data', []);
    $planId = $planId ?: ($data['plan_id'] ?? 0);
    $userId = $userId ?: ($data['user_id'] ?? 0);

    if (!$planId || !$userId) {
        Log::warning('Wompi success sin contexto de compra', [
            'qs' => $request->query(), 'session' => $data
        ]);
        return $this->failed($request);
    }

    $transactionId = $request->query('id');
    if (!$transactionId) {
        Log::warning('Wompi redirect sin id', ['query' => $request->query()]);
        return $this->failed($request);
    }

    $publicKey    = getSuperAdminSettingKeyValue('wompi_public_key');
    $privateKey   = getSuperAdminSettingKeyValue('wompi_private_key');
    $eventsSecret = getSuperAdminSettingKeyValue('wompi_events_secret');

    \Bancolombia\Wompi::initialize([
        'public_key'        => $publicKey,
        'private_key'       => $privateKey,
        'private_event_key' => $eventsSecret,
    ]);

    
    // 2) Consultar la transacción en Wompi
    $trxResp = Wompi::transaction($transactionId);

    // 3) Normalizar la respuesta si viene como string JSON
    if (is_string($trxResp)) {
        $decoded = json_decode($trxResp);
        if (json_last_error() === JSON_ERROR_NONE) {
            $trxResp = $decoded;
        } else {
            Log::error('Wompi transaction JSON inválido', [
                'raw'   => substr($trxResp, 0, 500),
                'error' => json_last_error_msg(),
            ]);
            return $this->failed($request);
        }
    }

    // 4) Validar estructura y estado
    $trx = $trxResp->data ?? null;
    if (!is_object($trx)) {
        Log::warning('Wompi transaction vacía o inesperada', ['resp' => $trxResp]);
        return $this->failed($request);
    }
    if (($trx->status ?? null) !== 'APPROVED') {
        Log::info('Wompi transaction no APPROVED', ['status' => $trx->status ?? null]);
        return $this->failed($request);
    }

    // 5) Calcular monto seguro
    $amountInCents = (int) ($trx->amount_in_cents ?? 0);
    $amount        = (int) round($amountInCents / 100);

    // 6) Cargar plan
    $plan = SubscriptionPlan::find($data['plan_id']);
    if (!$plan) {
        Log::error('Plan no encontrado', ['plan_id' => $data['plan_id']]);
        return $this->failed($request);
    }

    // 7) Persistir transacción y crear suscripción
    try {
        DB::beginTransaction();

        $transaction = Transaction::create([
            'transaction_id' => (string) ($trx->id ?? ''),
            'payment_type'   => Transaction::TYPE_WOMPI,   // asegúrate que exista la constante
            'amount'         => $amount,
            'status'         => Subscription::ACTIVE,       // o el status que uses para pagos
            'user_id'        => $data['user_id'],
            'meta'           => json_encode($trxResp, JSON_UNESCAPED_UNICODE),
        ]);

        $payload = [
            'plan'           => $plan->toArray(),
            'user_id'        => $data['user_id'],
            'payment_type'   => Subscription::TYPE_WOMPI,  // si usas esta constante, que exista
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
    } catch (\Throwable $ex) {
        DB::rollBack();
        Log::error('Error creando suscripción tras Wompi', ['e' => $ex]);
        return $this->failed($request);
    }

    return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
}



    public function failed(Request $request)
    {
        Notification::make()->danger()->title(__('messages.payment.payment_failed'))->send();
        return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
    }
}
