<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\KategorilerModel;
use App\Models\MakalelerModel;
use App\Models\SayfalarModel;
use Illuminate\View\View;

class Dashboard extends Controller
{
    public function index(): View
    {
        $degiskenler['sayfasayisi'] = SayfalarModel::count();
        $degiskenler['makalesayisi'] = MakalelerModel::count();
        $degiskenler['kategorisayisi'] = KategorilerModel::count();

        return view('back.dashboard', $degiskenler);
    }
}
