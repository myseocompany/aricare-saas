<?php

namespace App\Filament\HospitalAdmin\Clusters\Settings\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Module;
use Livewire\Livewire;
use Filament\Forms\Form;
use Mockery\Matcher\Not;
use Filament\Tables\Table;
use App\Models\SidebarSetting;
use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\HospitalAdmin\Clusters\Settings;
use App\Filament\HospitalAdmin\Clusters\Settings\Resources\SidebarSettingResource\Pages;


class SidebarSettingResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $cluster = Settings::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('messages.sidebar_setting');
    }

    public static function getLabel(): string
    {
        return __('messages.sidebar_setting');
    }

    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole('Admin')) {
            return true;
        }
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->hasRole('Admin')) {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (auth()->user()->hasRole('Admin')) {
            return true;
        }
        return false;
    }

    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole('Admin')) {
            return true;
        }
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return
            $table = $table->modifyQueryUsing(function (Builder $query) {
                $query->whereTenantId(getLoggedInUser()->tenant_id)->where('is_hidden', 0);
                return $query;
            })
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('messages.common.name')),
                ToggleColumn::make('is_active')
                    ->label(__('messages.common.status'))
                    ->afterStateUpdated(function () {
                        Redirect::to(self::getUrl('index'));
                        Notification::make()
                            ->title(__('messages.common.status_updated_successfully'))
                            ->success()
                            ->send();
                    })->alignEnd()
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label(__('messages.common.status'))
                    ->options([
                        '' => __('messages.filter.all'),
                        '1' => __('messages.filter.active'),
                        '0' => __('messages.filter.deactive'),
                    ])->native(false),
            ])
            ->recordUrl(null)
            ->actions([])
            ->bulkActions([])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSidebarSettings::route('/'),
            'create' => Pages\CreateSidebarSetting::route('/create'),
            'edit' => Pages\EditSidebarSetting::route('/{record}/edit'),
        ];
    }
}
