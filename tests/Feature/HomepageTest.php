<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\KategorilerModel;
use App\Models\MakalelerModel;
use App\Models\MesajlarModel;
use App\Models\SayfalarModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_anasayfa_acilir(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_iletisim_sayfasi_acilir(): void
    {
        $this->get('/iletisim')->assertOk();
    }

    public function test_bilinmeyen_kategori_403_doner(): void
    {
        $this->get('/kategori/olmayan-kategori')->assertForbidden();
    }

    public function test_bilinmeyen_sayfa_403_doner(): void
    {
        $this->get('/olmayan-sayfa')->assertForbidden();
    }

    public function test_kategori_makale_ve_sayfa_sayfalari_acilir(): void
    {
        $kategori = KategorilerModel::create(['name' => 'PHP', 'slug' => 'php']);
        MakalelerModel::create([
            'kategori_id' => $kategori->id,
            'baslik' => 'PHP nedir?',
            'slug_baslik' => 'php-nedir',
            'makale' => '<p>icerik</p>',
            'yazar' => 'Adem VAROL',
            'resim' => 'php-nedir.jpeg',
        ]);
        SayfalarModel::create([
            'baslik' => 'Hakkımızda',
            'slug_baslik' => 'hakkimizda',
            'resim' => 'hakkimizda.png',
            'icerik' => '<p>icerik</p>',
            'sira' => 2,
        ]);

        $this->get('/kategori/php')->assertOk()->assertSee('PHP nedir?');
        $this->get('/php/php-nedir')->assertOk()->assertSee('PHP nedir?');
        $this->get('/hakkimizda')->assertOk()->assertSee('Hakkımızda');
    }

    public function test_iletisim_dogrulama_hatalari_toast_icin_sessionda_doner(): void
    {
        $this->post('/iletisimPost', [
            '_token' => csrf_token(),
            'adsoyad' => '',
            'email' => 'gecersiz',
        ])->assertRedirect()->assertSessionHasErrors(['adsoyad', 'email', 'konu', 'mesaj']);
    }

    public function test_iletisim_mesaji_kaydedilir_ve_success_flash_doner(): void
    {
        $this->post('/iletisimPost', [
            '_token' => csrf_token(),
            'adsoyad' => 'Test Kullanıcı',
            'email' => 'test@example.com',
            'telefon' => '5321112233',
            'konu' => 'Deneme',
            'mesaj' => 'Deneme mesajı',
        ])->assertRedirect(route('homepage'))->assertSessionHas('success');

        $this->assertDatabaseHas('mesajlar', [
            'email' => 'test@example.com',
            'konu' => 'Deneme',
        ]);
    }

    public function test_admin_giris_akisi(): void
    {
        Admin::create([
            'name' => 'Adem VAROL',
            'email' => 'admin@madematik.com',
            'password' => 'gizli-sifre',
        ]);

        $this->get('/admin')->assertOk();
        $this->get('/admin/giris')->assertRedirect('/admin');

        $this->post('/admin/giris', [
            '_token' => csrf_token(),
            'email' => 'admin@madematik.com',
            'password' => 'yanlis',
        ])->assertRedirect(route('admin.login'));

        $this->post('/admin/giris', [
            '_token' => csrf_token(),
            'email' => 'admin@madematik.com',
            'password' => 'gizli-sifre',
        ])->assertRedirect(route('admin.dashboard'))->assertSessionHas('success');

        $this->get('/admin/panel/index')->assertOk();
        $this->get('/admin/makaleler')->assertOk();
        $this->get('/admin/sayfalar')->assertOk();
        $this->get('/admin/kategoriler')->assertOk();
        $this->get('/admin/ayarlar')->assertOk();
    }

    public function test_misafir_admin_paneline_giremez(): void
    {
        $this->get('/admin/panel/index')->assertRedirect(route('admin.login'));
        $this->get('/admin/makaleler')->assertRedirect(route('admin.login'));
        $this->get('/admin/mesajlar')->assertRedirect(route('admin.login'));
    }

    public function test_sitemap_xml_uretilir(): void
    {
        $kategori = KategorilerModel::create(['name' => 'PHP', 'slug' => 'php']);
        MakalelerModel::create([
            'kategori_id' => $kategori->id,
            'baslik' => 'PHP nedir?',
            'slug_baslik' => 'php-nedir',
            'makale' => '<p>icerik</p>',
            'yazar' => 'Adem VAROL',
            'resim' => 'php-nedir.jpeg',
        ]);
        SayfalarModel::create([
            'baslik' => 'Hakkımızda',
            'slug_baslik' => 'hakkimizda',
            'resim' => 'hakkimizda.png',
            'icerik' => '<p>icerik</p>',
            'sira' => 2,
        ]);

        $response = $this->get('/sitemap.xml')->assertOk();
        $this->assertStringContainsString('text/xml', $response->headers->get('Content-Type'));
        $response->assertSee(route('homepage'), false);
        $response->assertSee(route('kategoriListe', 'php'), false);
        $response->assertSee(route('makale', ['php', 'php-nedir']), false);
        $response->assertSee(route('sayfa', 'hakkimizda'), false);
    }

    public function test_admin_mesaj_akisi(): void
    {
        $admin = Admin::create([
            'name' => 'Adem VAROL',
            'email' => 'admin@madematik.com',
            'password' => 'gizli-sifre',
        ]);

        $mesaj = MesajlarModel::create([
            'adsoyad' => 'Test Kullanıcı',
            'email' => 'test@example.com',
            'telefon' => '5321112233',
            'konu' => 'Deneme',
            'mesaj' => 'Deneme mesajı',
        ]);

        $this->actingAs($admin)
            ->get('/admin/mesajlar')->assertOk()->assertSee('Deneme');

        $this->actingAs($admin)
            ->get("/admin/mesajlar/mesajduzenleme/{$mesaj->id}")->assertOk();

        $this->actingAs($admin)
            ->post("/admin/mesajlar/guncelleme/{$mesaj->id}", [
                '_token' => csrf_token(),
                'adsoyad' => 'Test Kullanıcı',
                'email' => 'test@example.com',
                'telefon' => '5321112233',
                'konu' => 'Güncel Konu',
                'mesaj' => 'Deneme mesajı',
            ])->assertRedirect(route('admin.mesajlar.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('mesajlar', ['id' => $mesaj->id, 'konu' => 'Güncel Konu']);

        $this->actingAs($admin)
            ->get("/admin/mesajlar/silme/{$mesaj->id}")
            ->assertRedirect(route('admin.mesajlar.index'))->assertSessionHas('success');

        $this->assertDatabaseMissing('mesajlar', ['id' => $mesaj->id]);
    }
}
