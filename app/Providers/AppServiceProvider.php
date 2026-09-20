<?php

namespace App\Providers;

use App\Models\AyarlarModel;
use App\Models\KategorilerModel;
use App\Models\SayfalarModel;
use Illuminate\Support\Facades\Cache;
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

            $ayarlar = Cache::remember('ayarlar:1', 3600, fn () => AyarlarModel::find(1));
            $view->with('ayarlar', $ayarlar);
        });

        view()->composer(['front.*'], function ($view) {
            $sayfalar = Schema::hasTable('sayfalar')
                ? Cache::remember('front:sayfalar', 3600, fn () => SayfalarModel::orderBy('sira', 'ASC')->get())
                : collect();

            $kategoriler = Schema::hasTable('kategoriler')
                ? Cache::remember('front:kategoriler', 3600, fn () => KategorilerModel::withCount('makaleler')->orderBy('name', 'ASC')->get())
                : collect();

            $view->with('sayfalar', $sayfalar);
            $view->with('kategoriler', $kategoriler);
        });
    }
}
