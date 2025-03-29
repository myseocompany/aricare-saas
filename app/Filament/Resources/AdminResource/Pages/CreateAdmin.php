<?php

namespace App\Filament\Resources\AdminResource\Pages;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Department;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Hash;
use App\Repositories\AdminRepository;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\AdminResource;
use Filament\Resources\Pages\CreateRecord;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;
    public function getTitle(): string
    {
        return __('messages.admin_user.new_admin');
    }
    protected static bool $canCreateAnother = false;
    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }
    protected function handleRecordCreation(array $input): Model
    {
        $input['region_code'] = !empty($input['phone']) ? getRegionCode($input['region_code'] ?? '') : null;
        $input['phone'] = getPhoneNumber($input['phone']);
        $user = app(AdminRepository::class)->store($input);
        return $user;
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.admin_user.admin_saved_successfully');
    }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
