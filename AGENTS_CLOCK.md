# AGENTS_CLOCK.md

## Amaç

Bu dosya, **mADEMatik Saat & Sayaç** web modülünün geliştirme gereksinimlerini tanımlar.

Web modülü mevcut Laravel 13 projesinin içinde yer alacak ve şu adresten erişilecek:

`/saat`

Hedef genel URL:

`https://madematik.com/saat`

Bu aşama **YALNIZCA WEB SÜRÜMÜ** içindir.

Bu aşamada Windows/Linux masaüstü uygulaması oluşturma. Ancak frontend kodunu, daha sonra Tauri tabanlı bir masaüstü uygulamasında mümkün olan en az değişiklikle tekrar kullanılabilecek şekilde yapılandır.

Herhangi bir değişiklik yapmadan önce kök dizindeki `AGENTS.md` dosyasını oku ve mevcut proje yapısını incele. Bu dosyayla çelişmediği sürece projenin mevcut kurallarına ve kodlama düzenine uy.

---

# 1. Kapsam

mADEMatik için tek sayfalık bir Saat + Geri Sayım aracı geliştir.

Sayfada iki ana görünüm bulunmalı:

- Saat
- Geri Sayım Sayacı

Kullanıcı bu iki görünüm arasında ayrı sayfalar yüklenmeden geçiş yapabilmeli.

Mevcut mADEMatik ana menüsüne tek bir bağlantı ekle:

- Türkçe: `Saat & Sayaç`
- İngilizce: `Clock & Timer`

Bu menü öğesi şu adresi açmalı:

`/saat`

Mevcut proje mimarisi teknik olarak zorunlu kılmadıkça `/sayac` gibi ayrı bir üst seviye URL oluşturma. Amaç, tek sayfa içinde çalışan tek bir araç oluşturmaktır.

---

# 2. Sayfa İçi Navigasyon

`/saat` sayfası içinde açıkça görülebilen üç navigasyon kontrolü bulunmalı:

- `SAAT` / `CLOCK`
- `SAYAÇ` / `TIMER`
- `ANA SAYFA` / `HOME`

Davranış:

- `SAAT`, orta içerik alanını saat görünümüne geçirir.
- `SAYAÇ`, orta içerik alanını geri sayım görünümüne geçirir.
- `ANA SAYFA`, mADEMatik ana sayfasına döner.

Bu kontroller masaüstü, dizüstü, tablet, mobil, projeksiyon ve akıllı tahta düzenlerinde kullanılabilir kalmalı.

Saat/sayaç ölçekleme kontrolleri bu navigasyonu ekran dışına itmemeli.

---

# 3. Önce Web, Sonra Masaüstü

Önce web sürümü tamamlanmalı.

Bu görev sırasında şunları ekleme:

- Tauri
- Electron
- Windows paketleme
- Linux paketleme
- AppImage
- `.deb`
- `.exe`
- MSI
- masaüstüne özel başka paketleme kodları

Ancak Saat/Sayaç iş mantığını Blade şablonlarının veya Laravel controller'larının içine gömme.

Tercihen tekrar kullanılabilir bir frontend yapısı oluştur:

- web tarafında host/mount noktası olarak kullanılan bir Blade sayfası;
- saat/sayaç davranışları için modüler JavaScript/TypeScript;
- Saat/Sayaç arayüzü için modüler CSS;
- state, çeviri, dünya saatleri, gün doğumu/batımı ve kalıcı kayıt için küçük bağımsız modüller.

Mümkün olduğunca mevcut Vite/frontend yapısına uy.

Örnek bir yapı şu şekilde olabilir:

```text
resources/
├── views/
│   └── tools/
│       └── clock-timer.blade.php
├── js/
│   └── clock-timer/
│       ├── index.*
│       ├── clock.*
│       ├── timer.*
│       ├── storage.*
│       ├── i18n.*
│       └── world-clock.*
└── css/
    └── clock-timer.*
```

