<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\AyarlarModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AyarlarController extends Controller
{
    public function index(): View
    {
        // Ayar satırı hiç yoksa (taze kurulum) varsayılan satırı oluştur;
        // mevcut kurulumlarda davranış değişmez.
        $ayarlar['ayarlar'] = AyarlarModel::firstOrCreate(
            ['id' => 1],
            [
                'baslik' => 'Adem VAROL',
                'logo' => 'adem-varol-logo.png',
                'favicon' => 'adem-varol-favicon.jpeg',
                'instagram' => 'https://www.instagram.com/madematik/',
                'youtube' => 'https://youtube.com/',
                'facebook' => 'https://www.facebook.com/mADEMatik',
                'twitter' => 'https://twitter.com/',
                'whatsapp' => 'https://wa.me/',
            ]
        );

        return view('back.ayar.index', $ayarlar);
    }

    public function ayarlarGuncelle(Request $request): RedirectResponse
    {
        $request->validate([
            'baslik' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'favicon' => 'nullable|file|mimes:jpeg,jpg,png,ico|max:2048',
            'instagram' => 'nullable|url|max:2048',
            'youtube' => 'nullable|url|max:2048',
            'facebook' => 'nullable|url|max:2048',
            'twitter' => 'nullable|url|max:2048',
            'whatsapp' => 'nullable|url|max:2048',
        ]);

        $ayar = AyarlarModel::firstOrNew(['id' => 1]);
        $ayar->baslik = $request->baslik;
        $ayar->instagram = $request->instagram;
        $ayar->youtube = $request->youtube;
        $ayar->facebook = $request->facebook;
        $ayar->twitter = $request->twitter;
        $ayar->whatsapp = $request->whatsapp;

        if ($request->hasFile('logo')) {
            if ($ayar->logo && file_exists(public_path('img/'.$ayar->logo))) {
                @unlink(public_path('img/'.$ayar->logo));
            }
            $logo = Str::slug($request->baslik).'-logo-'.time().'.'.$request->logo->getClientOriginalExtension();
            $request->logo->move(public_path('img'), $logo);
            $ayar->logo = $logo;
        }
        if ($request->hasFile('favicon')) {
            if ($ayar->favicon && file_exists(public_path('img/'.$ayar->favicon))) {
                @unlink(public_path('img/'.$ayar->favicon));
            }
            $favicon = Str::slug($request->baslik).'-favicon-'.time().'.'.$request->favicon->getClientOriginalExtension();
            $request->favicon->move(public_path('img'), $favicon);
            $ayar->favicon = $favicon;
        }
        $ayar->save();
        Cache::forget('ayarlar:1');

        return redirect()->back()->with('success', 'Ayarlarınız güncellendi.');
    }
}
