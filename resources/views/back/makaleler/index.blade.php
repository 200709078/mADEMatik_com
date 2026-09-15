@extends('back.layouts.master')
@section('title','TÜM MAKALELER')
@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold float-right text-primary">{{$makaleler->count()}} adet makale bulundu.</h6>
            <a href="{{route('admin.geridonusumoku')}}" class="btn btn-warning btn-sm"><i class="fa fa-trash"> Geri Dönüşüm Kutusu </i> </a>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                    <tr>
                        <th>Fotoğraf</th>
                        <th>Yazar</th>
                        <th>Başlık</th>
                        <th>Kategori</th>
                        <th>Hit</th>
                        <th>Oluşturulma</th>
                        <th>İşlemler</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Fotoğraf</th>
                        <th>Yazar</th>
                        <th>Başlık</th>
                        <th>Kategori</th>
                        <th>Hit</th>
                        <th>Oluşturulma</th>
                        <th>İşlemler</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach($makaleler as $veri)
                        <tr>
                            <td><img src="{{asset('img/img_makale/'.$veri->resim)}}" width="100"></td>
                            <td>{{$veri->yazar}}</td>
                            <td>{{$veri->baslik}}</td>
                            <td>{{$veri->getKategori->name}}</td>
                            <td>{{$veri->tiklanma}}</td>
                            <td>{{$veri->created_at->diffForHumans()}}</td>
                            <td width="120">
                                <a target="_blank" href="{{route('makale',[$veri->getKategori->slug,$veri->slug_baslik])}}" title="Görüntüle" class="btn btn-sm btn-success"><i class="fa fa-eye"> </i></a>
                                <a href="{{route('admin.makaleduzenleme',$veri->id)}}" title="Düzenle" class="btn btn-sm btn-primary"><i
                                        class="fa fa-pen"> </i></a>
                                <a href="{{route('admin.makalesilme',$veri->id)}}" title="Sil" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bu makale geri dönüşüm kutusuna taşınacak. Emin misiniz?')"><i class="fa fa-times"> </i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
