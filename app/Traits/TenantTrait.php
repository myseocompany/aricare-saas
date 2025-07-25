<?php
// app/Traits/TenantScoped.php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait TenantTrait
{
    protected static function bootTenantScoped()
    {
        static::addGlobalScope('tenant_id', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('tenant_id', Auth::user()->tenant_id);
            }
        });
    }

        public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()->tenant_id);
    }

    public static function assignTenantId(array $data): array
    {
        $data['tenant_id'] = auth()->user()->tenant_id;
        dd($data);
        return $data;
    }
}

