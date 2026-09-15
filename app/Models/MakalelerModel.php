<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MakalelerModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'makaleler';

    protected $fillable = [
        'kategori_id',
        'baslik',
        'slug_baslik',
        'makale',
        'yazar',
        'tiklanma',
        'durum',
        'resim',
    ];

    protected function casts(): array
    {
        return [
            'durum' => 'boolean',
            'tiklanma' => 'integer',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategorilerModel::class, 'kategori_id', 'id');
    }

    /**
     * Eski Blade görünümleriyle uyumluluk için korunan ilişki adı.
     */
    public function getKategori(): BelongsTo
    {
        return $this->kategori();
    }
}
