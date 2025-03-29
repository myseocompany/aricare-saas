<?php

namespace App\Filament\HospitalAdmin\Pages;

use App\Repositories\SubscriptionPlanRepository;
use Filament\Pages\Page;

class SubscriptionPlans extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.hospital-admin.pages.subscription-plans';


    protected function getViewData(): array
    {
        return app(SubscriptionPlanRepository::class)->getSubscriptionPlansData();
    }
}
