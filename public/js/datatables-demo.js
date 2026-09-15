// Call the dataTables jQuery plugin
var madematikTableLang = {
  emptyTable: 'Tabloda kayıt bulunamadı',
  info: '_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor',
  infoEmpty: '0 kayıttan 0 - 0 arası gösteriliyor',
  infoFiltered: '(_MAX_ kayıt arasından filtrelendi)',
  lengthMenu: 'Sayfada _MENU_ kayıt göster',
  loadingRecords: 'Yükleniyor...',
  processing: 'İşleniyor...',
  search: 'Ara:',
  zeroRecords: 'Eşleşen kayıt bulunamadı',
  paginate: {
    first: 'İlk',
    last: 'Son',
    next: 'Sonraki',
    previous: 'Önceki'
  },
  aria: {
    sortAscending: ': artan sıralama için tıklayın',
    sortDescending: ': azalan sıralama için tıklayın'
  }
};

$(document).ready(function() {
  $('#dataTable').DataTable({
    language: madematikTableLang
  });
  // Mesajlar listesi: kayıt sayısı menüsü ve arama kutusu yok
  $('#mesajTable').DataTable({
    language: madematikTableLang,
    lengthChange: false,
    searching: false
  });
});
