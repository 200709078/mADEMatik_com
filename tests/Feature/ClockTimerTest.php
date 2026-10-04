<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ClockTimerTest extends TestCase
{
    public function test_saat_sayfasi_veritabanina_erismeden_acilir(): void
    {
        $this->withoutVite();
        config([
            'database.connections.sqlite.database' => __DIR__.'/missing-clock-timer.sqlite',
            'session.driver' => 'database',
            'cache.default' => 'database',
        ]);
        $this->expectsDatabaseQueryCount(0);

        $response = $this->get('/saat');

        $response->assertOk()->assertViewIs('tools.clock-timer');
        $response->assertSee('href="'.route('homepage').'"', false);
    }

    public function test_mevcut_menude_saat_ve_sayac_baglantisi_bulunur(): void
    {
        app()->setLocale('tr');

        $response = $this->get('/iletisim');

        $response->assertSeeInOrder([
            'href="'.route('homepage').'"',
            'href="'.route('clock-timer').'"',
            'Saat &amp; Sayaç',
            'href="'.route('iletisim').'"',
        ], false);
    }

    public function test_ingilizce_menude_clock_ve_timer_baglantisi_bulunur(): void
    {
        app()->setLocale('en');

        $response = $this->get('/iletisim');

        $response->assertSeeInOrder([
            'href="'.route('clock-timer').'"',
            'Clock &amp; Timer',
        ], false);
    }

    public function test_saat_disindaki_sayfa_ve_makale_yollari_korunur(): void
    {
        $routes = Route::getRoutes();

        $this->assertSame('sayfa', $routes->match(Request::create('/sayac'))->getName());
        $this->assertSame('sayfa', $routes->match(Request::create('/kelebek'))->getName());
        $this->assertSame('makale', $routes->match(Request::create('/bir-kategori/bir-makale'))->getName());
    }
}
