# KONTEKS LENGKAP PROYEK: SIGAP BRIDA KOTA MAKASSAR (`sigap-new`)
> **Single Source of Truth untuk Developer & AI Agent (Antigravity, Claude Code, Cursor, Copilot)**  
> Berisi seluruh pemahaman arsitektur sistem, basis data, backend, frontend (Blade Views, Tailwind, Alpine.js, Client Scripts), otorisasi RBAC, hingga konvensi pengembangan.

---

## 1. Quick Reference & Informasi Sistem

| Properti | Detail / Nilai |
|---|---|
| **Nama Aplikasi** | SIGAP BRIDA (Sistem Informasi Gerakan Administrasi & Pelayanan) |
| **Instansi** | Badan Riset dan Inovasi Daerah (BRIDA) Kota Makassar |
| **URL Produksi** | `https://sigap.brida.makassarkota.go.id` |
| **Framework Backend**| Laravel 11.x (PHP ^8.2) |
| **Database** | MySQL / MariaDB (Eloquent ORM & Doctrine DBAL v4) |
| **Frontend Stack** | Tailwind CSS v3, Alpine.js v3, Blade Templates, Vite v5 |
| **Otorisasi (RBAC)**| Spatie Laravel Permission (^6.21) — Multi-role granular |
| **Warna Dominan** | Maroon (`bg-maroon` / `#7a2222` / `#7A1C1D`) |
| **PWA & Mobile** | Tersedia Service Worker (`sw.js`) dan App Manifest (`manifest.json`) |

---

## 2. Tech Stack & Dependensi Kunci

### Backend (PHP / Laravel)
- **Framework Core**: `laravel/framework` (^11.0), `laravel/tinker` (^2.9), `laravel/breeze` (^2.3)
- **Role & Permission**: `spatie/laravel-permission` (^6.21)
- **PDF Generation & Merging**:
  - `barryvdh/laravel-dompdf` (^3.1)
  - `tecnickcom/tcpdf`
  - `iio/libmergepdf` (^4.0) (Menggabungkan halaman dokumen PDF)
  - `setasign/fpdi`
- **Spreadsheet / Excel**: `maatwebsite/excel` (^3.1), `phpoffice/phpspreadsheet`
- **Image Processing**: `intervention/image` (3.7) (Kompresi & resize gambar server-side)
- **QR Code Generator**: `simplesoftwareio/simple-qrcode` (^4.2) & `bacon/bacon-qr-code` (Untuk validasi sertifikat, buku tamu, presensi publik)
- **Alert / UI Feedback**: `php-flasher/flasher-sweetalert` (^2.1), `sweetalert2` (^11.22.4)
- **Audit & Log**: `rap2hpoutre/laravel-log-viewer` (^2.5), internal `ActivityLogger`

### Frontend & Client-Side Scripts
- **Styling**: Tailwind CSS v3 dengan plugin `@tailwindcss/forms`
- **Reaktivitas UI**: Alpine.js v3 (Modal, dropdown, accordion state, drawer mobile)
- **HTTP Client**: Axios & Fetch API
- **Client-Side Compression & Chunking**:
  - `resources/js/sigap-dokumen-uploader.js`: Canvas adaptive image compression & PDF compression client-side sebelum diunggah via XHR progress bar.
  - `resources/js/sigap-ima-uploader.js`: Canvas image compression & chunk upload 1MB via Fetch API (mengatasi limit upload PHP server).

---

## 3. Struktur Layout, Navigasi & Views (`resources/views/`)

### A. Layout Dashboard Internal ([`layouts/app.blade.php`](file:///Users/andigigaterahalil/Developer/sigap-new/resources/views/layouts/app.blade.php))
- **Sidebar Multi-Level & Accordion Persistence**: Status buka-tutup submenu disimpan ke `localStorage` browser agar tidak tertutup saat ganti halaman:
  - `sb_skp_open` (SIGAP SKP)
  - `sb_magang_open` (SIGAP Magang)
  - `sb_pjlp_open` (SIGAP PJLP)
  - `sb_kinerja_open` (SIGAP Kinerja)
  - `sb_siipid_open` (SIGAP SIIPID - Penghargaan & Insentif Inovasi)
  - `sb_dokumen_open`, `sb_surat_open`, `sb_inovasi_open`, `sb_kgb_open`, dll.
- **Hierarki & Penempatan Menu**:
  - Menu **SIGAP SIIPID** berdiri sebagai submenu independen dan **wajib ditempatkan langsung di bawah SIGAP SPJ** (tidak boleh digabung ke dalam SIGAP Inovasi atau SIGAP IMA).
- **Global Search Modal (Cmd+K / Ctrl+K)**:
  - Dibangun dengan Alpine.js (`globalSearch()`).
  - Navigasi keyboard penuh (`Arrow Down`, `Arrow Up`, `Enter`, `ESC`).
  - Mendukung penelusuran kategori berjenjang (breadcrumb menu) ke seluruh modul SIGAP termasuk SIIPID.
- **Header & Profil Dropdown**:
  - Menampilkan user aktif, avatar dengan fallback `images/avatar-placeholder.png`, tautan ke profil pegawai, dan form logout anti-CSRF issue.
- **Notifikasi Global**: Menyertakan `@include('partials.flash')` yang mengeksekusi SweetAlert2 otomatis saat ada session flash (`success`, `error`, `warning`, `info`, `$errors`).

### B. Layout Publik / Portal Web ([`layouts/page.blade.php`](file:///Users/andigigaterahalil/Developer/sigap-new/resources/views/layouts/page.blade.php))
- Header publik dengan sticky navigation & logo SIGAP BRIDA.
- **Mega Menu 2 Kolom ("Jenis Layanan")**: Akses publik langsung ke direktori Dokumen, Pegawai, Riset Daerah, Form Presensi, SIGAP SIIPID (di kategori *Riset & Inovasi*), dsb.
- **Mobile Responsive Drawer**: Drawer navigasi slide-over dengan Alpine.js (`mobileOpen`) yang memuat seluruh kategori layanan termasuk SIGAP SIIPID.

### C. Komponen Blade Reusable (`resources/views/components/`)
- Form components: `text-input`, `input-label`, `input-error`, `dropdown`, `dropdown-link`, `modal`.
- Tombol aksi: `primary-button`, `secondary-button`, `danger-button`.
- Evaluasi & ulasan: `review-badge`, `review-comment`.

---

## 4. Peta Lengkap Modul: Controller, Model & Direktori View

