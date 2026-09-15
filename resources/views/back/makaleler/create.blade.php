@extends('back.layouts.master')
@section('title','MAKALE OLUŞTUR')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold float-right text-primary">{{$makaleler->count()}} adet makale bulundu.</h6>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $err)
                       <li>{{$err}}</li>
                    @endforeach,
                </div>
            @endif
            <form action="{{route('admin.makalekaydetme')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Makale Başlığı</label>
                    <input type="text" name="baslik" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Makale Yazarı</label>
                    <input type="text" name="yazar" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Makale Kategorisi</label>
                    <select class="form-control" name="kategori" required>
                        <option value="">Seçim yapınız</option>
                        @foreach($kategoriler as $kategori)
                            <option value="{{$kategori->id}}">{{$kategori->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Makale Fotoğrafı</label>
                    <input type="file" name="resim" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Makale İçeriği</label>
                    <textarea id="summernote" name="icerik" class="form-control" rows="10"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Makaleyi Oluştur</button>
                </div>
            </form>
        </div>
    </div>
@endsection
