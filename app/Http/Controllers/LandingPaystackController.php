<?php

namespace App\Http\Controllers;

use Exception;
use Laracasts\Flash\Flash;
use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\SubscriptionRepository;
use Unicodeveloper\Paystack\Facades\Paystack;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use App\Mail\NotifyMailSuperAdminForSubscribeHospital;

class LandingPaystackController extends Controller
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * PaypalController constructor.
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function paystackConfig()
    {
        config([
            'paystack.publicKey' => getSuperAdminPaymentCredentials('paystack_key'),
            'paystack.secretKey' => getSuperAdminPaymentCredentials('paystack_secret'),
            'paystack.paymentUrl' => 'https://api.paystack.co',
        ]);
    }

    public function redirectToGateway(Request $request)
    {
        $this->paystackConfig();

        $subscriptionsPricingPlan = SubscriptionPlan::findOrFail($request->get('planId'));

        $data = $this->subscriptionRepository->manageSubscription($request->get('planId'));

        if (! isset($data['plan'])) { // 0 amount plan or try to switch the plan if it is in trial mode
            // returning from here if the plan is free.
            if (isset($data['status']) && $data['status'] == true) {
                return $this->sendSuccess($data['subscriptionPlan']->name . ' ' . __('messages.subscription_pricing_plans.has_been_subscribed'));
            } else {
                if (isset($data['status']) && $data['status'] == false) {
                    return $this->sendError(__('messages.flash.cannot_switch'));
                }
            }
        }

        $subscriptionsPricingPlan = $data['plan'];
        $subscription = $data['subscription'];

        try {
            $request->merge([
                'email' => getLoggedInUser()->email,
                'orderID' => $subscription->id,
                'amount' => ($data['amountToPay'] * 100),
                'quantity' => 1,
                'currency' => strtoupper(getCurrentCurrency()),
                'reference' => Paystack::genTranxRef(),
                'metadata' => json_encode(['subscription_id' => $subscription->id] + ['is_landing_subscription' => true]),
            ]);
            $authorizationUrl = Paystack::getAuthorizationUrl();

            return $authorizationUrl->redirectNow();
        } catch (\Exception $e) {
            Flash::error(__('messages.new_change.payment_fail'));

            return Redirect::back()->withMessage([
                'msg' => __('messages.new_change.paystack_token_expired'),
                'type' => 'error',
            ]);
        }
    }
}
