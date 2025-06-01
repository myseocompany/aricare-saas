<?php

namespace App\Filament\HospitalAdmin\Clusters\RipsPayers;

use Filament\Resources\Resource;
use App\Filament\HospitalAdmin\Clusters\RipsPayers\Pages;
use App\Filament\HospitalAdmin\Clusters\RipsPayers\RelationManagers;

class TransactionsResource extends Resource
{
    protected static ?string $model = RipsPayer::class;

    protected static ?string $cluster = RipsPayers::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('messages.subscription_plans.transactions');
    }

    public static function getLabel(): string
    {
        return __('messages.subscription_plans.transactions');
    }
    }
