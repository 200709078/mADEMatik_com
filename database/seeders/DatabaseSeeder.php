<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AyarlarModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Uygulamanın boş kurulumda açılabilmesi için gereken asgari kayıtlar.
     * Canlı veriler bu seeder ile değil, app:import-legacy komutu ile taşınır.
     */
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => config('app.admin_email', env('ADMIN_EMAIL', 'admin@madematik.com'))],
            [
                'name' => config('app.admin_name', env('ADMIN_NAME', 'Adem VAROL')),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'degistir-12345')),
            ]
        );

        if (AyarlarModel::count() === 0) {
            AyarlarModel::create([
                'baslik' => 'Adem VAROL',
                'logo' => 'adem-varol-logo.png',
                'favicon' => 'adem-varol-favicon.jpeg',
                'instagram' => 'https://www.instagram.com/madematik/',
                'youtube' => 'https://youtube.com/',
                'facebook' => 'https://www.facebook.com/mADEMatik',
                'twitter' => 'https://twitter.com/',
                'whatsapp' => 'https://wa.me/',
            ]);
        }
    }
}