| Modul | Controller Utama | Model Terkait | Direktori View | Deskripsi & Alur Kerja |
|---|---|---|---|---|
| **Dokumen & Folder** | [SigapDokumenController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapDokumenController.php)<br>[FolderController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/FolderController.php) | `Document`, `Folder` | `resources/views/dashboard/dokumen/` | Hierarki folder dokumen umum/saya, upload adaptif kompresi & tautan berbagi (`shared-links`). |
| **Kepegawaian** | [SigapPegawaiController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapPegawaiController.php)<br>[PegawaiProfilController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/PegawaiProfilController.php) | `Employee`, `PegawaiProfile`, `PegawaiKompetensi`, `PersonalDocument` | `resources/views/dashboard/pegawai/`<br>`resources/views/SigapPegawai/` | Biodata ASN, kompetensi pegawai, arsip berkas pegawai, direktori publik (`/pegawai`). |
| **KGB (Gaji Berkala)** | [KgbController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/KgbController.php)<br>[KgbMasterGajiController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/KgbMasterGajiController.php) | `KgbRiwayat`, `KgbGajiPokok` | `resources/views/dashboard/kgb/` | Monitoring jatuh tempo kenaikan gaji berkala ASN & master tabel acuan gaji pokok nasional. |
| **SKP & Kinerja** | [SkpController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SkpController.php)<br>[SigapKinerjaController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapKinerjaController.php)<br>[SigapStoryController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapStoryController.php)<br>[SigapFeedController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapFeedController.php) | `Skp`, `SkpFoto`, `SkpKumpulan`, `Kinerja`, `KinerjaMedia`, `SigapStoryLog` | `resources/views/dashboard/skp/`<br>`resources/views/kinerja/` | Capaian target SKP tahunan/triwulan, galeri foto lightbox kegiatan, pelaporan bukti kinerja harian, SIGAP Story & Feed. |
| **Inovasi & IGA** | [SigapInovasiController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapInovasiController.php)<br>[SigapIgaController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapIgaController.php)<br>[InovasiReviewController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/InovasiReviewController.php) | `Inovasi`, `Evidence`, `EvidenceFile`, `EvidenceGuide`, `IgaAccount` | `resources/views/dashboard/inovasi/`<br>`resources/views/dashboard/iga/` | Database inovasi daerah, indikator eviden, asistensi IGA Kemendagri, review penilai inovasi. |
| **Inovasi Masyarakat (IMA)** | [SigapImaController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapImaController.php)<br>[ImaChunkUploadController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/ImaChunkUploadController.php)<br>[SigapImaSettingController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapImaSettingController.php) | `ImaInovasi`, `ImaIndicator`, `ImaSchedule`, `ImaDropdown` | `resources/views/dashboard/ima/` | Lomba & penjaringan inovasi masyarakat, chunked 1MB upload, skor indikator kustom, SDGs mapping, jadwal timeline. |
| **Inkubatorma** | [SigapInkubatormaController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapInkubatormaController.php) | `Inkubatorma`, `InkubatormaRecord`, `InkubatormaLog` | `resources/views/dashboard/inkubatorma/` | Manajemen inkubasi tenant inovasi, kurasi ide, validasi, hingga komersialisasi riset. |
| **Surat Menyurat** | [SuratMasukController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/Surat/SuratMasukController.php)<br>[SuratKeluarController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/Surat/SuratKeluarController.php) | `SuratMasuk`, `SuratKeluar` | `resources/views/dashboard/surat/masuk/`<br>`resources/views/dashboard/surat/keluar/` | Penomoran otomatis dinas kearsipan via `SuratKeluarService`, tracking disposisi, dan scan berkas PDF. |
| **Absensi & Kehadiran** | [SigapAbsensiController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapAbsensiController.php)<br>[SigapDaftarHadirController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapDaftarHadirController.php)<br>[SigapNarasumberController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapNarasumberController.php) | `SigapAbsensi`, `SigapDaftarHadirKegiatan`, `SigapDaftarHadirPeserta`, `SigapNarasumberKesediaan` | `resources/views/dashboard/absensi/`<br>`resources/views/dashboard/daftar_hadir/`<br>`resources/views/dashboard/narasumber/` | Rekap kehadiran internal ASN (harian & bulanan), buku tamu digital kegiatan publik (HTML5 touch signature canvas, souvenir checklist, print QR meja). |
| **PIC Kredensial** | [SigapPicController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapPicController.php) | `SigapPicSystem`, `SigapPicAssignment`, `SigapPicCredential`, `SigapPicLog` | `resources/views/dashboard/pic/` | Inventarisasi sistem informasi Pemkot Makassar, penugasan PIC pegawai, dan penyimpanan akun kredensial aman. |
| **Magang** | [MagangController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/MagangController.php) | `MagangBatch`, `MagangLogbook`, `MagangPeserta` | `resources/views/dashboard/magang/` | Batch peserta mahasiswa/siswa, pengisian logbook harian, izin susulan, upload laporan akhir, sertifikat kelulusan. |
| **PJLP** | [PjlpLogbookController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/PjlpLogbookController.php)<br>[PjlpVerificationController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/PjlpVerificationController.php) | `PjlpPeriode`, `PjlpLogbook` | `resources/views/dashboard/pjlp/` | Logbook tugas harian tenaga kontrak PJLP berbukti foto sebelum/sesudah verifikasi PPTK bulanan. |
| **Keuangan SPJ** | [SpjController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SpjController.php)<br>[SpjBidangController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SpjBidangController.php) | `SpjBidang`, `SpjSubKegiatan`, `SpjKegiatan`, `SpjGelombang` | `resources/views/dashboard/spj/` | Pengarsipan pertanggungjawaban anggaran berjenjang per gelombang & sub-kegiatan (unggah KAK & SK Panitia). |
| **Sertifikat** | [SertifikatController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SertifikatController.php)<br>[SigapSertifikatController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapSertifikatController.php) | `SertifikatKegiatan`, `SertifikatPeserta` | `resources/views/dashboard/sertifikat/` | Penomoran seri sertifikat otomatis, validasi QR code token publik, impor data Excel, unduh batch PDF. |
| **Notulensi** | [NotulensiController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/NotulensiController.php) | `Notulensi`, `NotulensiPeserta` | `resources/views/dashboard/notulensi/` | Notulensi rapat resmi dinas, agenda rapat, kesimpulan, foto dokumentasi, dan tanda tangan digital peserta rapat. |
| **Riset & PPD** | [RisetController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/RisetController.php)<br>[SigapRisetController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapRisetController.php)<br>[SigapPpdController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapPpdController.php) | `Riset`, `PpdKegiatan`, `PpdLembarLaporan`, `PpdLembarFoto` | `resources/views/dashboard/riset/`<br>`resources/views/dashboard/ppd/` | Publikasi kajian ilmiah/kelayakan riset dan lembar laporan monitoring evaluasi PPD. |
| **Format Dokumen** | [SigapFormatController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SigapFormatController.php)<br>[FormatController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/FormatController.php) | `FormatTemplate`, `FormatAccessLog` | `resources/views/dashboard/format/` | Katalog repositori template resmi dinas (SPT, SPPD, Nota Dinas, Laporan). |
| **SIIPID (Penghargaan & Insentif Inovasi)** | [SiipidController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SiipidController.php)<br>[SiipidReviewController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/SiipidReviewController.php)<br>[SiipidPublicController.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Http/Controllers/page/SiipidPublicController.php) | `SiipidPrestasi`, `SiipidReviewLog` | `resources/views/dashboard/siipid/`<br>`resources/views/SigapSiipid/` | Sistem Informasi Penghargaan & Insentif Inovasi Daerah (Draft Perwali 2026). Pendaftaran capaian berjenjang (Kota/Prov/Nasional/Internasional), validasi Eligibility Gate dari SIGAP Inovasi & IMA, combobox pencarian inovasi, meja review verifikator (`verif_siipid`), revisi berkala, penetapan SK Walikota, serta portal publik prestator (`/siipid`). |

