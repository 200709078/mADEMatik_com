<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\KategorilerModel;
use App\Models\SayfalarModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SayfalarController extends Controller
{
    public function sayfaDuzenleme(int $id): View
    {
        $sayfa['sayfalar'] = SayfalarModel::whereId($id)->first();

        return view('back.sayfalar.update', $sayfa);
    }

    public function sayfaGuncelleme(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'baslik' => 'required|min:5',
            'resim' => 'image|mimes:jpeg,jpg,png|max:2048',
            'icerik' => 'required',
            'sira' => 'required',
        ]);

        $sayfa = SayfalarModel::find($id);
        $sayfa->baslik = $request->baslik;
        $sayfa->slug_baslik = Str::slug($request->baslik);
        $sayfa->icerik = $request->icerik;
        $sayfa->sira = $request->sira;

        if ($request->hasFile('resim')) {
            $resimadi = Str::slug($request->baslik).'.'.$request->resim->getClientOriginalExtension();
            $request->resim->move(public_path('img/img_sayfa'), $resimadi);
            $sayfa->resim = $resimadi;
        }
        $sayfa->save();

        return redirect()->route('admin.sayfalar.index')->with('success', 'Sayfanız güncellendi.');
    }

    public function sayfaSilme(int $id): RedirectResponse
    {
        SayfalarModel::find($id)?->delete();

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

        return redirect()->route('admin.sgeridonusumoku')->with('success', 'Sayfanız geri dönüşüm kutusundan geri alındı.');
    }

    public function sayfaOkuma(): View
    {
        $sayfalar['sayfalar'] = SayfalarModel::orderBy('created_at', 'DESC')->get();

        return view('back.sayfalar.index', $sayfalar);
    }

    public function sayfaOlusturma(): View
    {
        $data['kategoriler'] = KategorilerModel::all();
        $data['sayfalar'] = SayfalarModel::orderBy('created_at', 'DESC')->get();

        return view('back.sayfalar.create', $data);
    }

    public function sayfaKaydetme(Request $request): RedirectResponse
    {
        $request->validate([
            'baslik' => 'required|min:5',
            'resim' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'icerik' => 'required',
            'sira' => 'required',
        ]);

        $sayfa = new SayfalarModel;
        $sayfa->baslik = $request->baslik;
        $sayfa->slug_baslik = Str::slug($request->baslik);
        $sayfa->icerik = $request->icerik;
        $sayfa->sira = $request->sira;

        if ($request->hasFile('resim')) {
            $resimadi = Str::slug($request->baslik).'.'.$request->resim->getClientOriginalExtension();
            $request->resim->move(public_path('img/img_sayfa'), $resimadi);
            $sayfa->resim = $resimadi;
        }
        $sayfa->save();

        return redirect()->route('admin.sayfalar.index')->with('success', 'Sayfanız oluşturuldu.');
    }
}
