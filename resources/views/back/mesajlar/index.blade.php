@extends('back.layouts.master')
@section('title','TÜM MESAJLAR')
@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold float-right text-primary">{{$mesajlar->count()}} adet mesaj bulundu.</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="mesajTable">
                    <thead>
                    <tr>
                        <th>Ad Soyad</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Konu</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Ad Soyad</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Konu</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach($mesajlar as $veri)
                        <tr>
                            <td>{{$veri->adsoyad}}</td>
                            <td>{{$veri->email}}</td>
                            <td>{{$veri->telefon}}</td>
                            <td>{{$veri->konu}}</td>
                            <td>{{$veri->created_at->diffForHumans()}}</td>
                            <td width="120">
                                <a href="{{route('admin.mesajduzenleme',$veri->id)}}" title="Düzenle" class="btn btn-sm btn-primary"><i
                                        class="fa fa-pen"> </i></a>
                                <a href="#" title="Sil" class="btn btn-sm btn-danger" data-toggle="modal"
                                   data-target="#mesajSilModal" data-url="{{route('admin.mesajsilme',$veri->id)}}"><i class="fa fa-times"> </i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mesajSilModal" tabindex="-1" role="dialog" aria-labelledby="mesajSilModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mesajSilModalLabel">Bu mesaj kalıcı olarak silinecek. Emin misiniz?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Kapat">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Hayır</button>
                    <form method="POST" class="form-sil-onay" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Evet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
