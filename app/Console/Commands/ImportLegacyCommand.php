<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportLegacyCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:import-legacy
                            {--source-database=legacy_import : Eski dump\'ın yüklü olduğu veritabanı}
                            {--truncate : Hedef tabloları aktarımdan önce boşalt}';

    /**
     * @var string
     */
    protected $description = 'Eski mADEMatik SQL yedeğindeki verileri yeni şemaya aktarır (kayıt ID\'leri korunur)';

    public function handle(): int
    {
        $source = $this->option('source-database');

        foreach (['admins', 'ayarlar', 'kategoriler', 'makaleler', 'mesajlar', 'sayfalar'] as $table) {
            if (! Schema::connection('mysql')->hasTable($table)) {
                $this->error("Hedef tablo bulunamadı: {$table} (önce php artisan migrate çalıştırın)");

                return self::FAILURE;
            }
        }

        try {
            DB::table("{$source}.admins")->count();
        } catch (\Throwable $e) {
            $this->error("Kaynak veritabanına erişilemedi: {$source} ({$e->getMessage()})");

            return self::FAILURE;
        }

        if ($this->option('truncate')) {
            Schema::disableForeignKeyConstraints();
            foreach (['mesajlar', 'sayfalar', 'makaleler', 'kategoriler', 'ayarlar', 'admins'] as $table) {
                DB::table($table)->truncate();
            }
            Schema::enableForeignKeyConstraints();
            $this->info('Hedef tablolar boşaltıldı.');
        }

        $rapor = [];

        $rapor['admins'] = $this->kopyala($source, 'admins', fn ($row) => [
            'id' => $row->id,
            'name' => $row->name,
            'email' => $row->email,
            'password' => $row->password,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ]);

        $rapor['ayarlar'] = $this->kopyala($source, 'ayarlar', fn ($row) => [
            'id' => $row->id,
            'baslik' => $row->baslik,
            'logo' => $row->logo ?: null,
            'favicon' => $row->favicon ?: null,
            'instagram' => $row->instagram ?: null,
            'youtube' => $row->youtube ?: null,
            'facebook' => $row->facebook ?: null,
            'twitter' => $row->twitter ?: null,
            'whatsapp' => $row->whatsapp ?? null,
            // Eski şemada DATE idi, yeni şemada timestamp.
            'created_at' => $this->tarihCevir($row->created_at),
            'updated_at' => $this->tarihCevir($row->updated_at),
        ]);

        $rapor['kategoriler'] = $this->kopyala($source, 'kategoriler', fn ($row) => [
            'id' => $row->id,
            'name' => $row->name,
            'slug' => $row->slug,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ]);

        $rapor['makaleler'] = $this->kopyala($source, 'makaleler', fn ($row) => [
            'id' => $row->id,
            'kategori_id' => $row->kategori_id,
            'baslik' => $row->baslik,
            'slug_baslik' => $row->slug_baslik,
            'makale' => $row->makale,
            'yazar' => $row->yazar,
            'tiklanma' => $row->tiklanma,
            'durum' => (bool) $row->durum,
            'resim' => $row->resim ?: null,
            'deleted_at' => $row->deleted_at,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ]);

        $rapor['sayfalar'] = $this->kopyala($source, 'sayfalar', fn ($row) => [
            'id' => $row->id,
            'baslik' => $row->baslik,
            'slug_baslik' => $row->slug_baslik,
            'resim' => $row->resim ?: null,
            'icerik' => $row->icerik,
            'sira' => $row->sira,
            'deleted_at' => $row->deleted_at,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ]);

        $rapor['mesajlar'] = $this->kopyala($source, 'mesajlar', fn ($row) => [
            'id' => $row->id,
            'adsoyad' => $row->adsoyad,
            'email' => $row->email,
            'telefon' => $row->telefon !== '' ? $row->telefon : null,
            'konu' => $row->konu,
            'mesaj' => $row->mesaj,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ]);

        $this->info('Aktarım tamamlandı (kaynak -> hedef):');
        $basarili = true;
        foreach ($rapor as $tablo => [$kaynak, $hedef]) {
            $durum = $kaynak === $hedef ? 'OK' : 'FARK!';
            if ($kaynak !== $hedef) {
                $basarili = false;
            }
            $this->line(sprintf('  %-12s %5d -> %5d  [%s]', $tablo, $kaynak, $hedef, $durum));
        }

        return $basarili ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @return array{0:int,1:int}
     */
    private function kopyala(string $source, string $tablo, callable $donustur): array
    {
        $kaynakSayi = DB::table("{$source}.{$tablo}")->count();

        DB::table("{$source}.{$tablo}")->orderBy('id')->chunk(200, function ($satirlar) use ($tablo, $donustur) {
            $veriler = [];
            foreach ($satirlar as $satir) {
                $veriler[] = $donustur($satir);
            }
            // ID'ler korunur; aynı ID varsa üzerine yazılır (idempotent).
            DB::table($tablo)->upsert($veriler, ['id']);
        });

        return [$kaynakSayi, DB::table($tablo)->count()];
    }

    private function tarihCevir(mixed $deger): ?string
    {
        if ($deger === null || $deger === '') {
            return null;
        }
        $metin = (string) $deger;

        return strlen($metin) === 10 ? $metin.' 00:00:00' : $metin;
    }
}