Bu sadece örnektir; birebir aynı klasör yapısı zorunlu değildir. Mevcut proje düzenine uygun bir yapı tercih et.

Temel amaç: sorumlulukların ayrılması ve kodun ileride masaüstü uygulamasında yeniden kullanılabilmesi.

---

# 4. Saat Görünümü

Saat görünümünde büyük ve belirgin bir dijital saat bulunmalı.

Asgari gösterim:

`HH:mm:ss`

Türkçe kullanıcılar için varsayılan gösterim 24 saat formatında olsun.

Ana saat düzgün güncellenmeli ve tarayıcı timer'ları yavaşlatsa bile doğru kalmalı. Saati dahili sayaç artırarak hesaplama. Her güncellemede gerçek sistem zamanından türet.

Saatin altında veya yakınında yerelleştirilmiş takvim bilgileri göster:

- tam tarih;
- gün adı;
- hafta numarası.

Türkçe örnek:

```text
3 Ekim 2026, Cumartesi
40. Hafta
```

İngilizce örnek:

```text
3 October 2026, Saturday
Week 40
```

Projenin mevcut özel bir standardı yoksa doğru ISO hafta numarası hesaplaması kullan.

---

# 5. Saat Altındaki Ek Bilgiler

Referans tasarımdan işlevsel olarak ilham alan, ancak onu birebir kopyalamayan temiz bir ikincil bilgi alanı oluştur.

Şunları göster:

- gün doğumu saati;
- gün batımı saati;
- seçili/varsayılan konum adı;
- dünya saatleri satırı.

Bu ek bilgiler, görsel hiyerarşide ana saatten daha geri planda kalmalı.

Ana ekranı reklam, video veya ilgisiz içeriklerle doldurma.

---

# 6. Gün Doğumu / Gün Batımı

Gün doğumu ve gün batımı hesaplaması her saniye sunucu isteği gerektirmemeli.

Tercihen istemci tarafında astronomik hesaplama veya küçük, güvenilir ve yerel paketlenebilen bir bağımlılık kullan.

Aynı sonuç lokal hesaplanabiliyorsa uzak API'ye bağımlı olma. Bu önemlidir; çünkü aynı frontend ileride çevrimdışı çalışabilen masaüstü uygulamasında kullanılacak.

Sayfa açılır açılmaz tarayıcı konum izni isteme.

Yapılandırılabilir bir varsayılan konum kullan ve gerekiyorsa kullanıcıya konumu hafif bir arayüzle değiştirme imkânı ver.

Seçilen konumu lokal olarak sakla.

Mevcut projede açıkça uygun bir konum/site ayarı varsa onu kullan.

Yoksa tek bir merkezî yapılandırma noktasından kolayca değiştirilebilecek mantıklı bir varsayılan seç. Koordinatları kodun farklı yerlerine dağınık biçimde gömme.

---

# 7. Dünya Saatleri

Ana bilgi alanının altında kompakt bir dünya saatleri satırı veya grid'i göster.

İlk şehir seti için mantıklı örnekler:

- New York
- Londra
- Berlin
- İstanbul
- Mekke
- Pekin
- Sidney

IANA timezone kimliklerini ve `Intl.DateTimeFormat` veya eşdeğer güvenilir bir yöntemi kullan. Böylece yaz/kış saati değişiklikleri doğru şekilde ele alınır.

Dünya saatlerini sabit saat farkı ekleyerek elle hesaplama.

Mümkünse şehir adlarını dile göre yerelleştir.

Şehir listesini tek bir yapılandırma kaynağından değiştirilebilir şekilde tasarla.

---

# 8. Geri Sayım Sayacı Görünümü

Sayaç görünümünde büyük ve net bir geri sayım ekranı bulunmalı.

Asgari kontroller:

- süre ayarlama;
- başlat;
- duraklat;
- devam et;
- sıfırla.

Sayaç sınıf ve akıllı tahta kullanımına uygun olmalı.

