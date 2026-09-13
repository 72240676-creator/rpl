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

        <!-- Baris 2: Informasi Lokasi SPKLU -->
        <div class="location-card">
            <div class="loc-info">
                <h4>📍 SPKLU Terdekat</h4>
                <p>Jl. Kusumanegara, Kota Yogyakarta<br><i>Tersedia: 2 Konektor</i></p>
            </div>
            <a href="#" class="btn-cari">Cari Lokasi Lain</a>
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