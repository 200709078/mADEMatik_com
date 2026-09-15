@extends('back.layouts.master')
@section('title','MESAJ DÜZENLEME')
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
            <form action="{{route('admin.mesajguncelleme',$mesajlar->id)}}" method="post">
                @csrf
                <div class="form-group">
                    <label>Ad Soyad</label>
                    <input type="text" name="adsoyad" class="form-control" value="{{$mesajlar->adsoyad}}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{$mesajlar->email}}" required>
                </div>
                <div class="form-group">
                    <label>Telefon</label>
                    <input type="text" name="telefon" class="form-control" value="{{$mesajlar->telefon}}">
                </div>
                <div class="form-group">
                    <label>Konu</label>
                    <input type="text" name="konu" class="form-control" value="{{$mesajlar->konu}}" required>
                </div>
                <div class="form-group">
                    <label>Mesaj</label>
                    <textarea name="mesaj" class="form-control" rows="8" required>{{$mesajlar->mesaj}}</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Mesajı Güncelle</button>
                </div>
            </form>
        </div>
    </div>
@endsection
