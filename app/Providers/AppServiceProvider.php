<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\User;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        parent::register();
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::define('viewApiDocs', function (User $user) {
            return true;
        });
        // Gate::policy()
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('discord', \SocialiteProviders\Google\Provider::class);
        });

        // Mencegah error saat migrasi awal (karena tabel belum ada)
        if (Schema::hasTable('site_settings')) {
            // Ambil data setting pertama, atau buat object kosong jika belum ada
            $setting = SiteSetting::first() ?? new SiteSetting();

            // Share variabel $siteSetting ke SEMUA view blade
            View::share('siteSetting', $setting);
        }
    }
}