Süre girişini pratik hale getir. Dakika/saniye alanları ve/veya hızlı süre butonları kullanılabilir.

Kullanışlı hızlı seçenekler örneğin:

- 1 dakika
- 5 dakika
- 10 dakika
- 15 dakika
- 20 dakika
- 40 dakika

Hızlı seçenekler ikincil özelliktir. Asıl öncelik doğru sayaç davranışıdır.

Sayaç başlatılırken, duraklatılırken veya sıfırlanırken sayfa yeniden yüklenmemeli.

---

# 9. Geri Sayımın Kalıcı Olması

Bu gereksinim kritiktir.

Çalışan bir sayaç başlatıldığında:

- sayfa kapatılsa,
- yenilense,
- tarayıcı yeniden başlatılsa,
- cihaz uykuya girip çıksa

sayfa tekrar açıldığında gerçek zamana göre doğru kalan süre görünmelidir.

Yalnızca `remainingSeconds` gibi azalan bir sayı saklama.

Çalışan sayaç için mutlak hedef bitiş zamanını sakla.

Mantık kavramsal olarak:

```text
endAt = Date.now() + duration
```

Her render/güncellemede:

```text
remaining = max(0, endAt - Date.now())
```

şeklinde hesaplanmalı.

Böylece sayaç:

- sayfa yenilemede,
- tarayıcı timer yavaşlatmasında,
- sekme askıya alındığında,
- sleep/resume sonrasında,
- sayfa kapatılıp açıldığında

doğru kalır.

Sayaç duraklatıldığında kalan süre ayrıca saklanmalı ve duraklatılmış durumda zaman ilerlememeli.

Şu durumları ayırt edebilecek yeterli state sakla:

- boşta;
- çalışıyor;
- duraklatılmış;
- tamamlandı.

mADEMatik'in diğer localStorage alanlarıyla çakışmaması için isim alanı kullan.

Örneğin:

`madematik.clockTimer.*`

Bu özelliğin state'ini veritabanında saklama.

---

# 10. Sayaç Bittiğinde

Sayaç sıfıra ulaştığında:

- `00:00` veya süreye uygun `HH:MM:SS` göster;
- tamamlanmış duruma geç;
- kısa ve açık bir görsel uyarı göster;
- istenirse kısa ve rahatsız etmeyen bir bitiş sesi çal.

Ses eklenirse:

- sessize alma/ses açma seçeneği olsun;
- kullanıcı etkileşimi olmadan tarayıcı politikalarını ihlal edecek autoplay davranışı kullanma;
- ses tercihini lokal olarak sakla.

Sürekli tekrarlayan agresif alarm sesi kullanma.

---

# 11. Zoom + / Zoom -

Hem Saat hem de Sayaç görünümünde açık ölçek kontrolleri bulunmalı:

- Zoom -
- Zoom +

Amaç, ana saat/sayaç ekranını projeksiyon ve akıllı tahtada uzaktan okunabilir hale getirmektir.

Önemli:

- Tarayıcı genel zoom özelliğini kullanma.
- Ana Saat/Sayaç içerik alanını ölçekle.
- Navigasyon ve temel kontroller erişilebilir kalmalı.
- Zoom tercihini lokal olarak sakla.
- Saat ve Sayaç için ayrı zoom değerleri tutulabilir.

Yaklaşık `%60 – %200` gibi mantıklı limitler veya eşdeğer responsive ölçek sistemi kullanılabilir.

Kullanıcının arayüzü kullanılamaz boyuta küçültmesine/büyütmesine izin verme.

---

# 12. Tam Ekran

Açıkça görülebilen bir tam ekran kontrolü ekle.

Desteklenen tarayıcılarda Fullscreen API kullan.

Tam ekran modu şunlarda iyi çalışmalı:

- akıllı tahta;
- sınıf projeksiyonu;
- masaüstü monitörü.

Tam ekrana girerken veya çıkarken düzen bozulmamalı.

Tam ekran desteği yoksa uygulama hata vermeden normal çalışmaya devam etmeli.

