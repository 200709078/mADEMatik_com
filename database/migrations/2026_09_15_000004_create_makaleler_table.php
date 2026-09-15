<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Eski şemadan farklar:
     * - durum: int(0/1 + comment) yerine boolean (0:pasif, 1:aktif anlamı korundu).
     * - tiklanma: unsignedInteger.
     * - resim: nullable (güncelleme formu zaten resimsiz kayda izin veriyordu).
     * - slug_baslik ve durum için index eklendi (front sorguları bu kolonlarla çalışır).
     */
    public function up(): void
    {
        Schema::create('makaleler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoriler')->cascadeOnDelete();
            $table->string('baslik');
            $table->string('slug_baslik')->index();
            $table->longText('makale');
            $table->string('yazar');
            $table->unsignedInteger('tiklanma')->default(0);
            $table->boolean('durum')->default(false);
            $table->string('resim')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('makaleler');
    }
};
