<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\KategorilerModel;
use App\Models\MakalelerModel;
use App\Models\SayfalarModel;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $data['kategoriler'] = KategorilerModel::orderBy('updated_at', 'DESC')->get();
        $data['makaleler'] = MakalelerModel::with('kategori')->orderBy('updated_at', 'DESC')->get();
        $data['sayfalar'] = SayfalarModel::orderBy('updated_at', 'DESC')->get();

        // XML bildirimi controller'da eklenir; Blade içinde <?xml
        // yazımı IDE'lerde hatalı yorumlanıp kırmızı çizgi çıkarır.
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".view('front.sitemap', $data)->render();

        return response($xml)->header('Content-Type', 'text/xml');
    }
}
