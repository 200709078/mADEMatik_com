<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\KategorilerModel;
use App\Models\MakalelerModel;
use App\Models\MesajlarModel;
use App\Models\SayfalarModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class Homepage extends Controller
{
    public function index(): View
    {
        $data['makaleler'] = MakalelerModel::with('getKategori')->orderBy('created_at', 'ASC')->get();

        return view('front.homepage', $data);
    }

    public function makaleTek(string $kategori, string $slug_baslik): View
    {
        $kategori = KategorilerModel::whereSlug($kategori)->first() ?? abort(404, 'Böyle bir makale bulunamadı...!!!');
        $icerik = MakalelerModel::with('getKategori')->whereSlug_baslik($slug_baslik)->whereKategori_id($kategori->id)->first() ?? abort(404, 'Böyle bir makale bulunamadı...!!!');
        $icerik->increment('tiklanma');
        $data['makale'] = $icerik;

        return view('front.makale', $data);
    }

    public function kategoriListe(string $slug): View
    {
        $kategori = KategorilerModel::whereSlug($slug)->first() ?? abort(404, 'Böyle bir kategori bulunamadı...!!!');
        $data['kategori'] = $kategori;
        $data['makaleler'] = MakalelerModel::with('getKategori')->whereKategori_id($kategori->id)->orderBy('created_at', 'DESC')->get();

        return view('front.kategori', $data);
    }

    public function sayfa(string $slug_baslik): View
    {
        $sayfa = SayfalarModel::whereSlug_baslik($slug_baslik)->first() ?? abort(404, 'Böyle bir sayfa bulunamadı...!!!');
        $data['sayfa'] = $sayfa;

        return view('front.sayfa', $data);
    }

    public function iletisim(): View
    {
        return view('front.iletisim');
    }

    public function iletisimPost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'adsoyad' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefon' => 'nullable|string|max:60',
            'konu' => 'required|string|max:255',
            'mesaj' => 'required|string',
        ]);

        $mesaj = new MesajlarModel;
        $mesaj->adsoyad = $validated['adsoyad'];
        $mesaj->email = $validated['email'];
        $mesaj->telefon = $validated['telefon'] ?? null;
        $mesaj->konu = $validated['konu'];
        $mesaj->mesaj = $validated['mesaj'];
        $mesaj->save();

        try {
            Mail::raw($validated['mesaj'], function ($message) use ($validated) {
                $message->from($validated['email'], $validated['adsoyad']);
                $message->to(config('app.contact_to_address'), config('app.contact_to_name'));
                $message->subject($validated['konu'].' - 0'.$validated['telefon']);
            });
        } catch (\Throwable $e) {
            Log::error('İletişim e-postası gönderilemedi.', ['hata' => $e->getMessage()]);

            return redirect()->route('homepage')
                ->with('error', 'Mesajınız kaydedildi ancak e-posta bildirimi gönderilemedi.');
        }

        return redirect()->route('homepage')
            ->with('success', 'Teşekkürler. Mesajınız bize iletildi...');
    }
}
