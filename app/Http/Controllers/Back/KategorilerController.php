<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\KategorilerModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KategorilerController extends Controller
{
    public function index(): View
    {
        $kategoriler['kategoriler'] = KategorilerModel::withCount('makaleler')->get();

        return view('back.kategoriler.index', $kategoriler);
    }

    public function yenikategoriEkle(Request $request): RedirectResponse
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        $isExist = KategorilerModel::whereSlug(Str::slug($request->kategori))->first();
        if ($isExist) {
            return redirect()->back()->with('error', $request->kategori.' isimli bir kategori zaten var.');
        }
        $kategori = new KategorilerModel;
        $kategori->name = $request->kategori;
        $kategori->slug = Str::slug($request->kategori);
        $kategori->save();
        Cache::forget('front:kategoriler');

        return redirect()->back()->with('success', 'Kategori eklendi.');
    }

    public function kategoriDuzenle(int $id): View
    {
        $kategori = KategorilerModel::findOrFail($id);

        return view('back.kategoriler.update', compact('kategori'));
    }

    public function kategoriGuncelle(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        $kategori = KategorilerModel::findOrFail($id);
        $slug = Str::slug($request->kategori);

        $isExist = KategorilerModel::whereSlug($slug)->where('id', '!=', $kategori->id)->first();
        if ($isExist) {
            return redirect()->back()->with('error', $request->kategori.' isimli bir kategori zaten var.');
        }

        $kategori->name = $request->kategori;
        $kategori->slug = $slug;
        $kategori->save();
        Cache::forget('front:kategoriler');

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori güncellendi.');
    }

    public function kategoriSil(int $id): RedirectResponse
    {
        $kategori = KategorilerModel::findOrFail($id);

        if ($kategori->makaleler()->exists()) {
            return redirect()->back()->with('error', 'Bu kategoriye ait makale olduğundan silinemez.');
        }

        $kategori->delete();
        Cache::forget('front:kategoriler');

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori silindi.');
    }
}
