<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChargingFinishedNotification extends Notification
{
    use Queueable;

    protected $type;
    protected $session;

    /**
     * Jenis:
     * - charging_finished
     * - payment_success
     */
    public function __construct($type, $session)
    {
        $this->type = $type;
        $this->session = $session;
    }

    /**
     * Kirim ke email dan database.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Email notification.
     */
    public function toMail($notifiable)
    {
        /*
         * ============================================
         * CHARGING SELESAI
         * ============================================
         */
        if ($this->type === 'charging_finished') {

            return (new MailMessage)
                ->subject('Pengisian Daya Selesai - EV Charge')
                ->greeting(
                    'Halo, ' .
                    ($notifiable->name ?? 'Pengguna') .
                    '!'
                )
                ->line(
                    'Sesi pengisian daya kendaraan listrik Anda telah selesai.'
                )
                ->line('Rincian Sesi:')
                ->line(
                    'ID Sesi: #' .
                    $this->session->id
                )
                ->line(
                    'Total Energi: ' .
                    number_format(
                        $this->session->energy_consumed_kwh ?? 0,
                        3,
                        ',',
                        '.'
                    ) .
                    ' kWh'
                )
                ->line(
                    'Biaya Sesi: Rp ' .
                    number_format(
                        $this->session->total_cost ?? 0,
                        0,
                        ',',
                        '.'
                    )
                )
                ->action(
                    'Lakukan Pembayaran',
                    route(
                        'charging.payment.view',
                        $this->session->id
                    )
                )
                ->line(
                    'Silakan lakukan pembayaran untuk menyelesaikan transaksi.'
                );
        }


        /*
         * ============================================
         * PEMBAYARAN BERHASIL
         * ============================================
         */

        $transaction = Transaction::where(
            'session_id',
            $this->session->id
        )
            ->where(
                'status',
                'success'
            )
            ->latest()
            ->first();


        $invoiceNumber =
            $transaction->invoice_number
            ?? 'INV-' . $this->session->id;


        $amount =
            $transaction->amount
            ?? $this->session->total_cost
            ?? 0;


        return (new MailMessage)
            ->subject(
                'Pembayaran Berhasil - EV Charge'
            )
            ->greeting(
                'Halo, ' .
                ($notifiable->name ?? 'Pengguna') .
                '!'
            )
            ->line(
                'Pembayaran tagihan pengisian daya Anda telah berhasil diproses.'
            )
            ->line('Rincian Pembayaran:')
            ->line(
                'No. Invoice: ' .
                $invoiceNumber
            )
            ->line(
                'ID Sesi: #' .
                $this->session->id
            )
            ->line(
                'Total Pembayaran: Rp ' .
                number_format(
                    $amount,
                    0,
                    ',',
                    '.'
                )
            )
            ->line(
                'Status: LUNAS'
            )
            ->action(
                'Lihat Invoice',
                route(
                    'charging.invoice',
                    $this->session->id
                )
            )
            ->line(
                'Invoice PDF juga telah dikirim melalui email.'
            )
            ->line(
                'Terima kasih telah menggunakan layanan EV Charge!'
            );
    }


    /**
     * Data yang disimpan ke tabel notifications.
     */
    public function toArray($notifiable)
    {
        /*
         * ============================================
         * CHARGING SELESAI
         * ============================================
         */
        if ($this->type === 'charging_finished') {

            return [
                'title' =>
                    'Pengisian Daya Selesai',

                'message' =>
                    'Sesi #' .
                    $this->session->id .
                    ' selesai. Silakan lakukan pembayaran.',

                'session_id' =>
                    $this->session->id,

                'notification_type' =>
                    'charging_finished',
            ];
        }


        /*
         * ============================================
         * PEMBAYARAN BERHASIL
         * ============================================
         */

        $transaction = Transaction::where(
            'session_id',
            $this->session->id
        )
            ->where(
                'status',
                'success'
            )
            ->latest()
            ->first();


        $invoiceNumber =
            $transaction->invoice_number
            ?? 'INV-' . $this->session->id;


        return [
            'title' =>
                'Pembayaran Berhasil',

            'message' =>
                'Pembayaran Invoice ' .
                $invoiceNumber .
                ' berhasil. Status pembayaran LUNAS.',

            /*
             * Tetap simpan session_id.
             * Ini penting agar halaman notifications
             * tahu invoice/session mana yang dibuka.
             */
            'session_id' =>
                $this->session->id,

            'invoice_number' =>
                $invoiceNumber,

            'notification_type' =>
                'payment_success',
        ];
    }
}