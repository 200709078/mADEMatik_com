<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\KategorilerModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KategorilerController extends Controller
{
    public function index(): View
    {
        $kategoriler['kategoriler'] = KategorilerModel::all();

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

        return redirect()->back()->with('success', 'Kategori eklendi.');
    }

    public function kategoriSil(int $say, int $id): RedirectResponse
    {
        if ($say > 0) {
            return redirect()->back()->with('error', 'Bu kategoriye ait makale olduğundan silinemez.');
        }

        KategorilerModel::find($id)?->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori silindi.');
    }
}
