<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SayfalarModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sayfalar';

    protected $fillable = [
        'baslik',
        'slug_baslik',
        'resim',
        'icerik',
        'sira',
    ];

    protected function casts(): array
    {
        return [
            'sira' => 'integer',
        ];
    }
}