---

# 13. Türkçe / İngilizce

Saat & Sayaç sayfası şu dilleri desteklemeli:

- Türkçe (`tr`)
- İngilizce (`en`)

Açık bir dil değiştirici kullan:

`TR | EN`

En az şu alanları yerelleştir:

- sayfa başlığı;
- navigasyon butonları;
- Saat/Sayaç metinleri;
- Başlat;
- Duraklat;
- Devam Et;
- Sıfırla;
- tam ekran metinleri/tooltip'leri;
- Gün doğumu;
- Gün batımı;
- Hafta;
- gün ve ay adları;
- sayaç hazır süreleri;
- sayaç tamamlandı metni;
- mümkünse konum etiketleri.

Dil tercihini lokal olarak sakla.

Ana mADEMatik sitesinde zaten bir locale mekanizması varsa uygun şekilde entegre et; ancak Saat/Sayaç çeviri katmanını ileride masaüstünde tekrar kullanılabilecek kadar bağımsız tut.

Aynı mantığın kopyalandığı iki ayrı sayfa oluşturma.

---

# 14. Responsive / Akıllı Tahta Tasarımı

Temel kullanım senaryoları:

- normal web tarayıcısı;
- sınıf bilgisayarı;
- projeksiyon;
- akıllı tahta;
- dizüstü;
- tablet;
- mobil.

Responsive tasarla; özellikle büyük ekran kullanımını iyi optimize et.

Ana saat/sayaç uzaktan okunabilir olmalı.

Şunlardan kaçın:

- çok küçük kontroller;
- aşırı yoğun kart yapısı;
- yatay taşma;
- 16:9 ekranda bozulan sabit düzenler;
- yalnızca hover ile erişilebilen kontroller.

Dokunmatik hedefler akıllı tahta kullanımı için yeterince büyük olmalı.

---

# 15. Görsel Yön

Mevcutsa mADEMatik'in görsel dilini kullan.

Saat & Sayaç sayfası mADEMatik'in bir parçası gibi görünmeli ancak aynı zamanda odaklanmış bir yardımcı araç olarak kalmalı.

Görsel hiyerarşi:

1. ana saat/sayaç;
2. tarih/durum;
3. ikincil bilgiler;
4. kontroller.

Gönderilen referans görsel yalnızca işlevsel ilham içindir.

Referans sitenin tasarımını, markasını veya yerleşimini piksel piksel kopyalama.

Reklam veya alakasız medya alanları ekleme.

Temiz, modern ve dikkat dağıtmayan bir sınıf aracı oluştur.

---

# 16. Ana Site Menüsüne Entegrasyon

Mevcut site navigasyonuna tek bir:

`Saat & Sayaç` / `Clock & Timer`

bağlantısı ekle.

Değişiklik yapmadan önce mevcut header/navbar yapısını incele.

Mevcut navigasyon bileşenini kopyalama veya baştan değiştirme.

Mevcut menü davranışını, mobil menüyü ve stil kurallarını koru.

Saat & Sayaç özelliği mevcut route'ları veya menüleri bozmamalı.

---

# 17. Mevcut Public Uygulamaları

`public` altında bulunan mevcut bağımsız uygulamalara dokunma:

- `drAW`
- `GeoMatik2d` veya mevcut gerçek büyük/küçük harf kullanımı
- `geomatik3d`
- `avCELL`

Bunları taşıma, yeniden adlandırma veya refactor etme.

Mevcut doğrudan URL davranışlarını etkileme.

Bu görev kapsamında `/kelebek` yolunu da tanımlama veya rezerve etme.

Bu görev yalnızca Saat & Sayaç web özelliği ve ona ulaşmak için gereken küçük navigasyon değişikliği ile sınırlıdır.

---

# 18. Backend / Veritabanı

Saat & Sayaç özelliği normal çalışması için yeni veritabanı tablolarına ihtiyaç duymamalı.

