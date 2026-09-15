@extends('front.layouts.master')
@section('title','Ana Sayfa')
@section('subTitle','Adem VAROL')
@section('foto','/img/home-bg.jpg')
@section('content')
    <div class="col-md-10 col-lg-8 col-xl-7">
        @include('front.widgets.makaleListesi')
    </div>
    @include('front.widgets.kategoriListesi')
@endsection
