@extends('layouts.app')

@section('content')

<style>
    .notification-page {
        width: 100%;
        max-width: 1080px;
        margin: 42px auto !important;
        padding: 0 28px !important;
        box-sizing: border-box;
    }

    .notification-back {
        margin-bottom: 20px !important;
    }

    .notification-back a {
        color: #64748b !important;
        font-weight: 600 !important;
        transition: color .2s ease;
    }

    .notification-back a:hover {
        color: #047857 !important;
    }

    .notification-header {
        align-items: center !important;
        padding: 26px 30px;
        margin-bottom: 22px !important;
        border: 1px solid #e5eee9;
        border-radius: 20px;
        background: linear-gradient(115deg, #ffffff 0%, #f0fdf7 100%);
        box-shadow: 0 8px 26px rgba(15, 23, 42, .04);
    }

    .notification-heading {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .notification-heading-icon,
    .notification-item-icon,
    .notification-empty-icon {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        color: #047857;
        background: #ecfdf5;
    }

    .notification-heading-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: #d1fae5;
    }

    .notification-heading-icon svg {
        width: 25px;
        height: 25px;
    }

    .notification-title {
        color: #0f172a !important;
        font-size: 25px !important;
        letter-spacing: -.5px;
    }

    .notification-subtitle {
        color: #64748b !important;
        line-height: 1.6;
    }

    .notification-mark-all {
        border: 0 !important;
        padding: 11px 17px !important;
        color: #ffffff !important;
        background: #059669 !important;
        box-shadow: 0 4px 10px rgba(5, 150, 105, .18);
        transition: background .2s ease, transform .2s ease;
    }

    .notification-mark-all:hover {
        background: #047857 !important;
        transform: translateY(-1px);
    }

    .notification-flash {
        border-radius: 12px !important;
        margin-bottom: 20px;
    }

    .notification-card {
        overflow: hidden;
        border: 1px solid #e8edf2 !important;
        border-radius: 18px !important;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .055) !important;
    }

    .notification-item {
        min-width: 0;
        gap: 18px;
        padding: 21px 26px !important;
        border-bottom: 1px solid #eef2f6 !important;
        transition: background .2s ease;
    }

    .notification-item:last-child {
        border-bottom: 0 !important;
    }

    .notification-item:hover {
        background: #f8fbfa !important;
    }

    .notification-item-content {
        min-width: 0;
        gap: 15px !important;
        align-items: flex-start !important;
    }

    .notification-item-icon {
        width: 42px;
        height: 42px;
        border-radius: 13px;
    }

    .notification-item-icon.warning {
        color: #b45309;
        background: #fffbeb;
    }

    .notification-item-icon svg {
        width: 20px;
        height: 20px;
    }

    .notification-item-copy {
        min-width: 0;
    }

    .notification-item-title {
        margin: 0 0 5px !important;
        color: #1e293b !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        line-height: 1.5;
    }

    .notification-item-message {
        margin: 0 0 9px !important;
        color: #64748b !important;
        font-size: 13px !important;
        line-height: 1.65 !important;
        overflow-wrap: anywhere;
    }

    .notification-item-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px;
        color: #94a3b8;
        font-size: 12px;
    }

    .notification-time {
        color: #94a3b8;
        white-space: nowrap;
    }

    .notification-tag {
        display: inline-flex;
        padding: 4px 9px;
        border-radius: 999px;
        color: #047857;
        background: #ecfdf5;
        font-size: 11px;
        font-weight: 700;
    }

    .notification-tag.warning {
        color: #b45309;
        background: #fffbeb;
    }

    .notification-action {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 11px;
        padding: 8px 12px;
        border: 1px solid #a7f3d0;
        border-radius: 9px;
        color: #047857;
        background: #f0fdf7;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: background .2s ease, border-color .2s ease;
    }

    .notification-action:hover {
        border-color: #6ee7b7;
        background: #d1fae5;
    }

    .notification-done {
        flex: 0 0 auto;
        padding: 8px 13px !important;
        border-color: #dbe5e1 !important;
        border-radius: 9px !important;
        color: #475569 !important;
        transition: all .2s ease;
    }

    .notification-done-form {
        margin: 0 0 0 16px !important;
    }

    .notification-done:hover {
        border-color: #10b981 !important;
        color: #047857 !important;
        background: #ecfdf5 !important;
    }

    .notification-empty {
        padding: 60px 24px !important;
    }

    .notification-empty-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 15px;
        border-radius: 18px;
    }

    .notification-empty-icon svg {
        width: 28px;
        height: 28px;
    }

    @media (max-width: 640px) {
        .notification-page {
            margin: 24px auto !important;
            padding: 0 16px !important;
        }

        .notification-header {
            align-items: flex-start !important;
            padding: 20px !important;
        }

        .notification-heading {
            align-items: flex-start;
            gap: 13px;
        }

        .notification-heading-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
        }

        .notification-title {
            font-size: 21px !important;
        }

        .notification-item {
            align-items: flex-start !important;
            padding: 17px !important;
        }

        .notification-item-content {
            gap: 11px !important;
        }

        .notification-item-icon {
            width: 36px;
            height: 36px;
        }

        .notification-done {
            padding: 7px 9px !important;
            font-size: 11px !important;
        }

        .notification-done-form {
            flex: 1 1 100%;
            margin: 0 !important;
            display: flex;
            justify-content: flex-end;
        }
    }
