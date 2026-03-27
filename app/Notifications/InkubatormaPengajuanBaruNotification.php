<?php

namespace App\Notifications;

use App\Models\Inkubatorma;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InkubatormaPengajuanBaruNotification extends Notification
{
    use Queueable;

    public function __construct(public Inkubatorma $inkubatorma)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $detailUrl = route('sigap-inkubatorma.detail', ['id' => $this->inkubatorma->id]);

        return (new MailMessage)
            ->subject('Pengajuan Baru SIGAP Inkubatorma')
            ->greeting('Halo, ' . ($notifiable->name ?? 'Verifikator') . ',')
            ->line('Ada pengajuan konsultasi baru di SIGAP Inkubatorma.')
            ->line('Judul: ' . ($this->inkubatorma->judul_konsultasi ?? '-'))
            ->line('Pengaju: ' . ($this->inkubatorma->nama_pengaju ?? '-'))
            ->line('OPD/Unit: ' . ($this->inkubatorma->opd_unit ?? '-'))
            ->line('Status: ' . ($this->inkubatorma->status ?? 'Menunggu'))
            ->action('Lihat Detail Pengajuan', $detailUrl)
            ->line('Silakan login untuk meninjau pengajuan tersebut.');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}