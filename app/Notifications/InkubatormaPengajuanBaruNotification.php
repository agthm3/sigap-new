<?php

namespace App\Notifications;

use App\Models\Inkubatorma;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InkubatormaPengajuanBaruNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Inkubatorma $inkubatorma)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Pengajuan Baru SIGAP Inkubatorma')
                    ->greeting('Halo, '. ($notifiable->name ?? 'Verifikator') . ',')
                    ->line('Ada pengajuan konsultasi baru si SIGAP Inkubatorma.')
                    ->line('Judul: ' . ($this->inkubatorma->judul_konsultasi ?? '-'))
                    ->line('Pengaju: ' . ($this->inkubatorma->nama_pengaju ?? '-'))
                    ->line('OPD/Unit: ' . ($this->inkubatorma->opd_unit ?? '-'))
                    ->line('Status: ' . ($this->inkubatorma->status ?? 'Menunggu'))
                    ->action('Lihat Detail Pengajuan', url('detailUrl'))
                    ->line('Silakan login untuk meninjau pengajuan tersebut.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