</style>

<div class="notification-page" style="
    max-width: 1080px;
    width: 100%;
    margin: 30px auto;
    padding: 0 20px;
    box-sizing: border-box;
    font-family: inherit;
">

    {{-- =========================================================
        TOMBOL KEMBALI
    ========================================================== --}}
    <div class="notification-back" style="margin-bottom: 16px;">

        <a
            href="{{ $isAdmin ? route('admin.dashboard') : route('dashboard') }}"
            style="
                display: inline-flex;
                align-items: center;
                text-decoration: none;
                color: #4b5563;
                font-size: 14px;
                font-weight: 500;
            "
        >
            ← Kembali ke {{ $isAdmin ? 'Dashboard Super Admin' : 'Dashboard' }}
        </a>

    </div>


    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="notification-header" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    ">

        <div class="notification-heading">
            <div class="notification-heading-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                    <path d="M10 21h4" />
                </svg>
            </div>
            <div>

            <h2 style="
                font-size: 24px;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 6px 0;
            " class="notification-title">
                Notifikasi
            </h2>

            <p class="notification-subtitle" style="
                color: #6b7280;
                font-size: 14px;
                margin: 0;
            ">
                {{ $isAdmin
                    ? 'Ringkasan aktivitas charging dan kondisi operasional stasiun.'
                    : 'Riwayat status pengisian daya dan notifikasi akun Anda' }}
            </p>

            </div>
        </div>


        {{-- =====================================================
            TANDAI SEMUA DIBACA
        ====================================================== --}}
        @if(!$isAdmin && $unreadCount > 0)

            <form
                action="{{ route('notifications.markAllAsRead') }}"
                method="POST"
                style="margin: 0;"
            >

                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    class="notification-mark-all" style="
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

        <div class="notification-flash" style="
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
    <div class="notification-card" style="
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
                    ?? 'Notifikasi';

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

                $notificationUrl = $data['action_url'] ?? $notificationUrl;
                $actionLabel = $data['action_label'] ?? null;
                $recommendationType = $data['recommendation_type'] ?? null;

            @endphp

            @php
                $isOperationalWarning = $isAdmin && (
                    str_contains(strtolower($title), 'perlu diperiksa')
                    || str_contains(strtolower($title), 'berubah')
                );
                $isLowBalanceRecommendation = $recommendationType === 'low_balance';
            @endphp


            {{-- =================================================
                SATU ITEM NOTIFIKASI
            ================================================== --}}
            <div class="notification-item" style="
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
                <div class="notification-item-content" style="
                    display: flex;
                    align-items: flex-start;
                    gap: 16px;
                    flex: 1;
                ">


                    <div class="notification-item-icon {{ $isOperationalWarning || $isLowBalanceRecommendation ? 'warning' : '' }}" aria-hidden="true">
                        @if($isOperationalWarning)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3 2.8 19a1.4 1.4 0 0 0 1.2 2h16a1.4 1.4 0 0 0 1.2-2L12 3Z" />
                                <path d="M12 9v4m0 4h.01" />
                            </svg>
                        @elseif($recommendationType === 'low_balance')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="6" width="18" height="14" rx="2" />
                                <path d="M3 10h18m-5 5h2" />
                                <path d="M7 3v3" />
                            </svg>
                        @elseif($recommendationType === 'points')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z" />
                            </svg>
                        @elseif($recommendationType === 'stations')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                                <circle cx="12" cy="10" r="2.5" />
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7h18v13H3zM7 7V4h10v3m-8 5h6" />
                                <path d="m9 16 2 2 4-4" />
                            </svg>
                        @endif
                    </div>


                    {{-- =========================================
                        ISI NOTIFIKASI
                    ========================================== --}}
                    <div class="notification-item-copy" style="flex: 1;">


                        @if($notificationUrl)

                            {{-- =================================
                                NOTIFIKASI BISA DIKLIK
                            ================================== --}}
                            <a
                                href="{{ $notificationUrl }}"
                                class="notification-item-link" style="
                                    text-decoration: none;
                                    color: inherit;
                                    display: block;
                                "
                            >

                                {{-- JUDUL --}}
                                <h4 class="notification-item-title" style="
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
                                <p class="notification-item-message" style="
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

                            <h4 class="notification-item-title" style="
                                font-size: 16px;
                                font-weight:
                                    {{ $isUnread ? '700' : '500' }};
                                color:
                                    {{ $isUnread ? '#111827' : '#374151' }};
                                margin: 0 0 6px 0;
                            ">
                                {{ $title }}
                            </h4>


                            <p class="notification-item-message" style="
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
                        <div class="notification-item-meta">
                        @if($isAdmin)
                            <span class="notification-tag {{ $isOperationalWarning ? 'warning' : '' }}">
                                {{ $isOperationalWarning ? 'Perlu perhatian' : 'Aktivitas operasional' }}
                            </span>
                        @elseif($recommendationType)
                            <span class="notification-tag {{ $isLowBalanceRecommendation ? 'warning' : '' }}">
                                {{ $isLowBalanceRecommendation ? 'Saldo perlu diisi' : 'Rekomendasi untuk Anda' }}
                            </span>
                        @elseif($isUnread)
                            <span class="notification-tag">Belum dibaca</span>
                        @endif
                                        @php
                                            $notificationCreatedAt = $notif->created_at;
                                            $notificationTime = $notificationCreatedAt?->timezone('Asia/Jakarta');
                                        @endphp
                                        <time
                                            class="notification-time"
                                            @if($notificationCreatedAt)
                                                datetime="{{ $notificationCreatedAt->toIso8601String() }}"
                                                title="{{ $notificationTime->format('d M Y, H:i:s') }}"
                                                data-notification-time
                                            @endif
                                        >
                                            {{ $notificationTime?->format('d M Y, H:i:s') ?? 'Baru saja' }}
                                        </time>
                                        </div>

                        @if($actionLabel && $notificationUrl)
                            <a class="notification-action" href="{{ $notificationUrl }}">
                                {{ $actionLabel }} <span aria-hidden="true">→</span>
                            </a>
                        @endif


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
                @if(!$isAdmin && $isUnread && $notif->id)

                    <form
                        action="{{ route(
                            'notifications.markAsRead',
                            $notif->id
                        ) }}"
                        method="POST"
                        class="notification-done-form" style="
                            margin: 0;
                            margin-left: 16px;
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <button
                            type="submit"
                            class="notification-done" style="
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
            <div class="notification-empty" style="
                text-align: center;
                padding: 50px 20px;
                color: #9ca3af;
            ">

                <div class="notification-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                        <path d="M10 21h4" />
                    </svg>
                </div>
                <p style="
                    margin: 0;
                    font-size: 15px;
                ">
                    {{ $isAdmin
                        ? 'Belum ada aktivitas operasional atau charger yang memerlukan perhatian.'
                        : 'Belum ada pemberitahuan untuk Anda.' }}
                </p>

            </div>


        @endforelse

    </div>

