@isset($kategoriler)
<div class="col-md-3">
    <div class="card-header">Kategoriler</div>
    <div class="list-group">
        @foreach($kategoriler as $kategori)
            <li class="list-group-item @if(Request::segment(2)==$kategori->slug) active @endif">
                <a href="{{route('kategoriListe',$kategori->slug)}}">{{$kategori->name}}</a>
                <span class="badge bg-danger float-end text-white">{{$kategori->makaleSay()}}</span>
            </li>
        @endforeach
    </div>
</div>
@endisset
