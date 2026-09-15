<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\KategorilerModel;
use App\Models\MakalelerModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MakalelerController extends Controller
{
    public function makaleDuzenleme(int $id): View
    {
        $data['kategoriler'] = KategorilerModel::all();
        $data['makaleler'] = MakalelerModel::whereId($id)->first();

        return view('back.makaleler.update', $data);
    }

    public function makaleGuncelleme(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'kategori' => 'required',
            'baslik' => 'required|min:5',
            'icerik' => 'required',
            'yazar' => 'required',
            'resim' => 'image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $makale = MakalelerModel::find($id);
        $makale->kategori_id = $request->kategori;
        $makale->baslik = $request->baslik;
        $makale->slug_baslik = Str::slug($request->baslik);
        $makale->makale = $request->icerik;
        $makale->yazar = $request->yazar;

        if ($request->hasFile('resim')) {
            $resimadi = Str::slug($request->baslik).'.'.$request->resim->getClientOriginalExtension();
            $request->resim->move(public_path('img/img_makale'), $resimadi);
            $makale->resim = $resimadi;
        }
        $makale->save();

        return redirect()->route('admin.makaleler.index')->with('success', 'Makaleniz güncellendi.');
    }

    public function makaleSilme(int $id): RedirectResponse
    {
        MakalelerModel::find($id)?->delete();

        return redirect()->route('admin.makaleler.index')->with('success', 'Makaleniz geri dönüşüm kutusuna taşındı.');
    }

    public function geriDonusum(): View
    {
        $makaleler['makaleler'] = MakalelerModel::onlyTrashed()->orderBy('deleted_at', 'DESC')->get();

        return view('back.makaleler.trashed', $makaleler);
    }

    public function geriAl(int $id): RedirectResponse
    {
        $makale = MakalelerModel::onlyTrashed()->find($id);
        $makale?->restore();

        return redirect()->route('admin.geridonusumoku')->with('success', 'Makaleniz geri dönüşüm kutusundan geri alındı.');
    }

    public function makaleOkuma(): View
    {
        $makaleler['makaleler'] = MakalelerModel::orderBy('created_at', 'DESC')->get();

        return view('back.makaleler.index', $makaleler);
    }

    public function makaleOlusturma(): View
    {
        $data['kategoriler'] = KategorilerModel::all();
        $data['makaleler'] = MakalelerModel::orderBy('created_at', 'DESC')->get();

        return view('back.makaleler.create', $data);
    }

    public function makaleKaydetme(Request $request): RedirectResponse
    {
        $request->validate([
            'kategori' => 'required',
            'baslik' => 'required|min:5',
            'icerik' => 'required',
            'yazar' => 'required',
            'resim' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $makale = new MakalelerModel;
        $makale->kategori_id = $request->kategori;
        $makale->baslik = $request->baslik;
        $makale->slug_baslik = Str::slug($request->baslik);
        $makale->makale = $request->icerik;
        $makale->yazar = $request->yazar;

        if ($request->hasFile('resim')) {
            $resimadi = Str::slug($request->baslik).'.'.$request->resim->getClientOriginalExtension();
            $request->resim->move(public_path('img/img_makale'), $resimadi);
            $makale->resim = $resimadi;
        }
        $makale->save();

        return redirect()->route('admin.makaleler.index')->with('success', 'Makaleniz oluşturuldu.');
    }
}
