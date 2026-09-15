@extends('back.layouts.master')
@section('title','SAYFA OLUŞTUR')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold float-right text-primary">{{$sayfalar->count()}} adet sayfa bulundu.</h6>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $err)
                       <li>{{$err}}</li>
                    @endforeach,
                </div>
            @endif
            <form action="{{route('admin.sayfakaydetme')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Sayfa Başlığı</label>
                    <input type="text" name="baslik" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Sayfa Sırası</label>
                    <input type="text" name="sira" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Sayfa Fotoğrafı</label>
                    <input type="file" name="resim" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Sayfa İçeriği</label>
                    <textarea id="summernote" name="icerik" class="form-control" rows="8"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Sayfayı Oluştur</button>
                </div>
            </form>
        </div>
    </div>
@endsection
