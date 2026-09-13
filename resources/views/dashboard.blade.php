@extends('layouts.app')

@section('title', 'Dashboard EV')

@section('content')
<div class="dashboard-container">
    
    <!-- Bagian Header -->
    <div class="dash-header">
        <div>
            <!-- Menggunakan 'nama' sesuai kolom database kustom -->
            <h2 class="dash-title">Selamat datang, {{ auth()->user()->nama }}</h2>
            <p class="dash-subtitle">Siap mengisi daya kendaraanmu hari ini?</p>
        </div>

        <!-- Kapsul Saldo & Poin (Referensi MyPertamina/PLN) -->
        <div class="stats-row">
            <div class="stat-pill">
                <span>💳</span> Saldo Rp0
            </div>
            <div class="stat-pill">
                <span>⚡</span> 0 Poin
            </div>
        </div>
    </div>

    <!-- Bagian Utama -->
    <div class="dash-body">
        
        <!-- Baris 1: Banner Pendaftaran EV -->
        <div class="ev-banner">
            <div>
                <h3>🚗 Daftarkan Kendaraan Listrikmu</h3>
                <p>Tambahkan plat nomor dan kapasitas baterai (kWh) untuk fitur optimal.</p>
            </div>
            <!-- Mengubah tombol biasa menjadi tautan ke halaman profile -->
            <a href="/profile" style="background: #ffffff; color: #059669; text-decoration: none; padding: 12px 30px; border-radius: 20px; font-weight: 700; font-size: 15px; transition: 0.2s; display: inline-block;">Register EV</a>
        </div>

        <!-- Baris 2: Informasi Lokasi SPKLU (Tampilan Baru) -->
        <div style="background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; padding: 25px 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; position: relative; overflow: hidden;">
            
            <!-- Aksen Dekoratif Lingkaran -->
            <div style="position: absolute; top: -30px; right: -20px; width: 120px; height: 120px; background: #10b981; opacity: 0.04; border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -20px; right: 80px; width: 60px; height: 60px; background: #0284c7; opacity: 0.03; border-radius: 50%;"></div>

            <!-- Bagian Kiri: Informasi / Teks -->
            <div style="flex: 1; min-width: 280px; z-index: 1;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="background: #ecfdf5; color: #10b981; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 2px 10px rgba(16, 185, 129, 0.15);">
                        📍
                    </div>
                    <h4 style="margin: 0; font-size: 19px; font-weight: 700; color: #0f172a; letter-spacing: -0.3px;">Stasiun Pengisian Terdekat</h4>
                </div>

                @if(isset($nearestLocation) && $nearestLocation)
                    <div style="background: #ffffff; border: 1px solid #f1f5f9; padding: 18px; border-radius: 14px; display: inline-block; width: 100%; max-width: 500px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        <h5 style="margin: 0 0 6px 0; font-size: 16px; color: #1e293b; font-weight: 700;">{{ $nearestLocation->nama_lokasi }}</h5>
                        <p style="margin: 0 0 14px 0; font-size: 13.5px; color: #64748b; line-height: 1.5;">{{ $nearestLocation->alamat }}</p>
                        
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <!-- Badge Status -->
                            <span style="background: {{ $nearestLocation->status == 'aktif' ? '#d1fae5' : '#fee2e2' }}; color: {{ $nearestLocation->status == 'aktif' ? '#065f46' : '#991b1b' }}; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                                <span style="width: 8px; height: 8px; background: {{ $nearestLocation->status == 'aktif' ? '#10b981' : '#ef4444' }}; border-radius: 50%; display: inline-block; animation: pulse 2s infinite;"></span> 
                                {{ strtoupper(str_replace('_', ' ', $nearestLocation->status)) }}
                            </span>
                            <!-- Badge Jam Operasional -->
                            <span style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                🕒 {{ $nearestLocation->jam_operasional ?? '24 Jam' }}
                            </span>
                        </div>
                    </div>
                @else
                    <!-- Tampilan Jika Data Kosong -->
                    <div style="background: #f8fafc; border: 2px dashed #cbd5e1; padding: 18px; border-radius: 14px; display: inline-block; width: 100%; max-width: 500px;">
                        <p style="margin: 0; font-size: 14px; color: #64748b; display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 20px;">⚠️</span> 
                            <span>Belum ada lokasi SPKLU aktif di sekitar Anda saat ini.<br><i style="font-size: 12.5px;">Silakan cek kembali nanti atau cari di area lain.</i></span>
                        </p>
                    </div>
                @endif
            </div>

            <!-- Bagian Kanan: Tombol Aksi -->
            <div style="z-index: 1;">
                <a href="{{ route('locations.index') }}" style="background: #0f172a; color: #ffffff; padding: 14px 28px; border-radius: 14px; text-decoration: none; font-weight: 600; font-size: 14.5px; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 6px 15px rgba(15, 23, 42, 0.2); transition: all 0.3s ease; white-space: nowrap;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(15, 23, 42, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 15px rgba(15, 23, 42, 0.2)';">
                    🗺️ Buka Peta SPKLU
                </a>
            </div>
            
            <!-- Tambahan CSS untuk animasi indikator status berkedip -->
            <style>
                @keyframes pulse {
                    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
                    70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
                }
            </style>
        </div>

        <!-- Baris 3: Grid Layanan -->
        <div class="services-section">
            <div class="services-title">Butuh isi daya di perjalanan?</div>
            <div class="services-grid">
                <div class="service-item"><span class="service-icon">🚙</span><span class="service-name">SPKLU</span></div>
                <div class="service-item"><span class="service-icon">🛵</span><span class="service-name">SPKLU R2</span></div>
                <div class="service-item"><span class="service-icon">🔌</span><span class="service-name">SPLU</span></div>
                <div class="service-item"><span class="service-icon">🔋</span><span class="service-name">Tukar Baterai</span></div>
            </div>
        </div>
        
    </div>

    <!-- Tombol Mengambang / Scan Barcode -->
    <button class="floating-scan">
        <span style="font-size: 20px;">📷</span> Scan / Charge EV
    </button>
    
</div>
@endsection