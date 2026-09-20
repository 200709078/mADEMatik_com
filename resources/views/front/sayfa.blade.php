@extends('front.layouts.master')
@section('title',$sayfa->baslik)
@section('subTitle',"Adem VAROL")
@section('foto',asset('/img/img_sayfa/'.$sayfa->resim))
@section('content')
    <div class="col-md-10 col-lg-8 col-xl-7">
<p>{!! $sayfa->icerik !!}</p>
    </div>
@endsection
