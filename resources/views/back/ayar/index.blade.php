@extends('back.layouts.master')
@section('title','SİTE AYARLARI')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="post" action="{{route('admin.ayarlar.guncelle')}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Site Başlığı</label>
                            <input type="text" name="baslik" required class="form-control" value="{{$ayarlar->baslik}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Site Logosu</label><br>
                            <img src="{{asset('img/'.$ayarlar->logo)}}" class="rounded" height="200"><br><br>
                            <div class="dosya-sec">
                                <label for="logoInput" class="btn btn-primary btn-block mb-1">Dosya Seç</label>
                                <input type="file" id="logoInput" name="logo" class="d-none dosya-sec-input">
                                <div class="small text-muted dosya-sec-ad">Dosya seçilmedi</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Site Fav-Iconu</label><br>
                            <img src="{{asset('img/'.$ayarlar->favicon)}}" class="rounded" height="200"><br><br>
                            <div class="dosya-sec">
                                <label for="faviconInput" class="btn btn-primary btn-block mb-1">Dosya Seç</label>
                                <input type="file" id="faviconInput" name="favicon" class="d-none dosya-sec-input">
                                <div class="small text-muted dosya-sec-ad">Dosya seçilmedi</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                        </div>
                    </div>
                </div>
                <hr class="row">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Instagram Adresi</label>
                            <input type="text" name="instagram" required class="form-control" value="{{$ayarlar->instagram}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>YouTube Adresi</label>
                            <input type="text" name="youtube" required class="form-control" value="{{$ayarlar->youtube}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Facebook Adresi</label>
                            <input type="text" name="facebook" required class="form-control" value="{{$ayarlar->facebook}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Twitter Adresi</label>
                            <input type="text" name="twitter" required class="form-control" value="{{$ayarlar->twitter}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>WhatsApp Adresi</label>
                            <input type="text" name="whatsapp" required class="form-control" value="{{$ayarlar->whatsapp}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label></label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-primary">GÜNCELLE</button>
                </div>
            </form>

        </div>
    </div>
@endsection
