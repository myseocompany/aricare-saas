<div class="flex items-center">
    @if (auth()->check())
        @if (auth()->user()->hasRole('Super Admin'))
            <a href="{{ route('landing-home') }}" target="_black"
                class="flex items-center text-gray-900 text-lg font-bold dark:text-white ">
                <img src="{{ asset(App\Models\SuperAdminSetting::where('key', '=', 'app_logo')->first()->value) ?? '' }}"
                    width="40" height="40" alt="{{ config('app.name') }}">
                &nbsp;&nbsp;&nbsp;{{ App\Models\SuperAdminSetting::where('key', '=', 'app_name')->first()->value ?? '' }}
            </a>
        @else
            <a href="{{ route('front', ['username' => auth()->user()->hospital->tenant_username]) }}" target="_black"
                class="flex items-center text-gray-900 text-lg font-bold dark:text-white ">
                <img src="{{ asset(App\Models\Setting::where('key', '=', 'app_logo')->where('tenant_id', '=', auth()->user()->tenant_id)->first()->value) ?? '' }}"
                    width="40" height="40" alt="{{ config('app.name') }}">
                &nbsp;&nbsp;&nbsp;{{ App\Models\Setting::where('key', '=', 'app_name')->where('tenant_id', '=', auth()->user()->tenant_id)->first()->value ?? '' }}
            </a>
        @endif
    @endif
</div>
