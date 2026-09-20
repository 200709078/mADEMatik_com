@extends('back.layouts.master')
@section('title','KATEGORİ DÜZENLE: '.$kategori->name)
@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $err)
                        <li>{{$err}}</li>
                    @endforeach
                </div>
            @endif
            <form action="{{route('admin.kategori.guncelle',$kategori->id)}}" method="post">
                @csrf
                <div class="form-group">
                    <label>Kategori Adı</label>
                    <input type="text" name="kategori" class="form-control" value="{{old('kategori',$kategori->name)}}" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Kategoriyi Güncelle</button>
                </div>
            </form>
        </div>
    </div>
@endsection
