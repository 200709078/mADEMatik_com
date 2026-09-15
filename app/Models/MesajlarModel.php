<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MesajlarModel extends Model
{
    use HasFactory;

    protected $table = 'mesajlar';

    protected $fillable = [
        'adsoyad',
        'email',
        'telefon',
        'konu',
        'mesaj',
    ];
}
