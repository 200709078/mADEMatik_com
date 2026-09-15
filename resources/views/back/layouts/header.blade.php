<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title','Yönetim Paneli')</title>

    <link href="{{asset('css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<style>
    /* Açma/kapama oku: sol panelin sağ kenar orta noktası */
    #accordionSidebar {
        position: relative;
    }
    /* Kapalı panelde kısa marka */
    #accordionSidebar .sidebar-brand-short {
        display: none;
        font-weight: 800;
        font-size: 1.2rem;
        letter-spacing: 0.05rem;
        text-transform: none;
    }
    #accordionSidebar.toggled .sidebar-brand-short {
        display: inline;
    }
    /* Alt menü (Makaleler/Sayfalar) zemini: panele uyumlu koyu-mavi */
    #accordionSidebar .collapse-inner-dark {
        background-color: rgba(0, 0, 0, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }
    #accordionSidebar .collapse-inner-dark .collapse-header {
        color: rgba(255, 255, 255, 0.65);
    }
    #accordionSidebar .collapse-inner-dark .collapse-item {
        color: rgba(255, 255, 255, 0.85);
    }
    #accordionSidebar .collapse-inner-dark .collapse-item:hover,
    #accordionSidebar .collapse-inner-dark .collapse-item:focus {
        background-color: rgba(255, 255, 255, 0.14);
        color: #fff;
        text-decoration: none;
    }
    #accordionSidebar .collapse-inner-dark .collapse-item.active {
        color: #fff;
        font-weight: 700;
    }
    .sidebar-edge-toggle {
        position: absolute;
        right: -1.25rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1030;
    }
    .sidebar-edge-toggle #sidebarToggle {
        margin-bottom: 0;
        background-color: #4e73df;
        border: 2px solid #bfd0f7;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        opacity: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    /* Font Awesome glyph kapalı: ok, buton içindeki gerçek SVG'dir */
    #accordionSidebar .sidebar-edge-toggle #sidebarToggle::after {
        content: none;
    }
    #accordionSidebar .sidebar-edge-toggle #sidebarToggleIcon {
        transition: transform 0.2s ease;
    }
    #accordionSidebar .sidebar-edge-toggle #sidebarToggleIcon path {
        transition: stroke 0.15s ease;
    }
    /* Panel kapalıyken ok sağa baksın (durum .toggled class'ından okunur, JS gerekmez) */
    #accordionSidebar.toggled .sidebar-edge-toggle #sidebarToggle #sidebarToggleIcon {
        transform: scaleX(-1);
    }
    .sidebar-edge-toggle #sidebarToggle:hover,
    .sidebar-edge-toggle #sidebarToggle:focus {
        background-color: #4e73df;
        border-color: #fff;
        text-decoration: none;
    }
    #accordionSidebar .sidebar-edge-toggle #sidebarToggle:hover #sidebarToggleIcon path {
        stroke: #fff;
    }
</style>


</head>
