<body id="page-top">
<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin.dashboard')}}">
            <div class="sidebar-brand-text mx-2">Adem VAROL</div>
            <div class="sidebar-brand-short mx-2">aVa</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item @if(Request::segment(2)=="panel") active @endif">
            <a class="nav-link" href="{{route('admin.dashboard')}}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>YÖNETİM PANELİ</span></a>
        </li>
        <hr class="sidebar-divider">
        <li class="nav-item">
            <a class="nav-link @if(Request::segment(2)=='makaleler') in @else collapsed @endif" href="#"
               data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                <i class="fas fa-fw fa-address-book"></i>
                <span>Makaleler</span>
            </a>
            <div id="collapseTwo" class="collapse @if(Request::segment(2)=='makaleler') show @endif"
                 aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded collapse-inner-dark">
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
                <div class="py-2 collapse-inner rounded collapse-inner-dark">
                    <a class="collapse-item @if(Request::segment(2)=='sayfalar' and !Request::segment(3)) active @endif" href="{{route('admin.sayfalar.index')}}">Tüm Sayfalar</a>
                    <a class="collapse-item @if(Request::segment(2)=='sayfalar' and Request::segment(3)=='olustur') active @endif" href="{{route('admin.sayfalar.olustur')}}">Sayfa Oluştur</a>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('admin.kategori.index')}}">
                <i class="fas fa-fw fa-list"></i>
                <span>Kategoriler</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('admin.mesajlar.index')}}">
                <i class="fas fa-fw fa-envelope"></i>
                <span>Mesajlar</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{route('admin.ayar.index')}}">
                <i class="fas fa-fw fa-cog active"></i>
                <span>Site Ayarları</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('homepage')}}" target="_blank">
                <i class="fas fa-fw fa-eye"></i>
                <span>Siteyi Görüntüle</span>
            </a>
        </li>
        <li class="nav-item">
            <form method="POST" action="{{route('admin.logout')}}" style="display:inline">
                @csrf
                <button type="submit" class="nav-link btn btn-link text-left w-100" style="border:none;background:none">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>Çıkış Yap</span>
                </button>
            </form>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <div class="d-none d-md-inline sidebar-edge-toggle">
            <button class="rounded-circle" id="sidebarToggle" title="Menüyü aç/kapat" aria-label="Menüyü aç/kapat">
                <svg id="sidebarToggleIcon" width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M10.5 2.5 4 8l6.5 5.5" stroke="#bfd0f7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </ul>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
            </nav>
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">@yield('title')</h1>
                </div>
