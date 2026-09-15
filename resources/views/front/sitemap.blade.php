<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('homepage') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('iletisim') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @foreach($kategoriler as $kategori)
    <url>
        <loc>{{ route('kategoriListe', $kategori->slug) }}</loc>
        <lastmod>{{ $kategori->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    @foreach($makaleler as $makale)
    <url>
        <loc>{{ route('makale', [$makale->kategori->slug, $makale->slug_baslik]) }}</loc>
        <lastmod>{{ $makale->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    @foreach($sayfalar as $sayfa)
    <url>
        <loc>{{ route('sayfa', $sayfa->slug_baslik) }}</loc>
        <lastmod>{{ $sayfa->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
