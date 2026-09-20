</div>
</div>
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>made by mADEMatik 2022<sup><a href="{{route('homepage')}}">&copy</a></sup></span>
        </div>
    </div>
</footer>
</div>
</div>
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Çıkış yapmak istiyor musunuz?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Hayır</button>
                <form method="POST" action="{{route('admin.logout')}}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Evet</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="{{asset('js/jsb/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('js/jquery.easing.min.js')}}"></script>
<script src="{{asset('js/sb-admin-2.min.js')}}"></script>
<script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('js/datatables-demo.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

  <script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
    // Dosya Seç butonları: seçilen dosyanın adını yanında göster
    $(document).on('change', '.dosya-sec-input', function () {
        var ad = (this.files && this.files.length) ? this.files[0].name : 'Dosya seçilmedi';
        $(this).closest('.dosya-sec').find('.dosya-sec-ad').text(ad);
    });
    // Mesaj silme modalı: Evet butonuna satırın silme adresini yaz
    $(document).on('show.bs.modal', '#mesajSilModal', function (e) {
        $(this).find('.form-sil-onay').attr('action', $(e.relatedTarget).data('url'));
    });
  </script>




@include('layouts.toast')
</body>
</html>
