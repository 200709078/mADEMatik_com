<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Eski şemadan farklar:
     * - whatsapp kolonu eklendi (canlı DB'de vardı, migration'da yoktu).
     * - created_at/updated_at DATE yerine standart timestamp.
     * - baslik dışındaki alanlar nullable (eski dump'ta hepsi TEXT NOT NULL idi,
     *   ancak logo/favicon/sosyal alanlar boş bırakılabilmelidir).
     * - Tüm metin alanları string(255); collation DB varsayılanı (utf8mb4).
     */
    public function up(): void
    {
        Schema::create('ayarlar', function (Blueprint $table) {
            $table->id();
            $table->string('baslik');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('whatsapp')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayarlar');
    }
};
