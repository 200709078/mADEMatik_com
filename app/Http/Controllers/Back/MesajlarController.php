<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\MesajlarModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MesajlarController extends Controller
{
    public function mesajOkuma(): View
    {
        $mesajlar['mesajlar'] = MesajlarModel::orderBy('created_at', 'DESC')->get();

        return view('back.mesajlar.index', $mesajlar);
    }

    public function mesajDuzenleme(int $id): View
    {
        $mesaj['mesajlar'] = MesajlarModel::findOrFail($id);

        return view('back.mesajlar.update', $mesaj);
    }

    public function mesajGuncelleme(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'adsoyad' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefon' => 'nullable|string|max:60',
            'konu' => 'required|string|max:255',
            'mesaj' => 'required|string',
        ]);

        $mesaj = MesajlarModel::findOrFail($id);
        $mesaj->adsoyad = $validated['adsoyad'];
        $mesaj->email = $validated['email'];
        $mesaj->telefon = $validated['telefon'] ?? null;
        $mesaj->konu = $validated['konu'];
        $mesaj->mesaj = $validated['mesaj'];
        $mesaj->save();

        return redirect()->route('admin.mesajlar.index')->with('success', 'Mesaj güncellendi.');
    }

    public function mesajSilme(int $id): RedirectResponse
    {
        MesajlarModel::findOrFail($id)->delete();

        return redirect()->route('admin.mesajlar.index')->with('success', 'Mesaj silindi.');
    }
}
