<?php

namespace App\Providers;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Carbon::setLocale(config('app.locale'));

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                'Admin Management',
                'Staff Management'
            ]);

            // Filament::registerUserMenuItems([
            //     'account' => UserMenuItem::make()
            //         ->label(trans('Profile'))
            //         ->url(ProfileResource::getUrl()),
            //     'logout' => UserMenuItem::make()
            //         ->label(trans('Logout'))
            //         ->url(route('logout'))
            // ]);
        });
    }
}
