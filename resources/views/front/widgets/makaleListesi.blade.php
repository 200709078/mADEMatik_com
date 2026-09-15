@if(count($makaleler)>0)
    @foreach($makaleler as $icerik)
        <div class="post-preview">
            <a href="{{route('makale',[$icerik->getKategori->slug,$icerik->slug_baslik])}}">
            <h2 class="post-title">{{$icerik->baslik}}</h2>
            <img src="{{asset('/img/img_makale/'.$icerik->resim)}}" width="600" alt="{{asset($icerik->baslik)}}">
                <h3 class="post-subtitle">
                {!! Illuminate\Support\STR::words(strip_tags($icerik->makale),10," . . .") !!}
                </h3>
            </a>
            <p class="post-meta text-danger">
                Yazar: <b>{{$icerik->yazar}}</b><br>
                Kategori: <b>{{$icerik->getKategori->name}}</b><br>
                Okunma Sayısı: <b>{{$icerik->tiklanma}}</b><br>
                Paylaşım: <b>{{$icerik->created_at->diffForHumans()}}</b>
            </p>
        </div>
        @if(!$loop->last)
            <hr class="my-4"/>
        @endif
    @endforeach
    {{--{{$makaleler->links()}}--}}
@else
    <div class="alert alert-danger">
        <h1>Bu kategoriye ait yazı bulunamadı...!!!</h1>
    </div>
@endif

