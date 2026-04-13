<?php

namespace App\Notifications;

use App\Models\Inkubatorma;
use App\Models\InkubatormaRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InkubatormaRecordNotification extends Notification
{
    use Queueable;

    // Tipe kejadian
    const TIPE_SESI_ADA_REVISI      = 'sesi_ada_revisi';
    const TIPE_SESI_TANPA_REVISI    = 'sesi_tanpa_revisi';
    const TIPE_UPLOAD_REVISI        = 'upload_revisi';
    const TIPE_REVIEW_ADA_REVISI    = 'review_ada_revisi';
    const TIPE_REVIEW_AMAN          = 'review_aman';
    const TIPE_KONFIRMASI_SELESAI   = 'konfirmasi_selesai';

    public function __construct(
        public Inkubatorma $inkubatorma,
        public InkubatormaRecord $record,
        public string $tipe,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $detailUrl = 'https://sigap.brida.makassarkota.go.id/sigap-inkubatorma/'
            . $this->inkubatorma->id . '/detail';
        $recordUrl = 'https://sigap.brida.makassarkota.go.id/sigap-inkubatorma/'
            . $this->inkubatorma->id . '/records';

        $judul = $this->inkubatorma->judul_konsultasi ?? '-';
        $kode  = $this->inkubatorma->kode ?? '-';
        $nama  = $notifiable->name ?? 'Pengguna';

        return match ($this->tipe) {

            self::TIPE_SESI_ADA_REVISI => (new MailMessage)
                ->subject("[SIGAP Inkubatorma] Ada Catatan Sesi + Revisi: {$judul}")
                ->greeting("Halo, {$nama}!")
                ->line('Verifikator telah menambahkan catatan sesi konsultasi dan terdapat **revisi yang perlu dikerjakan**.')
                ->line('---')
                ->line('**Kode Pengajuan:** ' . $kode)
                ->line('**Judul:** ' . $judul)
                ->line('**Judul Record:** ' . ($this->record->title ?? '-'))
                ->line('**Catatan Revisi:** ' . ($this->record->revision_note ?? '-'))
                ->action('Lihat Record Konsultasi', $recordUrl)
                ->line('Mohon segera upload hasil revisi sesuai catatan di atas.')
                ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar'),

            self::TIPE_SESI_TANPA_REVISI => (new MailMessage)
                ->subject("[SIGAP Inkubatorma] Catatan Sesi Konsultasi Baru: {$judul}")
                ->greeting("Halo, {$nama}!")
                ->line('Verifikator telah menambahkan catatan hasil sesi konsultasi.')
                ->line('---')
                ->line('**Kode Pengajuan:** ' . $kode)
                ->line('**Judul:** ' . $judul)
                ->line('**Judul Record:** ' . ($this->record->title ?? '-'))
                ->action('Lihat Record Konsultasi', $recordUrl)
                ->line('Silakan buka halaman record untuk membaca catatan lengkapnya.')
                ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar'),

            self::TIPE_UPLOAD_REVISI => (new MailMessage)
                ->subject("[SIGAP Inkubatorma] Pengaju Upload Hasil Revisi: {$judul}")
                ->greeting("Halo, {$nama}!")
                ->line('Pengaju telah mengupload hasil revisi dan menunggu tinjauan Anda.')
                ->line('---')
                ->line('**Kode Pengajuan:** ' . $kode)
                ->line('**Judul:** ' . $judul)
                ->line('**Catatan dari Pengaju:** ' . ($this->record->content ?? '-'))
                ->action('Tinjau Hasil Revisi', $recordUrl)
                ->line('Mohon segera tinjau dan berikan feedback.')
                ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar'),

            self::TIPE_REVIEW_ADA_REVISI => (new MailMessage)
                ->subject("[SIGAP Inkubatorma] Revisi Masih Diperlukan: {$judul}")
                ->greeting("Halo, {$nama}!")
                ->line('Verifikator telah mereview hasil revisi Anda dan masih terdapat **perbaikan yang perlu dilakukan**.')
                ->line('---')
                ->line('**Kode Pengajuan:** ' . $kode)
                ->line('**Judul:** ' . $judul)
                ->line('**Catatan Revisi:** ' . ($this->record->revision_note ?? '-'))
                ->action('Lihat Record Konsultasi', $recordUrl)
                ->line('Mohon upload ulang hasil revisi sesuai catatan.')
                ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar'),

            self::TIPE_REVIEW_AMAN => (new MailMessage)
                ->subject("[SIGAP Inkubatorma] Revisi Anda Sudah Aman: {$judul}")
                ->greeting("Halo, {$nama}!")
                ->line('Verifikator telah mereview hasil revisi Anda dan **dinyatakan sudah aman**.')
                ->line('---')
                ->line('**Kode Pengajuan:** ' . $kode)
                ->line('**Judul:** ' . $judul)
                ->line('Jika tidak ada pertanyaan lagi, silakan kirim konfirmasi selesai untuk menutup sesi konsultasi ini.')
                ->action('Kirim Konfirmasi Selesai', $recordUrl)
                ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar'),

            self::TIPE_KONFIRMASI_SELESAI => (new MailMessage)
                ->subject("[SIGAP Inkubatorma] Pengaju Konfirmasi Selesai: {$judul}")
                ->greeting("Halo, {$nama}!")
                ->line('Pengaju telah mengirimkan **konfirmasi selesai** dan konsultasi siap untuk ditutup.')
                ->line('---')
                ->line('**Kode Pengajuan:** ' . $kode)
                ->line('**Judul:** ' . $judul)
                ->line('Silakan buka panel verifikasi dan ubah status menjadi Selesai untuk menutup konsultasi ini.')
                ->action('Buka Panel Verifikasi', $detailUrl)
                ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar'),

            default => (new MailMessage)
                ->subject("[SIGAP Inkubatorma] Update Record: {$judul}")
                ->greeting("Halo, {$nama}!")
                ->line('Ada update baru pada record konsultasi.')
                ->action('Lihat Record', $recordUrl)
                ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar'),
        };
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}