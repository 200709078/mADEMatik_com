@isset($kategoriler)
<style>
    .kategori-card {
        border: 1px solid #dee2e6;
        border-top: 3px solid #0085A1;
    }
    .kategori-card .card-header {
        background-color: #fff;
        color: #0085A1;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.1em;
    }
    .kategori-card .list-group-item {
        color: #212529;
        border-left: 0;
        border-right: 0;
    }
    .kategori-card .list-group-item:hover {
        color: #0085A1;
        background-color: #f8f9fa;
    }
    .kategori-card .list-group-item.active {
        background-color: #0085A1;
        border-color: #0085A1;
        color: #fff;
    }
    .kategori-card .list-group-item.active:hover {
        color: #fff;
    }
    .kategori-card .badge {
        background-color: #e9ecef;
        color: #495057;
    }
    .kategori-card .list-group-item.active .badge {
        background-color: #fff;
        color: #0085A1;
    }
</style>
<div class="col-md-3 mb-4">
    <div class="card kategori-card shadow-sm">
        <div class="card-header">Kategoriler</div>
        <div class="list-group list-group-flush">
            @foreach($kategoriler as $kategori)
                <a href="{{route('kategoriListe',$kategori->slug)}}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center @if(Request::segment(2)==$kategori->slug) active @endif">
                    <span>{{$kategori->name}}</span>
                    <span class="badge rounded-pill">{{$kategori->makaleSay()}}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endisset
