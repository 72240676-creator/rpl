<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ChargingSession;

class ChargingFinishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $session;

    /**
     * Create a new notification instance.
     */
    public function __construct(ChargingSession $session)
    {
        $this->session = $session;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // Mengirimkan melalui channel email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $chargerName = $this->session->charger->device_number ?? 'Charger EV';
        $sessionId = $this->session->getKey();

        return (new MailMessage)
            ->subject('Struk & Invoice Sesi Pengisian Daya EV - Sumber Jaya Rejeki')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Sesi pengisian daya kendaraan listrik Anda pada perangkat ' . $chargerName . ' telah selesai dan transaksi pembayaran berhasil.')
            ->line('Anda dapat mengunduh salinan struk digital atau invoice lengkap melalui tombol di bawah ini.')
            ->action('Lihat Invoice', url('/invoice/' . $sessionId))
            ->line('Terima kasih telah menggunakan layanan Sumber Jaya Rejeki!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'session_id' => $this->session->getKey(),
            'message' => 'Sesi pengisian daya telah selesai.',
        ];
    }
}