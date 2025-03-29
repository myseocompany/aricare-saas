<?php

namespace App\Repositories;

use Exception;
use App\Models\Sms;
use App\Models\User;
use Twilio\Rest\Client;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use PragmaRX\Countries\Package\Services\Countries;
use Twilio\Exceptions\ConfigurationException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class SmsRepository
 */
class SmsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user',
        'message',
    ];

    /**
     * @return array|string[]
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Sms::class;
    }

    public function store($input, $action)
    {
        if (isset($input['prefix_code'])) {
            $regionCode = getCountryCode($input['prefix_code']) ?? null;
        }

        if ($input['number_directly'] == false) {
            $userMobile = User::whereIn('id', $input['send_to'])->pluck('phone', 'id');
            foreach ($userMobile as $key => $phone) {
                $this->sendSMS($key, null, $phone, $input['message'], $action);
            }
        } else {
            $this->sendSMS(null, $regionCode, $input['phone'], $input['message'], $action);
        }
    }

    /**
     * @throws ConfigurationException
     */
    public function sendSMS($sendTo, $regionCode, $phone, $message, $action)
    {
        try {
            $sid = config('twilio.sid');
            $token = config('twilio.token');
            $client = new Client($sid, $token);

            $sms = Sms::create([
                'send_to' => $sendTo,
                'region_code' => $regionCode,
                'phone_number' => $phone,
                'message' => $message,
                'send_by' => Auth::user()->id,
            ]);

            $smsLimit = Subscription::whereUserId(getLoggedInUserId())->whereStatus(1)->value('sms_limit');
            Subscription::whereUserId(getLoggedInUserId())->whereStatus(1)->update([
                'sms_limit' => $smsLimit - 1,
            ]);

            $client->messages->create(
                substr($phone, 0, 1) == '+' ? $phone : '+' . $sms->region_code . $sms->phone_number,
                [
                    'from' => config('twilio.from_number'),
                    'body' => $message,
                ]
            );
        } catch (Exception $e) {
            // throw new UnprocessableEntityHttpException($e->getMessage());
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
            $action->halt;
        }
    }
}
