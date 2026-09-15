<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Eski şemadan farklar:
     * - resim: nullable (güncelleme formu zaten resimsiz kayda izin veriyordu).
     * - sira: unsignedInteger + index (navigasyon sira'ya göre sıralanır).
     * - slug_baslik için index eklendi.
     * - slug_baslik UNIQUE yapılmadı: çöp kutusundaki kayıtlar arasında
     *   aynı slug tekrar edebiliyor (canlı veride örneği var).
     */
    public function up(): void
    {
        Schema::create('sayfalar', function (Blueprint $table) {
            $table->id();
            $table->string('baslik');
            $table->string('slug_baslik')->index();
            $table->string('resim')->nullable();
            $table->longText('icerik');
            $table->unsignedInteger('sira')->default(0)->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sayfalar');
    }
};
