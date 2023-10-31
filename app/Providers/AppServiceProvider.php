<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

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
        LogViewer::auth(function ($request) {
            return auth()->check();
        });

        Filament::registerRenderHook('user-menu.start', function () {
            return Blade::render('<livewire:operday-status />');
        });

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label(__('Работа')),
                NavigationGroup::make()
                    ->label(__('Касса')),
                NavigationGroup::make()
                    ->label(__('Склад')),
                NavigationGroup::make()
                    ->label(__('Админ')),
                NavigationGroup::make()
                    ->label(__('Справочники')),
            ]);
        });
    }
}
