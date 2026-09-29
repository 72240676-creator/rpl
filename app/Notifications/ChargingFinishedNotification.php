<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChargingFinishedNotification extends Notification
{
    use Queueable;

    protected $type;
    protected $data;

    /**
     * @param string $type Jenis notifikasi ('charging_finished' atau 'payment_success')
     * @param mixed $data Data sesi pengisian daya atau invoice
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        // 1. Notifikasi Pengisian Daya Selesai
        if ($this->type === 'charging_finished') {
            return (new MailMessage)
                ->subject('Pengisian Daya Selesai - EV Charge')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Sesi pengisian daya kendaraan listrik Anda telah selesai.')
                ->line('Rincian Sesi:')
                ->line('- ID Sesi: #' . $this->data->id)
                ->line('- Total Energi: ' . ($this->data->energy_kwh ?? '0') . ' kWh')
                ->line('- Biaya Sesi: Rp ' . number_format($this->data->total_cost ?? 0, 0, ',', '.'))
                ->action('Lihat Detail Invoice', url('/notifications'))
                ->line('Silakan lakukan pembayaran untuk menyelesaikan transaksi.');
        }

        // 2. Notifikasi Pembayaran Invoice Berhasil
        return (new MailMessage)
            ->subject('Pembayaran Berhasil - Bukti Transaksi EV Charge')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Pembayaran tagihan pengisian daya Anda telah berhasil diproses.')
            ->line('Rincian Pembayaran:')
            ->line('- No. Invoice: #' . ($this->data->invoice_number ?? $this->data->id))
            ->line('- Total Pembayaran: Rp ' . number_format($this->data->amount ?? $this->data->total_cost ?? 0, 0, ',', '.'))
            ->line('- Status: LUNAS')
            ->action('Lihat Riwayat Transaksi', url('/notifications'))
            ->line('Terima kasih telah menggunakan layanan EV Charge!');
    }

    public function toArray($notifiable)
    {
        if ($this->type === 'charging_finished') {
            return [
                'title' => 'Pengisian Daya Selesai',
                'message' => 'Sesi #' . $this->data->id . ' selesai. Silakan lakukan pembayaran.',
                'session_id' => $this->data->id,
            ];
        }

        return [
            'title' => 'Pembayaran Berhasil',
            'message' => 'Pembayaran Invoice #' . ($this->data->invoice_number ?? $this->data->id) . ' berhasil.',
            'invoice_id' => $this->data->id,
        ];
    }
}