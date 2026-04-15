<?php

namespace App\Notifications;

use App\Models\Inkubatorma;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InkubatormaStatusUpdateNotification extends Notification
{
    use Queueable;

    public function __construct(public Inkubatorma $inkubatorma) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $detailUrl = 'https://sigap.brida.makassarkota.go.id/sigap-inkubatorma/' . $this->inkubatorma->id . '/detail';

        $judul   = $this->inkubatorma->judul_konsultasi ?? '-';
        $status  = $this->inkubatorma->status ?? '-';
        $layanan = $this->inkubatorma->layanan_nama ?? '-';

        // Jadwal final kalau sudah ada
        $jadwalFinal = '—';
        if (!empty($this->inkubatorma->tanggal_final)) {
            $jadwalFinal = \Carbon\Carbon::parse($this->inkubatorma->tanggal_final)
                ->timezone('Asia/Makassar')
                ->translatedFormat('d F Y');
            if (!empty($this->inkubatorma->jam_final)) {
                $jadwalFinal .= ' • ' . \Carbon\Carbon::parse($this->inkubatorma->jam_final)->format('H:i') . ' WITA';
            }
        }

        $metodeFinal = match($this->inkubatorma->metode_final ?? '') {
            'online'  => 'Online (Zoom/Meet)',
            'offline' => 'Tatap Muka (Offline)',
            default   => '—',
        };

        $mail = (new MailMessage)
            ->subject("[SIGAP Inkubatorma] Status Diperbarui: {$status} — {$judul}")
            ->greeting('Halo, ' . ($notifiable->name ?? 'Pengaju') . '!')
            ->line('Ada pembaruan status pada pengajuan konsultasi Anda:')
            ->line('---')
            ->line('**Kode Pengajuan:** ' . ($this->inkubatorma->kode ?? '-'))
            ->line('**Judul Pengajuan:** ' . $judul)
            ->line('**Layanan:** ' . $layanan)
            ->line('**Status Terbaru:** ' . $status)
            ->line('**Jadwal Konsultasi:** ' . $jadwalFinal)
            ->line('**Metode:** ' . $metodeFinal);

        if (!empty($this->inkubatorma->catatan_verifikator)) {
            $mail->line('**Catatan Verifikator:** ' . $this->inkubatorma->catatan_verifikator);
        }

        return $mail
            ->action('Lihat Detail Pengajuan', $detailUrl)
            ->line('Silakan login untuk melihat detail pengajuan Anda.')
            ->salutation('Salam, Tim SIGAP BRIDA Kota Makassar');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}