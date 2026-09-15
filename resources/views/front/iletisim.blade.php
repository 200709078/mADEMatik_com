@extends('front.layouts.master')
@section('title',"İletişim")
@section('subTitle',"Adem VAROL")
@section('foto','/img/contact-bg.jpg')
@section('content')

    <div class="col-md-10 col-lg-8 col-xl-7">
        <div class="alert alert-success">
            Bizimle iletişime geçin...!
        </div>
        <div class="my-5">
            <form method="post" action="{{route('iletisimpost')}}" id="contactForm">
                @csrf
                <div class="form-floating">
                    <input class="form-control" name="adsoyad" type="text"
                           placeholder="Adınızı ve soyadınızı giriniz..." required/>
                    <label>Ad Soyad :</label>
                </div>
                <div class="form-floating">
                    <input class="form-control" name="email" type="email" placeholder="Email adresinizi giriniz..." required/>
                    <label>Email Adres :</label>
                </div>
                <div class="form-floating">
                    <input class="form-control" name="telefon" type="tel" maxlength="10" placeholder="Telefon numaranızı giriniz..."/>
                    <label>Telefon Numarası :</label>
                </div>
                <div class="form-floating">
                    <input class="form-control" name="konu" type="text" placeholder="Mesaj konusunu giriniz..." required/>
                    <label>Konu :</label>
                </div>
                <div class="form-floating">
                    <textarea id="summernote" class="form-control" name="mesaj" placeholder="Mesajınızı giriniz..."
                              style="height: 12rem" required></textarea>
                    <label>Mesaj :</label>
                </div>
                <br/>
                <button class="btn btn-primary text-uppercase" type="submit">Gönder</button>
            </form>
        </div>
    </div>

@endsection
