@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 30px auto; padding: 0 20px; font-family: inherit;">
    
    <!-- Tombol Kembali ke Dashboard -->
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; text-decoration: none; color: #4b5563; font-size: 14px; font-weight: 500; transition: color 0.2s;">
            ← Kembali ke Dashboard
        </a>
    </div>

    <!-- Bagian Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1f2937; margin: 0 0 6px 0;">Pemberitahuan</h2>
            <p style="color: #6b7280; font-size: 14px; margin: 0;">Riwayat status pengisian daya dan notifikasi akun Anda</p>
        </div>
        @if($unreadCount > 0)
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST" style="margin: 0;">
                @csrf
                @method('PATCH')
                <button type="submit" style="background-color: transparent; border: 1.5px solid #10b981; color: #059669; font-weight: 600; font-size: 13px; padding: 8px 18px; border-radius: 9999px; cursor: pointer; transition: 0.2s;">
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- Alert Flash Message -->
    @if(session('success'))
        <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Container Kartu Notifikasi -->
    <div style="background-color: #ffffff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6; overflow: hidden;">
        @forelse($notifications as $notif)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; background-color: {{ !$notif->is_read ? '#f9fdfa' : '#ffffff' }};">
                <div style="display: flex; align-items: flex-start; gap: 16px;">
                    <!-- Indikator Status Titik -->
                    <span style="display: inline-block; width: 10px; height: 10px; margin-top: 6px; border-radius: 50%; background-color: {{ !$notif->is_read ? '#10b981' : '#d1d5db' }}; flex-shrink: 0;"></span>
                    
                    <div>
                        <h4 style="font-size: 16px; font-weight: {{ !$notif->is_read ? '700' : '500' }}; color: {{ !$notif->is_read ? '#111827' : '#4b5563' }}; margin: 0 0 6px 0;">
                            {{ $notif->title }}
                        </h4>
                        <p style="font-size: 14px; color: #4b5563; margin: 0 0 8px 0; line-height: 1.5;">
                            {{ $notif->message }}
                        </p>
                        <span style="font-size: 12px; color: #9ca3af;">
                            {{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Baru saja' }}
                        </span>
                    </div>
                </div>

                @if(!$notif->is_read)
                    <form action="{{ route('notifications.markAsRead', $notif->id) }}" method="POST" style="margin: 0; margin-left: 16px;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" style="background-color: #ffffff; border: 1px solid #e5e7eb; color: #4b5563; font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 9999px; cursor: pointer;">
                            Selesai
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 50px 20px; color: #9ca3af;">
                <p style="margin: 0; font-size: 15px;">Belum ada pemberitahuan untuk Anda.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection