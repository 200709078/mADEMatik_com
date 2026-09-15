@extends('front.layouts.master')
@section('title',$kategori->name.' Kategorisi')
@section('subTitle','')
@section('foto','/img/home-bg.jpg')
@section('content')
    <div class="col-md-10 col-lg-8 col-xl-7">
        @include('front.widgets.makaleListesi')
    </div>
    @include('front.widgets.kategoriListesi')
@endsection
