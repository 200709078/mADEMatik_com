<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\SayfalarModel;
use App\Support\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SayfalarController extends Controller
{
    public function sayfaDuzenleme(int $id): View
    {
        $sayfa['sayfalar'] = SayfalarModel::findOrFail($id);

        return view('back.sayfalar.update', $sayfa);
    }

    public function sayfaGuncelleme(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'baslik' => 'required|string|min:5|max:255',
            'resim' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'icerik' => 'required|string',
            'sira' => 'required|integer|min:0',
        ]);

        $sayfa = SayfalarModel::findOrFail($id);
        $sayfa->baslik = $request->baslik;
        $sayfa->slug_baslik = $this->uniqueSayfaSlug($request->baslik, $id);
        $sayfa->icerik = HtmlSanitizer::clean($request->icerik);
        $sayfa->sira = $request->sira;

        if ($request->hasFile('resim')) {
            if ($sayfa->resim && file_exists(public_path('img/img_sayfa/'.$sayfa->resim))) {
                @unlink(public_path('img/img_sayfa/'.$sayfa->resim));
            }
            $resimadi = Str::slug($request->baslik).'-'.time().'.'.$request->resim->getClientOriginalExtension();
            $request->resim->move(public_path('img/img_sayfa'), $resimadi);
            $sayfa->resim = $resimadi;
        }
        $sayfa->save();
        Cache::forget('front:sayfalar');

        return redirect()->route('admin.sayfalar.index')->with('success', 'Sayfanız güncellendi.');
    }

    public function sayfaSilme(int $id): RedirectResponse
    {
        SayfalarModel::findOrFail($id)->delete();
        Cache::forget('front:sayfalar');

        return redirect()->route('admin.sayfalar.index')->with('success', 'Sayfanız geri dönüşüm kutusuna taşındı.');
    }

    public function geriDonusum(): View
    {
        $sayfalar['sayfalar'] = SayfalarModel::onlyTrashed()->orderBy('deleted_at', 'DESC')->get();

        return view('back.sayfalar.trashed', $sayfalar);
    }

    public function geriAl(int $id): RedirectResponse
    {
        $sayfa = SayfalarModel::onlyTrashed()->find($id);
        $sayfa?->restore();
        Cache::forget('front:sayfalar');

        return redirect()->route('admin.sgeridonusumoku')->with('success', 'Sayfanız geri dönüşüm kutusundan geri alındı.');
    }

    public function sayfaOkuma(): View
    {
        $sayfalar['sayfalar'] = SayfalarModel::orderBy('created_at', 'DESC')->get();

        return view('back.sayfalar.index', $sayfalar);
    }

    public function sayfaOlusturma(): View
    {
        $data['sayfalar'] = SayfalarModel::orderBy('created_at', 'DESC')->get();

        return view('back.sayfalar.create', $data);
    }

    private function uniqueSayfaSlug(string $baslik, ?int $excludeId = null): string
    {
        $base = Str::slug($baslik);
        $slug = $base;
        $i = 1;

        while (SayfalarModel::withTrashed()->where('slug_baslik', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function sayfaKaydetme(Request $request): RedirectResponse
    {
        $request->validate([
            'baslik' => 'required|string|min:5|max:255',
            'resim' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'icerik' => 'required|string',
            'sira' => 'required|integer|min:0',
        ]);

        $sayfa = new SayfalarModel;
        $sayfa->baslik = $request->baslik;
        $sayfa->slug_baslik = $this->uniqueSayfaSlug($request->baslik);
        $sayfa->icerik = HtmlSanitizer::clean($request->icerik);
        $sayfa->sira = $request->sira;

        if ($request->hasFile('resim')) {
            $resimadi = Str::slug($request->baslik).'-'.time().'.'.$request->resim->getClientOriginalExtension();
            $request->resim->move(public_path('img/img_sayfa'), $resimadi);
            $sayfa->resim = $resimadi;
        }
        $sayfa->save();
        Cache::forget('front:sayfalar');

        return redirect()->route('admin.sayfalar.index')->with('success', 'Sayfanız oluşturuldu.');
    }
}
