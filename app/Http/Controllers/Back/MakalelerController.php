<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\KategorilerModel;
use App\Models\MakalelerModel;
use App\Support\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MakalelerController extends Controller
{
    public function makaleDuzenleme(int $id): View
    {
        $data['kategoriler'] = KategorilerModel::all();
        $data['makaleler'] = MakalelerModel::findOrFail($id);

        return view('back.makaleler.update', $data);
    }

    public function makaleGuncelleme(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'kategori' => 'required|integer|exists:kategoriler,id',
            'baslik' => 'required|string|min:5|max:255',
            'icerik' => 'required|string',
            'yazar' => 'required|string|max:255',
            'resim' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $makale = MakalelerModel::findOrFail($id);
        $makale->kategori_id = $request->kategori;
        $makale->baslik = $request->baslik;
        $makale->slug_baslik = $this->uniqueMakaleSlug($request->baslik, $id);
        $makale->makale = HtmlSanitizer::clean($request->icerik);
        $makale->yazar = $request->yazar;

        if ($request->hasFile('resim')) {
            if ($makale->resim && file_exists(public_path('img/img_makale/'.$makale->resim))) {
                @unlink(public_path('img/img_makale/'.$makale->resim));
            }
            $resimadi = Str::slug($request->baslik).'-'.time().'.'.$request->resim->getClientOriginalExtension();
            $request->resim->move(public_path('img/img_makale'), $resimadi);
            $makale->resim = $resimadi;
        }
        $makale->save();
        Cache::forget('front:kategoriler');

        return redirect()->route('admin.makaleler.index')->with('success', 'Makaleniz güncellendi.');
    }

    public function makaleSilme(int $id): RedirectResponse
    {
        MakalelerModel::findOrFail($id)->delete();
        Cache::forget('front:kategoriler');

        return redirect()->route('admin.makaleler.index')->with('success', 'Makaleniz geri dönüşüm kutusuna taşındı.');
    }

    public function geriDonusum(): View
    {
        $makaleler['makaleler'] = MakalelerModel::with('getKategori')->onlyTrashed()->orderBy('deleted_at', 'DESC')->get();

        return view('back.makaleler.trashed', $makaleler);
    }

    public function geriAl(int $id): RedirectResponse
    {
        $makale = MakalelerModel::onlyTrashed()->find($id);
        $makale?->restore();
        Cache::forget('front:kategoriler');

        return redirect()->route('admin.geridonusumoku')->with('success', 'Makaleniz geri dönüşüm kutusundan geri alındı.');
    }

    public function makaleOkuma(): View
    {
        $makaleler['makaleler'] = MakalelerModel::with('getKategori')->orderBy('created_at', 'DESC')->get();

        return view('back.makaleler.index', $makaleler);
    }

    public function makaleOlusturma(): View
    {
        $data['kategoriler'] = KategorilerModel::all();
        $data['makaleler'] = MakalelerModel::orderBy('created_at', 'DESC')->get();

        return view('back.makaleler.create', $data);
    }

    private function uniqueMakaleSlug(string $baslik, ?int $excludeId = null): string
    {
        $base = Str::slug($baslik);
        $slug = $base;
        $i = 1;

        while (MakalelerModel::withTrashed()->where('slug_baslik', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function makaleKaydetme(Request $request): RedirectResponse
    {
        $request->validate([
            'kategori' => 'required|integer|exists:kategoriler,id',
            'baslik' => 'required|string|min:5|max:255',
            'icerik' => 'required|string',
            'yazar' => 'required|string|max:255',
            'resim' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $makale = new MakalelerModel;
        $makale->kategori_id = $request->kategori;
        $makale->baslik = $request->baslik;
        $makale->slug_baslik = $this->uniqueMakaleSlug($request->baslik);
        $makale->makale = HtmlSanitizer::clean($request->icerik);
        $makale->yazar = $request->yazar;

        if ($request->hasFile('resim')) {
            $resimadi = Str::slug($request->baslik).'-'.time().'.'.$request->resim->getClientOriginalExtension();
            $request->resim->move(public_path('img/img_makale'), $resimadi);
            $makale->resim = $resimadi;
        }
        $makale->save();
        Cache::forget('front:kategoriler');

        return redirect()->route('admin.makaleler.index')->with('success', 'Makaleniz oluşturuldu.');
    }
}
