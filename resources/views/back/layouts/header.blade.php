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
    .sidebar-edge-toggle {
        position: absolute;
        right: -1.25rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1030;
    }
    .sidebar-edge-toggle #sidebarToggle {
        margin-bottom: 0;
        background-color: #fff;
        border: 2px solid #4e73df;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        opacity: 1;
    }
    .sidebar-edge-toggle #sidebarToggle::after {
        color: #4e73df;
        margin-right: 0;
    }
    .sidebar-edge-toggle #sidebarToggle:hover,
    .sidebar-edge-toggle #sidebarToggle:focus {
        background-color: #e8edfb;
        border-color: #2e59d9;
        text-decoration: none;
    }
    .sidebar-edge-toggle #sidebarToggle:hover::after {
        color: #2e59d9;
    }
</style>


</head>
