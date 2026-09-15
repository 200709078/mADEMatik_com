@extends('back.layouts.master')
@section('title','MAKALE GÜNCELLEME: '.$makaleler->baslik)
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
            <form action="{{route('admin.makaleguncelleme',$makaleler->id)}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Makale Başlığı</label>
                    <input type="text" name="baslik" class="form-control" value="{{$makaleler->baslik}}" required>
                </div>
                <div class="form-group">
                    <label>Makale Yazarı</label>
                    <input type="text" name="yazar" class="form-control" value="{{$makaleler->yazar}}" required>
                </div>
                <div class="form-group">
                    <label>Makale Kategorisi</label>
                    <select class="form-control" name="kategori"  required>
                        <option value="">Seçim yapınız</option>
                        @foreach($kategoriler as $kategori)
                            <option @if($makaleler->kategori_id==$kategori->id) selected @endif value="{{$kategori->id}}">{{$kategori->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Makale Fotoğrafı</label><br>
                    <img src="{{asset('img/img_makale/'.$makaleler->resim)}}" class="rounded" width="200">
                    <input type="file" name="resim" class="form-control">
                </div>
                <div class="form-group">
                    <label>Makale İçeriği</label>
                    <textarea id="summernote" name="icerik" class="form-control" rows="10">{{$makaleler->makale}}</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Makaleyi Güncelle</button>
                </div>
            </form>
        </div>
    </div>
@endsection
