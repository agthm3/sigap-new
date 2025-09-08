@if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
<script>
(function(){
  function fireAlerts(){
    @if(session('success'))
      Swal.fire({icon:'success', title:'Berhasil', text: @json(session('success')), timer:2000, showConfirmButton:false});
    @endif

    @if(session('error'))
      Swal.fire({icon:'error', title:'Gagal', text: @json(session('error'))});
    @endif

    @if(session('warning'))
      Swal.fire({icon:'warning', title:'Perhatian', text: @json(session('warning'))});
    @endif

    @if(session('info'))
      Swal.fire({icon:'info', title:'Info', text: @json(session('info'))});
    @endif

    @if($errors->any())
      Swal.fire({
        icon:'error',
        title:'Validasi gagal',
        html: `{!! implode('<br>', $errors->all()) !!}`
      });
    @endif
  }

  // Kalau Swal belum ada (misal CDN lambat), tunggu sebentar
  function waitForSwalAndFire(retry=0){
    if (window.Swal && typeof Swal.fire === 'function') {
      fireAlerts();
    } else if (retry < 20) {
      setTimeout(()=>waitForSwalAndFire(retry+1), 100); // cek tiap 100ms, max ~2 detik
    }
  }

  // Jalankan segera jika dokumen sudah siap, atau tunggu DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function(){ waitForSwalAndFire(); });
  } else {
    waitForSwalAndFire();
  }
})();
</script>
@endif
