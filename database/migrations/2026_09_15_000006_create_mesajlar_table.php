<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Eski şemadan farklar:
     * - telefon: nullable (iletişim formu telefonu zorunlu tutmuyor;
     *   boş değerde strict-mode SQL hatası alınmaması için).
     * - Uzunluk 60: canlı veride botlar telefon alanına e-posta benzeri
     *   metinler yazmış (max 38 karakter); veri kaybı olmaması için
     *   eski 255 yerine gerçek ihtiyaca uygun 60 seçildi.
     * - email için index eklendi.
     */
    public function up(): void
    {
        Schema::create('mesajlar', function (Blueprint $table) {
            $table->id();
            $table->string('adsoyad');
            $table->string('email')->index();
            $table->string('telefon', 60)->nullable();
            $table->string('konu');
            $table->longText('mesaj');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesajlar');
    }
};