</div>

<script>
    (() => {
        const formatRelativeTime = (timestamp) => {
            const seconds = Math.max(0, Math.floor((Date.now() - timestamp) / 1000));

            if (seconds < 45) return 'Baru saja';
            if (seconds < 90) return '1 menit yang lalu';

            const minutes = Math.floor(seconds / 60);
            if (minutes < 45) return `${minutes} menit yang lalu`;
            if (minutes < 90) return '1 jam yang lalu';

            const hours = Math.floor(minutes / 60);
            if (hours < 22) return `${hours} jam yang lalu`;
            if (hours < 36) return 'Kemarin';

            const days = Math.floor(hours / 24);
            if (days < 7) return `${days} hari yang lalu`;

            return null;
        };

        const updateNotificationTimes = () => {
            document.querySelectorAll('[data-notification-time]').forEach((element) => {
                const timestamp = Date.parse(element.dateTime);
                if (Number.isNaN(timestamp)) return;

                const relativeTime = formatRelativeTime(timestamp);
                if (relativeTime) {
                    element.textContent = relativeTime;
                    return;
                }

                element.textContent = new Intl.DateTimeFormat('id-ID', {
                    dateStyle: 'medium',
                    timeStyle: 'short',
                    timeZone: 'Asia/Jakarta',
                }).format(timestamp);
            });
        };

        updateNotificationTimes();
        window.setInterval(updateNotificationTimes, 30000);
    })();
</script>

@endsection