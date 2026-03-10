<?php

namespace App\Notifications;

use App\Models\Inkubatorma;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InkubatormaStatusUpdateNotification extends Notification
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

        $mail = (new MailMessage)
            ->subject('Update Status Pengajuan SIGAP Inkubatorma')
            ->greeting('Halo ' . ($notifiable->name ?? 'Pengguna') . ',')
            ->line('Status pengajuan Anda telah diperbarui.')
            ->line('Judul: ' . ($this->inkubatorma->judul_konsultasi ?? '-'))
            ->line('Status Baru: ' . ($this->inkubatorma->status ?? '-'));

        if (!empty($this->inkubatorma->catatan_verifikator)) {
            $mail->line('Catatan Verifikator: ' . $this->inkubatorma->catatan_verifikator);
        }

        return $mail
            ->action('Lihat Detail Pengajuan', $detailUrl)
            ->line('Terima kasih telah menggunakan SIGAP Inkubatorma.');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}