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
                <a class="btn btn-primary" href="{{route('admin.logout')}}">Evet</a>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="{{asset('js/jsb/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('js/jquery.easing.min.js')}}"></script>
<script src="{{asset('js/sb-admin-2.min.js')}}"></script>
<script src="{{asset('js/Chart.min.js')}}"></script>
<script src="{{asset('js/chart-area-demo.js')}}"></script>
<script src="{{asset('js/chart-pie-demo.js')}}"></script>
<script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('js/datatables-demo.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>

{{--<script>
    $(function () {
        $('.sil').click(function () {
            kategoriid = $(this)[0].getAttribute('kategoriid');
            kategorisay = $(this)[0].getAttribute('kategorisay');
            if (kategorisay > 0) {
                //toastr()->success('Bu kategoriye ait makale olduğundan silinemez.', 'made by mADEMatik design.');
            } else {
                var url = '/admin/kategoriler/kategorisil/' + kategoriid;
                location.href = url;
            }
        })
    });
</script>--}}


  <script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
  </script>




@include('layouts.toast')
</body>
</html>
