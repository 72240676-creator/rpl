@extends('layouts.app')

@section('content')

<div style="
    max-width: 900px;
    margin: 30px auto;
    padding: 0 20px;
    font-family: inherit;
">

    {{-- =========================================================
        TOMBOL KEMBALI
    ========================================================== --}}
    <div style="margin-bottom: 16px;">

        <a
            href="{{ route('dashboard') }}"
            style="
                display: inline-flex;
                align-items: center;
                text-decoration: none;
                color: #4b5563;
                font-size: 14px;
                font-weight: 500;
            "
        >
            ← Kembali ke Dashboard
        </a>

    </div>


    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    ">

        <div>

            <h2 style="
                font-size: 24px;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 6px 0;
            ">
                Pemberitahuan
            </h2>

            <p style="
                color: #6b7280;
                font-size: 14px;
                margin: 0;
            ">
                Riwayat status pengisian daya dan notifikasi akun Anda
            </p>

        </div>


        {{-- =====================================================
            TANDAI SEMUA DIBACA
        ====================================================== --}}
        @if($unreadCount > 0)

            <form
                action="{{ route('notifications.markAllAsRead') }}"
                method="POST"
                style="margin: 0;"
            >

                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    style="
                        background-color: transparent;
                        border: 1.5px solid #10b981;
                        color: #059669;
                        font-weight: 600;
                        font-size: 13px;
                        padding: 8px 18px;
                        border-radius: 9999px;
                        cursor: pointer;
                    "
                >
                    Tandai Semua Dibaca
                </button>

            </form>

        @endif

    </div>


    {{-- =========================================================
        FLASH MESSAGE
    ========================================================== --}}
    @if(session('success'))

        <div style="
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        ">
            {{ session('success') }}
        </div>

    @endif


    {{-- =========================================================
        CONTAINER NOTIFIKASI
    ========================================================== --}}
    <div style="
        background-color: #ffffff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
        overflow: hidden;
    ">

        @forelse($notifications as $notif)

            @php

                /*
                |--------------------------------------------------------------------------
                | AMBIL DATA NOTIFIKASI
                |--------------------------------------------------------------------------
                */

                $data = is_array($notif->data)
                    ? $notif->data
                    : json_decode($notif->data, true);

                $data = $data ?? [];


                /*
                |--------------------------------------------------------------------------
                | DATA DASAR
                |--------------------------------------------------------------------------
                */

                $title = $data['title']
                    ?? 'Pemberitahuan';

                $message = $data['message']
                    ?? '';

                $sessionId = $data['session_id']
                    ?? null;

                $notificationType = $data['notification_type']
                    ?? null;


                /*
                |--------------------------------------------------------------------------
                | STATUS DIBACA
                |--------------------------------------------------------------------------
                */

                $isUnread = is_null($notif->read_at);


                /*
                |--------------------------------------------------------------------------
                | URL TUJUAN
                |--------------------------------------------------------------------------
                |
                | charging_finished
                | -> halaman pembayaran
                |
                | payment_success
                | -> halaman invoice
                |
                | Tetapi:
                | kalau session sudah PAID, selalu arahkan ke invoice.
                |
                */

                $notificationUrl = null;

                $chargingSession = null;


                if ($sessionId) {

                    /*
                    |--------------------------------------------------------------------------
                    | CARI CHARGING SESSION
                    |--------------------------------------------------------------------------
                    */

                    $chargingSession =
                        \App\Models\ChargingSession::find(
                            $sessionId
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA SESSION DITEMUKAN
                    |--------------------------------------------------------------------------
                    */

                    if ($chargingSession) {


                        /*
                        |--------------------------------------------------------------------------
                        | PEMBAYARAN SUDAH BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $notificationType === 'payment_success'
                            ||
                            $chargingSession->status === 'paid'
                        ) {

                            $notificationUrl = route(
                                'charging.invoice',
                                [
                                    'session' => $sessionId
                                ]
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | CHARGING SELESAI, BELUM BAYAR
                        |--------------------------------------------------------------------------
                        */

                        } elseif (
                            $notificationType === 'charging_finished'
                        ) {

                            $notificationUrl = route(
                                'charging.payment.view',
                                [
                                    'session' => $sessionId
                                ]
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | NOTIFIKASI LAMA
                        |--------------------------------------------------------------------------
                        |
                        | Digunakan untuk data notification lama yang belum
                        | mempunyai notification_type.
                        |
                        */

                        } else {

                            if (
                                $chargingSession->status === 'paid'
                            ) {

                                $notificationUrl = route(
                                    'charging.invoice',
                                    [
                                        'session' => $sessionId
                                    ]
                                );

                            } else {

                                $notificationUrl = route(
                                    'charging.payment.view',
                                    [
                                        'session' => $sessionId
                                    ]
                                );
                            }
                        }
                    }
                }

            @endphp


            {{-- =================================================
                SATU ITEM NOTIFIKASI
            ================================================== --}}
            <div style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 24px;
                border-bottom: 1px solid #f3f4f6;
                background-color:
                    {{ $isUnread ? '#f9fdfa' : '#ffffff' }};
            ">


                {{-- =============================================
                    BAGIAN KIRI
                ============================================== --}}
                <div style="
                    display: flex;
                    align-items: flex-start;
                    gap: 16px;
                    flex: 1;
                ">


                    {{-- TITIK STATUS --}}
                    <span style="
                        display: inline-block;
                        width: 10px;
                        height: 10px;
                        margin-top: 6px;
                        border-radius: 50%;
                        background-color:
                            {{ $isUnread ? '#10b981' : '#d1d5db' }};
                        flex-shrink: 0;
                    ">
                    </span>


                    {{-- =========================================
                        ISI NOTIFIKASI
                    ========================================== --}}
                    <div style="flex: 1;">


                        @if($notificationUrl)

                            {{-- =================================
                                NOTIFIKASI BISA DIKLIK
                            ================================== --}}
                            <a
                                href="{{ $notificationUrl }}"
                                style="
                                    text-decoration: none;
                                    color: inherit;
                                    display: block;
                                "
                            >

                                {{-- JUDUL --}}
                                <h4 style="
                                    font-size: 16px;
                                    font-weight:
                                        {{ $isUnread ? '700' : '500' }};
                                    color:
                                        {{ $isUnread ? '#111827' : '#374151' }};
                                    margin: 0 0 6px 0;
                                ">
                                    {{ $title }}
                                </h4>


                                {{-- PESAN --}}
                                <p style="
                                    font-size: 14px;
                                    color: #4b5563;
                                    margin: 0 0 8px 0;
                                    line-height: 1.5;
                                ">
                                    {{ $message }}
                                </p>

                            </a>


                        @else

                            {{-- =================================
                                NOTIFIKASI TANPA LINK
                            ================================== --}}

                            <h4 style="
                                font-size: 16px;
                                font-weight:
                                    {{ $isUnread ? '700' : '500' }};
                                color:
                                    {{ $isUnread ? '#111827' : '#374151' }};
                                margin: 0 0 6px 0;
                            ">
                                {{ $title }}
                            </h4>


                            <p style="
                                font-size: 14px;
                                color: #4b5563;
                                margin: 0 0 8px 0;
                                line-height: 1.5;
                            ">
                                {{ $message }}
                            </p>

                        @endif


                        {{-- =====================================
                            WAKTU NOTIFIKASI
                        ====================================== --}}
                        <span style="
                            font-size: 12px;
                            color: #9ca3af;
                        ">

                            {{
                                $notif->created_at
                                    ? $notif->created_at->diffForHumans()
                                    : 'Baru saja'
                            }}

                        </span>


                        {{-- =====================================
                            KETERANGAN LINK
                        ====================================== --}}
                        @if(
                            $notificationUrl &&
                            $notificationType === 'charging_finished' &&
                            $chargingSession &&
                            $chargingSession->status !== 'paid'
                        )

                            <div style="
                                margin-top: 8px;
                                font-size: 12px;
                                color: #059669;
                                font-weight: 600;
                            ">
                                Klik untuk melakukan pembayaran →
                            </div>

                        @elseif(
                            $notificationUrl &&
                            (
                                $notificationType === 'payment_success'
                                ||
                                (
                                    $chargingSession &&
                                    $chargingSession->status === 'paid'
                                )
                            )
                        )

                            <div style="
                                margin-top: 8px;
                                font-size: 12px;
                                color: #059669;
                                font-weight: 600;
                            ">
                                Klik untuk melihat invoice →
                            </div>

                        @endif


                    </div>

                </div>


                {{-- =============================================
                    TOMBOL SELESAI / TANDAI DIBACA
                ============================================== --}}
                @if($isUnread)

                    <form
                        action="{{ route(
                            'notifications.markAsRead',
                            $notif->id
                        ) }}"
                        method="POST"
                        style="
                            margin: 0;
                            margin-left: 16px;
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <button
                            type="submit"
                            style="
                                background-color: #ffffff;
                                border: 1px solid #e5e7eb;
                                color: #4b5563;
                                font-size: 12px;
                                font-weight: 600;
                                padding: 6px 14px;
                                border-radius: 9999px;
                                cursor: pointer;
                                white-space: nowrap;
                            "
                        >
                            Selesai
                        </button>

                    </form>

                @endif


            </div>


        @empty


            {{-- =================================================
                BELUM ADA NOTIFIKASI
            ================================================== --}}
            <div style="
                text-align: center;
                padding: 50px 20px;
                color: #9ca3af;
            ">

                <p style="
                    margin: 0;
                    font-size: 15px;
                ">
                    Belum ada pemberitahuan untuk Anda.
                </p>

            </div>


        @endforelse

    </div>

</div>

@endsection