---

## 5. Struktur Role & Akses (Spatie Laravel Permission)

Role sistem bersifat granular untuk menjaga pemisahan tugas verifikasi antar-bidang:
- `admin` / `superadmin`: Memiliki hak penuh atas konfigurasi, master data, audit logs (`admin.logs`), dan manajemen role (`roles.*`).
- `employee`: Akun ASN BRIDA (Akses: SKP Pribadi, KGB Saya, Absensi Saya, Logbook Kinerja, Dokumen Saya, Profil Pegawai).
- `inovator`: Akun OPD pengusul inovasi daerah.
- `verificator` / `verif_inovasi`: Penilai dokumen dan bukti eviden inovasi daerah.
- `verif_pegawai`: Verifikator kelengkapan biodata dan berkas pegawai.
- `verif_skp`: Pejabat penilai target dan bukti capaian SKP tahunan/triwulan.
- `verif_surat`: Pengelola administrasi tata usaha surat masuk & surat keluar.
- `verificator_absensi`: Petugas rekapitulasi kehadiran dan monitoring absen harian/bulanan.
- `verif_magang`: Mentor pembimbing mahasiswa magang.
- `magang`: Peserta magang (hanya modul Magang & Logbook Saya).
- `verif_pjlp`: PPTK / pejabat verifikator laporan kinerja harian PJLP.
- `pjlp`: Tenaga kontrak perorangan (hanya modul Logbook PJLP & History).
- `verif_daftarhadir`: Operator pembuat acara kegiatan, cetak QR presensi, dan ceklis souvenir.
- `operator_spj`: Operator pengelola berkas pertanggungjawaban keuangan sub-kegiatan.
- `verif_notulensi`: Notulis rapat dinas.
- `inovator_siipid`: Inovator peserta pengusul penghargaan dan insentif inovasi daerah.
- `verif_siipid`: Tim reviewer/verifikator usulan prestasi inovasi daerah (meja review, perbaikan berkas, rekomendasi, dan penetapan SK Walikota).
- `researcher`: Peneliti naskah riset.
- `user`: Pengguna umum portal.

