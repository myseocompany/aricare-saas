<?php
/*




*/
namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Models\SuperAdminSetting;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Support\Arr;
use Filament\Forms\Get;

class PaymentGatway extends Page
{
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static string $view = 'filament.clusters.settings.pages.payment-gatway';

    public ?array $data = [];

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Settings::class;

    protected static ?string $title = "";

    public static function getNavigationLabel(): string
    {
        return __('messages.setting.payment_gateway');
    }


    public function mount()
    {
        $keys = [
            'stripe_enable','stripe_key','stripe_secret',
            'paypal_enable','paypal_key','paypal_secret',
            'razorpay_enable','razorpay_key','razorpay_secret',
            'paystack_enable','paystack_key','paystack_secret',
            'phonepe_enable','phonepe_merchant_id','phonepe_merchant_user_id','phonepe_env',
            'phonepe_salt_key','phonepe_salt_index','phonepe_merchant_transaction_id',
            'flutterwave_enable','flutterwave_key','flutterwave_secret',
            // ---- Wompi ----
            'wompi_enable','wompi_public_key','wompi_private_key','wompi_events_secret', 'wompi_integrity_secret','wompi_env'
        ];
        $settingsData = SuperAdminSetting::select('key', 'value')->whereIn('key', $keys)->get()->keyBy('key')->toArray();
        $this->form->fill($settingsData + ['manual_payment_enabled' => ['value' => true]]);
    }
    public function getTitle(): string
    {
        return '';
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        // Wompi
                        Toggle::make('wompi_enable.value')
    ->live()
    ->label('Wompi:'),

Group::make()
    ->schema([
        TextInput::make('wompi_env.value')
            ->label('Ambiente (test|prod)')
            ->placeholder('test o prod')
            ->helperText('Usa "test" para pruebas y "prod" para producción.')
            ->requiredIf('wompi_enable.value', true),

        TextInput::make('wompi_public_key.value')
            ->label('Public Key')
            ->placeholder('pub_test_xxx / pub_prod_xxx')
            ->requiredIf('wompi_enable.value', true),

        TextInput::make('wompi_private_key.value')
            ->label('Private Key')
            ->password()
            ->revealable()
            ->requiredIf('wompi_enable.value', true),

        TextInput::make('wompi_events_secret.value')
            ->label('Events Secret (Webhooks)')
            ->password()
            ->revealable()
            ->helperText('Se usa para verificar la firma de los eventos.')
            ->requiredIf('wompi_enable.value', true),
        TextInput::make('wompi_integrity_secret.value')
            ->label('Integridad (Secret)')
            ->password()
            ->revealable()
            ->helperText('Se usa para verificar la integridad del checkout/pagos.')
            ->requiredIf('wompi_enable.value', true),
    ])
    ->columns(2)
    ->visible(fn (Get $get) => (bool) $get('wompi_enable.value')),
                        // Fin Wompi
                        Toggle::make('stripe_enable.value')
                            ->live()
                            ->label(__('messages.setting.stripe') . ':'),
                        Group::make()
                            ->schema([
                                TextInput::make('stripe_key.value')
                                    ->label(__('messages.setting.stripe_key') . ':')
                                    ->placeholder(__('messages.setting.stripe_key'))
                                    ->validationAttribute(__('messages.setting.stripe_key'))
                                    ->required(),
                                TextInput::make('stripe_secret.value')
                                    ->label(__('messages.setting.stripe_secret') . ':')
                                    ->placeholder(__('messages.setting.stripe_secret'))
                                    ->validationAttribute(__('messages.setting.stripe_secret'))
                                    ->required(),
                            ])->columns(2)->visible(function (callable $get) {
                                if ($get('stripe_enable.value')) {
                                    return true;
                                }
                                return false;
                            }),

                        Toggle::make('paypal_enable.value')
                            ->live()
                            ->label(__('messages.setting.paypal') . ':'),
                        Group::make()
                            ->schema([
                                TextInput::make('paypal_key.value')
                                    ->label(__('messages.setting.paypal_client_id'))
                                    ->placeholder(__('messages.setting.paypal_client_id'))
                                    ->validationAttribute(__('messages.setting.paypal_client_id'))
                                    ->required(),
                                TextInput::make('paypal_secret.value')
                                    ->label(__('messages.setting.paypal_secret'), ':')
                                    ->placeholder(__('messages.setting.paypal_secret'))
                                    ->validationAttribute(__('messages.setting.paypal_secret'))
                                    ->required(),
                            ])->columns(2)->visible(function (callable $get) {
                                if ($get('paypal_enable.value')) {
                                    return true;
                                }
                                return false;
                            }),

                        Toggle::make('razorpay_enable.value')
                            ->live()
                            ->label(__('messages.setting.razorpay') . ':'),
                        Group::make()
                            ->schema([
                                TextInput::make('razorpay_key.value')
                                    ->label(__('messages.setting.razorpay_key'))
                                    ->placeholder(__('messages.setting.razorpay_key'))
                                    ->validationAttribute(__('messages.setting.razorpay_key'))
                                    ->required(),
                                TextInput::make('razorpay_secret.value')
                                    ->label(__('messages.setting.razorpay_secret') . ':')
                                    ->placeholder(__('messages.setting.razorpay_secret'))
                                    ->validationAttribute(__('messages.setting.razorpay_secret'))
                                    ->required(),
                            ])->columns(2)->visible(function (callable $get) {
                                if ($get('razorpay_enable.value')) {
                                    return true;
                                }
                                return false;
                            }),

                        Toggle::make('paystack_enable.value')
                            ->live()
                            ->label(__('messages.setting.paystack') . ':'),
                        Group::make()
                            ->schema([
                                TextInput::make('paystack_key.value')
                                    ->label(__('messages.setting.paystack_public_key') . ':')
                                    ->placeholder(__('messages.setting.paystack_public_key'))
                                    ->validationAttribute(__('messages.setting.paystack_public_key'))
                                    ->required(),
                                TextInput::make('paystack_secret.value')
                                    ->label(__('messages.setting.paystack_secret_key') . ':')
                                    ->placeholder(__('messages.setting.paystack_secret_key'))
                                    ->validationAttribute(__('messages.setting.paystack_secret_key'))
                                    ->required(),
                            ])->columns(2)->visible(function (callable $get) {
                                if ($get('paystack_enable.value')) {
                                    return true;
                                }
                                return false;
                            }),

                        Toggle::make('phonepe_enable.value')
                            ->live()
                            ->label(__('messages.phonepe.phonepe') . ':'),
                        Group::make()
                            ->schema([
                                TextInput::make('phonepe_merchant_id.value')
                                    ->label(__('messages.phonepe.phonepe_merchant_id') . ':')
                                    ->placeholder(__('messages.phonepe.phonepe_merchant_id'))
                                    ->validationAttribute(__('messages.phonepe.phonepe_merchant_id'))
                                    ->required(),
                                TextInput::make('phonepe_merchant_user_id.value')
                                    ->label(__('messages.phonepe.phonepe_merchant_user_id') . ':')
                                    ->placeholder(__('messages.phonepe.phonepe_merchant_user_id'))
                                    ->validationAttribute(__('messages.phonepe.phonepe_merchant_user_id'))
                                    ->required(),
                            ])->columns(2)->visible(function (callable $get) {
                                if ($get('phonepe_enable.value')) {
                                    return true;
                                }
                                return false;
                            }),
                        Group::make()
                            ->schema([
                                TextInput::make('phonepe_env.value')
                                    ->label(__('messages.phonepe.phonepe_env') . ':')
                                    ->placeholder(__('messages.phonepe.phonepe_env'))
                                    ->validationAttribute(__('messages.phonepe.phonepe_env'))
                                    ->required(),
                                TextInput::make('phonepe_salt_key.value')
                                    ->label(__('messages.phonepe.phonepe_salt_key') . ':')
                                    ->placeholder(__('messages.phonepe.phonepe_salt_key'))
                                    ->validationAttribute(__('messages.phonepe.phonepe_salt_key'))
                                    ->required(),
                            ])->columns(2)->visible(function (callable $get) {
                                if ($get('phonepe_enable.value')) {
                                    return true;
                                }
                                return false;
                            }),

                        Group::make()
                            ->schema([
                                TextInput::make('phonepe_salt_index.value')
                                    ->label(__('messages.phonepe.phonepe_salt_index') . ':')
                                    ->placeholder(__('messages.phonepe.phonepe_salt_index'))
                                    ->validationAttribute(__('messages.phonepe.phonepe_salt_index'))
                                    ->required(),
                                TextInput::make('phonepe_merchant_transaction_id.value')
                                    ->label(__('messages.phonepe.phonepe_merchant_transaction_id'), ':')
                                    ->placeholder(__('messages.phonepe.phonepe_merchant_transaction_id'))
                                    ->validationAttribute(__('messages.phonepe.phonepe_merchant_transaction_id'))
                                    ->required(),
                            ])->columns(2)->visible(function (callable $get) {
                                if ($get('phonepe_enable.value')) {
                                    return true;
                                }
                                return false;
                            }),

                        Toggle::make('flutterwave_enable.value')
                            ->live()
                            ->label(__('messages.flutterwave.flutterwave') . ':'),
                        Group::make()
                            ->schema([
                                TextInput::make('flutterwave_key.value')
                                    ->label(__('messages.flutterwave.flutterwave_public_key') . ':')
                                    ->placeholder(__('messages.flutterwave.flutterwave_public_key'))
                                    ->validationAttribute(__('messages.flutterwave.flutterwave_public_key'))
                                    ->required(),
                                TextInput::make('flutterwave_secret.value')
                                    ->label(__('messages.flutterwave.flutterwave_secret_key') . ':')
                                    ->placeholder(__('messages.flutterwave.flutterwave_secret_key'))
                                    ->validationAttribute(__('messages.flutterwave.flutterwave_secret_key'))
                                    ->required(),
                            ])->columns(2)->visible(function (callable $get) {
                                if ($get('flutterwave_enable.value')) {
                                    return true;
                                }
                                return false;
                            }),
                        Toggle::make('manual_payment_enabled')
                            ->disabled()
                            ->label(__('messages.transaction_filter.manual') . ':'),
                    ])
            ])->statePath('data');
    }
    public function save()
    {
        $result = $this->form->getState();
        $keys = Arr::except($result, ['stripe_enable', 'paypal_enable', 'razorpay_enable', 'paystack_enable', 'phonepay_enable', 'flutterwave_enable']);
        foreach ($result as $key => $value) {
            if (array_key_exists($key, $keys)) {
                SuperAdminSetting::updateOrCreate(['key' => $key], ['value' => $value['value']]);
                continue;
            }

            SuperAdminSetting::updateOrCreate(['key' => $key], ['value' => $value['value']]);
        }
        Notification::make()
            ->success()
            ->title(__('messages.flash.payment_gateway_updated'))
            ->send();
    }
}
