@extends('front.layouts.master')
@section('title',$makale->baslik)
@section('subTitle',$makale->yazar)
@section('foto',asset('/img/img_makale/'.$makale->resim))
@section('content')
    <div class="col-md-10 col-lg-8 col-xl-7">
        <h2 class="post-title">{{$makale->baslik}}</h2>
        <p>
            {!! $makale->makale !!}
        </p>
        <p class="post-meta text-danger">
            Yazar: <b>{{$makale->yazar}}</b><br>
            Kategori: <b>{{$makale->getKategori->name}}</b><br>
            Okunma Sayısı: <b>{{$makale->tiklanma}}</b><br>
           Paylaşım: <b>{{$makale->created_at->diffForHumans()}}</b>
        </p>
    </div>
    @include('front.widgets.kategoriListesi');
@endsection
