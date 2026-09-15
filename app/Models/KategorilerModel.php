<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategorilerModel extends Model
{
    use HasFactory;

    protected $table = 'kategoriler';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function makaleler(): HasMany
    {
        return $this->hasMany(MakalelerModel::class, 'kategori_id', 'id');
    }

    public function makaleSay(): int
    {
        return $this->makaleler()->count();
    }
}
