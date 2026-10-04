<?php

namespace App\Mail;

use App\Models\ChargingSession;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendChargingInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public ChargingSession $session;

    public function __construct(ChargingSession $session)
    {
        $this->session = $session;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Charging Invoice Mail'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.charging_invoice'
        );
    }

    public function attachments(): array
    {
        $transaction = Transaction::where(
            'session_id',
            $this->session->getKey()
        )
            ->where('status', 'success')
            ->first();

        $charger = $this->session->charger;

        $pdf = Pdf::loadView(
            'invoicepdf',
            [
                'session' => $this->session,
                'transaction' => $transaction,
                'charger' => $charger,
            ]
        );

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'invoice-session-' .
                $this->session->getKey() .
                '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}