---

## 6. Shared Services & Utilitas Inti

1. **[ActivityLogger.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Services/ActivityLogger.php)**:
   - Mencatat audit trail CRUD otomatis (`user_id`, `action`, `module`, `description`, `ip_address`, `user_agent`) ke tabel `activity_logs`.
2. **[ImageCompressor.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Services/ImageCompressor.php)**:
   - Kompresi gambar server-side untuk memastikan foto kegiatan/eviden efisien sebelum disimpan di storage.
3. **[SuratKeluarService.php](file:///Users/andigigaterahalil/Developer/sigap-new/app/Services/SuratKeluarService.php)**:
   - Mengatur format penomoran otomatis surat keluar sesuai kode klasifikasi kearsipan daerah Kota Makassar.
4. **[flash.blade.php](file:///Users/andigigaterahalil/Developer/sigap-new/resources/views/partials/flash.blade.php)**:
   - Handler popup SweetAlert2 otomatis saat session flash terdeteksi.

---

## 7. Aturan & Konvensi Penting untuk AI Agent

1. **Role Checking & Security**:
   - Jangan berasumsi pengguna selalu berstatus `admin`. Selalu gunakan `@hasrole`, `@hasanyrole`, atau middleware `role:` / `permission:` sesuai hierarki RBAC.
2. **Konsistensi UI & State**:
   - Pertahankan skema warna brand **Maroon** (`bg-maroon` / `#7a2222`), styling Tailwind CSS, dan modal Alpine.js.
   - Pertahankan sinkronisasi menu sidebar dengan `localStorage` (`sb_*_open`) agar status accordion tidak reset saat halaman dimuat ulang.
3. **Penanganan Unggah Berkas**:
   - Validasi MIME type (`pdf`, `jpg`, `png`) dan ukuran file di Controller.
   - Untuk berkas besar (seperti bukti eviden video/dokumen lomba), rujuk endpoint chunked upload (`ImaChunkUploadController.php` & `sigap-ima-uploader.js`).
4. **Integritas Tanda Tangan & QR Code**:
   - Fitur sertifikat, presensi, dan notulensi menggunakan token hash/UUID publik. Jangan merusak alur enkripsi, generasi QR Code, atau format signature canvas Base64.
5. **Database Migration Integrity**:
   - Selalu cek daftar migrasi di `database/migrations/` sebelum menambah kolom baru guna menghindari konflik skema atau duplikasi migrasi.
6. **Aturan Khusus Modul SIGAP SIIPID**:
   - **Eligibility Gate**: Inovator hanya berhak mengusulkan prestasi di `/sigap-siipid/usulkan` apabila sudah memiliki setidaknya 1 inovasi terdaftar di **SIGAP Inovasi** (OPD) atau **SIGAP IMA** (Masyarakat). Jika belum ada, sistem menampilkan *Gate Notice* yang mengarahkan mereka untuk mendaftarkan inovasi terlebih dahulu.
   - **Menu Placement**: Menu navigasi sidebar **SIGAP SIIPID** (`layouts/app.blade.php`) wajib berdiri sendiri langsung **di bawah SIGAP SPJ** (tidak boleh digabung atau disubordinasikan ke modul inovasi lain).
   - **Alur Reviewer**: Status usulan dikelola oleh role `verif_siipid` melalui rute `/sigap-siipid/verifikator/*` dengan alur: `diajukan` ➔ `sedang_dinilai` ➔ `dikembalikan_perbaikan` / `ditolak` / `direkomendasikan` ➔ `ditetapkan_sk`. Usulan yang dikembalikan dapat diperbaiki dan diajukan ulang oleh inovator.
