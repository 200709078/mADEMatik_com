<body id="page-top">
<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin.dashboard')}}">
            <div class="sidebar-brand-text mx-2">Adem VAROL</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item @if(Request::segment(2)=="panel") active @endif">
            <a class="nav-link" href="{{route('admin.dashboard')}}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>YÖNETİM PANELİ</span></a>
        </li>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">
            İÇERİK YÖNETİMİ
        </div>

        <li class="nav-item">
            <a class="nav-link @if(Request::segment(2)=='makaleler') in @else collapsed @endif" href="#"
               data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                <i class="fas fa-fw fa-address-book"></i>
                <span>Makaleler</span>
            </a>
            <div id="collapseTwo" class="collapse @if(Request::segment(2)=='makaleler') show @endif"
                 aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Makale İşlemleri:</h6>
                    <a class="collapse-item @if(Request::segment(2)=='makaleler' and !Request::segment(3)) active @endif" href="{{route('admin.makaleler.index')}}">Tüm Makaleler</a>
                    <a class="collapse-item @if(Request::segment(2)=='makaleler' and Request::segment(3)=='olustur') active @endif" href="{{route('admin.makaleler.olustur')}}">Makale Oluştur</a>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Request::segment(2)=='sayfalar') in @else collapsed @endif " href="#" data-toggle="collapse" data-target="#collapsePage" aria-expanded="true" aria-controls="collapsePage">
                <i class="fas fa-fw fa-book-medical"></i>
                <span>Sayfalar</span>
            </a>
            <div id="collapsePage" class="collapse @if(Request::segment(2)=='sayfalar') show @endif" aria-labelledby="headingPage" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Sayfa İşlemleri:</h6>
                    <a class="collapse-item @if(Request::segment(2)=='sayfalar' and !Request::segment(3)) active @endif" href="{{route('admin.sayfalar.index')}}">Tüm Sayfalar</a>
                    <a class="collapse-item @if(Request::segment(2)=='sayfalar' and Request::segment(3)=='olustur') active @endif" href="{{route('admin.sayfalar.olustur')}}">Sayfa Oluştur</a>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" @if(Request::segment(2)=='kategoriler') style='color:white !important;' @endif href="{{route('admin.kategori.index')}}">
                <i @if(Request::segment(2)=='kategoriler') style='color:white !important;' @endif class="fas fa-fw fa-list"></i>
                <span>Kategoriler</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{route('admin.ayar.index')}}">
                <i class="fas fa-fw fa-cog active"></i>
                <span>Site Ayarları</span>
            </a>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <div class="d-none d-md-inline sidebar-edge-toggle">
            <button class="rounded-circle" id="sidebarToggle" title="Menüyü aç/kapat" aria-label="Menüyü aç/kapat"></button>
        </div>
    </ul>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{Auth::user()->name}}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-it2em" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Çıkış Yap
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">@yield('title')</h1>
                    <a href="{{route('homepage')}}" target="_blank"
                       class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                            class="fas fa-download fa-sm text-white-50"></i> Siteyi Görüntüle</a>
                </div>
