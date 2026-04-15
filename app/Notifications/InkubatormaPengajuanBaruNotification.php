<?php

namespace App\Notifications;

use App\Models\Inkubatorma;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InkubatormaPengajuanBaruNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Inkubatorma $inkubatorma,
        public bool $isVerifikator = false,
    ) {}
    

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $detailUrl = 'https://sigap.brida.makassarkota.go.id/sigap-inkubatorma/' . $this->inkubatorma->id . '/detail';

        $judul        = $this->inkubatorma->judul_konsultasi ?? '-';
        $pengaju      = $this->inkubatorma->nama_pengaju ?? '-';
        $opd          = $this->inkubatorma->opd_unit ?? '-';
        $layanan      = $this->inkubatorma->layanan_nama ?? '-';
        $status       = $this->inkubatorma->status ?? 'Menunggu';

        $jadwalUsulan = '—';
        if (!empty($this->inkubatorma->tanggal_usulan)) {
            $jadwalUsulan = \Carbon\Carbon::parse($this->inkubatorma->tanggal_usulan)
                            ->timezone('Asia/Makassar')
                            ->translatedFormat('d F Y');
            if (!empty($this->inkubatorma->jam_usulan)) {
                $jadwalUsulan .= ' • ' . \Carbon\Carbon::parse($this->inkubatorma->jam_usulan)->format('H:i') . ' WITA';
            }
        }

        $metodeLabel = match($this->inkubatorma->metode_usulan ?? '') {
            'online'  => 'Online (Zoom/Meet)',
            'offline' => 'Tatap Muka (Offline)',
            default   => '—',
        };

        if ($this->isVerifikator) {
            // Email ke verifikator: kasih tau ada pengajuan baru yang perlu ditinjau
            return (new MailMessage)
                ->subject("[SIGAP Inkubatorma] Pengajuan Baru: {$judul}")
                ->greeting('Halo, ' . ($notifiable->name ?? 'Verifikator') . '!')
                ->line('Ada pengajuan konsultasi baru yang masuk dan perlu ditinjau:')
                ->line('---')
                ->line('**Judul Pengajuan:** ' . $judul)
                ->line('**Pengaju:** ' . $pengaju)
                ->line('**OPD / Unit:** ' . $opd)
                ->line('**Layanan:** ' . $layanan)
                ->line('**Usulan Jadwal:** ' . $jadwalUsulan)
                ->line('**Metode Usulan:** ' . $metodeLabel)
                ->line('**Status:** ' . $status)
                ->action('Lihat Detail Pengajuan', $detailUrl)
                ->line('Silakan login dan tinjau pengajuan tersebut secepatnya.')
                ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar');
        }

        // Email ke pengaju: konfirmasi pengajuan berhasil diterima
        return (new MailMessage)
            ->subject("[SIGAP Inkubatorma] Pengajuan Berhasil Dikirim: {$judul}")
            ->greeting('Halo, ' . ($notifiable->name ?? 'Pengaju') . '!')
            ->line('Pengajuan konsultasi Anda telah berhasil diterima oleh sistem SIGAP Inkubatorma.')
            ->line('---')
            ->line('**Kode Pengajuan:** ' . ($this->inkubatorma->kode ?? '-'))
            ->line('**Judul Pengajuan:** ' . $judul)
            ->line('**Layanan:** ' . $layanan)
            ->line('**Usulan Jadwal:** ' . $jadwalUsulan)
            ->line('**Metode Usulan:** ' . $metodeLabel)
            ->line('**Status saat ini:** ' . $status)
            ->line('Tim verifikator BRIDA akan segera meninjau pengajuan Anda. Update selanjutnya akan dikirim melalui email ini.')
            ->action('Lihat Detail Pengajuan', $detailUrl)
            ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}