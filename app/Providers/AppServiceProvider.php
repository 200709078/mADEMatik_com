<?php

namespace App\Providers;

use App\Models\AyarlarModel;
use App\Models\KategorilerModel;
use App\Models\SayfalarModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
     *
     * Schema::hasTable kontrolleri, migration'lar henüz çalışmamışken
     * (örn. php artisan migrate) sorgu hatası alınmaması içindir.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            if (! Schema::hasTable('ayarlar')) {
                $view->with('ayarlar', null);

                return;
            }

            $view->with('ayarlar', AyarlarModel::find(1));
        });

        view()->composer(['front.*'], function ($view) {
            $view->with('sayfalar', Schema::hasTable('sayfalar') ? SayfalarModel::orderBy('sira', 'ASC')->get() : collect());
            $view->with('kategoriler', Schema::hasTable('kategoriler') ? KategorilerModel::orderBy('name', 'ASC')->get() : collect());
        });
    }
}
