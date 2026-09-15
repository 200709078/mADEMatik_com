@extends('back.layouts.master')
@section('title','Tüm Kategoriler')
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"> Yeni Kategori Ekle</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{route('admin.kategori.ekle')}}">
                        @csrf
                        <div class="form-group">
                            <label> Kategori Adı</label><br>
                            <input type="text" class="form-control" name="kategori" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">EKLE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"> @yield('title')</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                            <tr>
                                <th>Kategori Adı</th>
                                <th>Makale Sayısı</th>
                                <th>İşlemler</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($kategoriler as $kategori)
                                <tr>
                                    <td>{{$kategori->name}}</td>
                                    <td>{{$kategori->makaleSay()}}</td>
                                    <td>
                                        <a href="{{route('admin.kategori.sil',[$kategori->makaleSay(), $kategori->id])}}"
                                           title="Kategori Sil" class="btn btn-sm btn-danger sil"><i
                                                class="fa fa-times"> </i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
