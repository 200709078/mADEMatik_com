<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <title>@yield('title') - {{ $ayarlar?->baslik ?? 'mADEMatik' }}</title>
    <link rel="icon" type="image/x-icon" href="{{asset('/img/'.($ayarlar?->favicon ?? 'favicon.ico'))}}"/>
    <script src="{{asset('/js/all.js')}}" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet"
          type="text/css"/>
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800"
        rel="stylesheet" type="text/css"/>
    <link href="{{asset('/css/styles.css')}}" rel="stylesheet"/>
    <style>
        /* Üst menü: tam saydam yerine hafif koyu zemin */
        @media (min-width: 992px) {
            #mainNav {
                background: rgba(33, 37, 41, 0.45);
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="{{route('homepage')}}">
            <img src="{{ asset('/img/'.($ayarlar?->logo ?? 'logo.png'))}}" width="50">
            Adem VAROL
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            Menu
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto py-4 py-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-lg-3 py-3 py-lg-4" href="{{route('homepage')}}">Ana Sayfa</a>
                </li>

                @foreach($sayfalar as $sayfa)
                    <li class="nav-item">
                        <a class="nav-link px-lg-3 py-3 py-lg-4" href="{{route('sayfa',$sayfa->slug_baslik)}}">{{$sayfa->baslik}}</a>
                    </li>
                @endforeach


                <li class="nav-item">
                    <a class="nav-link px-lg-3 py-3 py-lg-4" href="{{route('iletisim')}}">İletişim</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<header class="masthead" style="background-image: url('@yield('foto')')">
    <div class="container position-relative px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-md-12 mx-auto">
                <div class="site-heading">
                    <h1>
                        @yield('title')
                    </h1>
                    <span class="subheading">
                        @yield('subTitle')
                    </span>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="container px-4 px-lg-5">
    <div class="row gx-4 gx-lg-5 justify-content-center">
