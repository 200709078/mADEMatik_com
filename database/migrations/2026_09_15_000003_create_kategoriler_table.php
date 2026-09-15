<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Eski şemadan fark: slug UNIQUE (uygulama zaten aynı slug'ı
     * reddediyor; DB seviyesinde de garanti altına alındı).
     */
    public function up(): void
    {
        Schema::create('kategoriler', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategoriler');
    }
};
