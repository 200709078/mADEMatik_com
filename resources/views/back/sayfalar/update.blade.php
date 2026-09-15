@extends('back.layouts.master')
@section('title','SAYFA GÜNCELLEME: '.$sayfalar->baslik)
@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $err)
                       <li>{{$err}}</li>
                    @endforeach,
                </div>
            @endif
            <form action="{{route('admin.sayfaguncelleme',$sayfalar->id)}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Sayfa Başlığı</label>
                    <input type="text" name="baslik" class="form-control" value="{{$sayfalar->baslik}}" required>
                </div>
                <div class="form-group">
                    <label>Sayfa Sırası</label>
                    <input type="text" name="sira" class="form-control" value="{{$sayfalar->sira}}" required>
                </div>
                <div class="form-group">
                    <label>Sayfa Fotoğrafı</label><br>
                    <img src="{{asset('img/img_sayfa/'.$sayfalar->resim)}}" class="rounded" width="200">
                    <input type="file" name="resim" class="form-control">
                </div>
                <div class="form-group">
                    <label>Sayfa İçeriği</label>
                    <textarea id="summernote" name="icerik" class="form-control" rows="8">{{$sayfalar->icerik}}</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Sayfayı Güncelle</button>
                </div>
            </form>
        </div>
    </div>
@endsection