Şunları tarayıcı tarafında lokal sakla:

- sayaç state'i;
- zoom tercihleri;
- dil;
- ses tercihi;
- seçilen konum;
- varsa son seçilen Saat/Sayaç görünümü.

Gerçek bir backend gereksinimi ortaya çıkmadıkça migration oluşturma.

İlgisiz veritabanı şemasına dokunma.

Canlı sunucu veya production veritabanı üzerinde işlem yapma.

---

# 19. Güvenlik / Gizlilik

Sayaç state'ini veya kullanıcı tercihlerini üçüncü taraf servislere gönderme.

Sayfa açıldığında otomatik olarak kesin konum izni isteme.

Bu özellik kapsamında analytics, tracking veya reklam ekleme.

Mevcut build sistemi gerekli dosyaları yerel olarak paketleyebiliyorsa harici CDN bağımlılıklarını tercih etme.

Bu yaklaşım gelecekteki çevrimdışı masaüstü uygulaması için de önemlidir.

---

# 20. Erişilebilirlik

Temel erişilebilirlik kurallarına uy:

- semantik button elemanları;
- görünür focus durumları;
- klavyeyle kullanılabilir kontroller;
- yalnızca ikon içeren butonlara anlamlı `aria-label`;
- yeterli kontrast;
- yalnızca renkle ifade edilmeyen durum bilgileri.

Gereksiz karmaşıklık uğruna büyük ekran tasarımını bozma; ancak ana kontroller erişilebilir olmalı.

---

# 21. Performans

Sayfa hızlı yüklenmeli ve hafif kalmalı.

Saati güncellemek için her saniye sunucu isteği gönderme.

Saat ve geri sayım tamamen istemci tarafında güncellenmeli.

Bu özellik için gereksiz büyük framework veya bağımlılık ekleme.

Mevcut Laravel projesi zaten bir frontend framework kullanıyorsa, ancak gerçekten en temiz çözüm buysa onu kullan. Saat & Sayaç için tüm siteyi yeni bir frontend framework'e taşıma.

---

# 22. State Geri Yükleme

Sayfa ilk açıldığında:

1. kayıtlı tercihleri yükle;
2. dili geri yükle;
3. varsa son Saat/Sayaç görünümünü geri yükle;
4. zoom değerlerini geri yükle;
5. seçili konumu geri yükle;
6. sayaç state'ini incele;
7. sayaç çalışıyorsa `endAt - Date.now()` ile kalan süreyi hesapla;
8. kalan süre sıfır veya negatifse tamamlandı durumuna geçir;
9. sayaç duraklatılmışsa kayıtlı kalan süreyi geri yükle;
10. doğru görünümü render et.

Çalışan sayaçta kalan süreyi saniyede bir değişken azaltarak tutma. Her zaman timestamp üzerinden hesapla.

---

# 23. Uzun Süreler

Tüm sayaçların bir saatten kısa olduğunu varsayma.

Süreleri mantıklı formatla:

- 1 saatten kısa: `MM:SS`
- 1 saat veya daha uzun: `HH:MM:SS`

Çok saatlik sürelerde taşma veya format hatası oluşturma.

Gerekirse mantıklı bir maksimum süre belirle ve kod içinde belgelenebilir şekilde tut.

---

# 24. Hata Yönetimi

Şu durumlarda bile ana uygulama çalışmaya devam etmeli:

- fullscreen desteklenmiyor;
- localStorage kapalı veya engellenmiş;
- gün doğumu/batımı hesaplaması başarısız;
- bozuk kayıtlı tercih var;
- eski localStorage formatı uyumsuz.

Güvenli varsayılanlara dön ve mümkün olduğunca otomatik toparlan.

İkincil bir özelliğin hatası ana saat veya sayacın çalışmasını engellememeli.

---

# 25. Uygulama Süreci

Kodlamaya başlamadan önce:

- mevcut Laravel 13 yapısını incele;
- `AGENTS.md` dosyasını oku;
- mevcut site navigasyon/header bileşenini bul;
- mevcut frontend build düzenini belirle;
- varsa localization yaklaşımını incele;
- `/saat` yolunun boş olduğunu doğrula;
- tekrar kullanılabilecek mevcut tasarım token/style'larını incele.

Ardından özelliği aşamalı geliştir.

mADEMatik'in ilgisiz bölümlerini yeniden yazma.

Production deploy yapma.

cPanel veya canlı sunucuya bağlanma.

Masaüstü uygulamasını henüz geliştirme.

---

# 26. İlk Testler

Bu aşamada gereksiz büyük test altyapısı kurmadan pratik smoke testler yap.

En az şunları doğrula:

- `/saat` başarıyla açılıyor;
- menü linki `/saat` adresini açıyor;
- Saat görünümü doğru güncelleniyor;
- saniyeler doğru ilerliyor;
- tarih/gün/hafta doğru;
- TR/EN değişimi çalışıyor;
- dünya saatleri doğru timezone ile gösteriliyor;
- gün doğumu/batımı bölümü hata halinde güvenli şekilde geri dönüyor;
- sayaç süresi ayarlanabiliyor;
- sayaç başlıyor;
- sayaç duraklıyor;
- sayaç devam ediyor;
- sayaç sıfırlanıyor;
- çalışan sayaç sayfa yenilendiğinde doğru devam ediyor;
- çalışan sayaç sayfa kapatılıp tekrar açıldığında doğru devam ediyor;
- duraklatılmış sayaç tekrar açıldığında duraklatılmış kalıyor;
- süresi bitmiş sayaç tamamlandı durumunda geri geliyor;
- Zoom + / Zoom - çalışıyor;
- zoom tercihi korunuyor;
- fullscreen desteklenen tarayıcıda çalışıyor;
- 16:9 büyük ekranda düzen kullanılabilir;
- mobil görünüm kullanılabilir;
- mevcut mADEMatik navigasyonu bozulmamış;
- `drAW`, `GeoMatik2d`, `geomatik3d`, `avCELL` değişmemiş.

Bu testlerden sonra Windows/Linux paketleme aşamasına başlama.

---

# 27. İş Bitince Rapor

Web sürümü tamamlandığında dur ve masaüstü aşamasına geçmeden rapor ver.

Raporda şunlar yer alsın:

- oluşturulan dosyalar;
- değiştirilen dosyalar;
- eklenen route;
- menü/header değişikliği;
- frontend modül yapısı;
- kullanılan localStorage anahtarları;
- çeviri sistemi;
- gün doğumu/batımı çözümü;
- dünya saatleri çözümü;
- sayaç kalıcılığı yaklaşımı;
- yapılan smoke testler;
- bilinen sorunlar;
- gelecekteki masaüstü aşamasına geçmeden önce yeniden değerlendirilmesi gereken kararlar.

Desktop/Tauri aşamasının **başlatılmadığını açıkça belirt**.

Windows/Linux masaüstü geliştirmesine başlamadan önce kullanıcı onayını bekle.

---

# 28. Gelecekteki Masaüstü Aşaması — Şimdilik Uygulama

Gelecekte bu frontend'in şu ortamlarda yeniden kullanılması hedefleniyor:

- Windows uygulaması;
- Linux uygulaması;
- büyük olasılıkla Tauri kullanılarak.

Bu nedenle:

- Saat/Sayaç temel iş mantığını Laravel backend'e bağımlı yapma;
- mümkün olduğunca uzak servislere bağımlı olma;
- çeviri verilerini tekrar kullanılabilir tut;
- mümkünse persistence işlemlerini küçük bir soyutlama katmanı arkasında tut;
- browser fullscreen gibi yalnızca web'e ait işlevleri izole et;
- temel sayaç mantığının içine Laravel URL'leri hard-code etme.

Bu bölüm yalnızca mimari yönlendirmedir.

**Bu görev sırasında masaüstü uygulamasını oluşturma.**
