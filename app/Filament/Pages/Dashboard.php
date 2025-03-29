<?php

namespace App\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??
            __('messages.dashboard.dashboard');
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? __('messages.dashboard.dashboard');
    }
}
