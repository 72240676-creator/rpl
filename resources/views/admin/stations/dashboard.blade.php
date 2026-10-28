
@extends('layouts.app')

@section('content')
<style>
    .admin-dashboard {
        max-width: 1450px;
        margin: 0 auto;
        padding: 28px;
        color: #14213d;
    }

    .dashboard-heading {
        margin-bottom: 22px;
    }

    .dashboard-heading h1 {
        font-size: 27px;
        font-weight: 750;
        margin: 0 0 6px;
    }

    .dashboard-heading p {
        color: #718096;
        margin: 0;
        font-size: 14px;
    }

    .welcome-banner {
        background: linear-gradient(115deg, #059669, #10b981);
        color: white;
        padding: 28px 30px;
        border-radius: 16px;
        margin-bottom: 22px;
        box-shadow: 0 8px 20px rgba(5, 150, 105, .12);
    }

    .welcome-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .welcome-banner h2 {
        font-size: 23px;
        font-weight: 750;
        margin: 0 0 8px;
    }

    .welcome-banner p {
        margin: 0;
        font-size: 14px;
        opacity: .95;
    }

    .btn-kelola-spklu {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 18px;
        background: white;
        color: #047857;
        border-radius: 9px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        transition: all .2s ease;
    }

    .btn-kelola-spklu:hover {
        background: #ecfdf5;
        color: #047857;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .stat-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .025);
    }

    .stat-icon {
        width: 54px;
        height: 54px;
        flex-shrink: 0;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
    }

    .icon-green { background: #dcfce7; }
    .icon-blue { background: #dbeafe; }
    .icon-orange { background: #ffedd5; }
    .icon-purple { background: #f3e8ff; }

    .stat-label {
        color: #718096;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .stat-number {
        font-size: 27px;
        line-height: 1.2;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .stat-note {
        color: #718096;
        font-size: 11px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 18px;
        margin-bottom: 20px;
    }

    .dashboard-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        padding: 23px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .025);
        min-width: 0;
    }

    .card-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .card-heading h3 {
        font-size: 16px;
        font-weight: 750;
        margin: 0 0 5px;
    }

    .card-heading p {
        font-size: 12px;
        color: #718096;
        margin: 0;
    }

    .heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: #ecfdf5;
        border-radius: 12px;
        font-size: 20px;
        flex-shrink: 0;
    }

    .status-item {
        margin-bottom: 19px;
    }

    .status-item:last-child {
        margin-bottom: 0;
    }

    .status-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        margin-bottom: 9px;
        gap: 10px;
    }

    .status-name {
        display: flex;
        align-items: center;
        gap: 9px;
        font-weight: 600;
    }

    .status-count {
        font-weight: 700;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .dot-green { background: #10b981; }
    .dot-gray { background: #94a3b8; }
    .dot-orange { background: #f59e0b; }

    .progress-track {
        width: 100%;
        height: 9px;
        background: #edf2f7;
        border-radius: 20px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 20px;
    }

    .fill-green { background: #10b981; width: 77%; }
    .fill-gray { background: #94a3b8; width: 14%; }
    .fill-orange { background: #f59e0b; width: 8%; }

    .system-list {
        border: 1px solid #e8edf4;
        border-radius: 12px;
        overflow: hidden;
    }

    .system-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 15px 13px;
        border-bottom: 1px solid #edf2f7;
        font-size: 12px;
    }

    .system-row:last-child {
        border-bottom: none;
    }

    .system-label {
        color: #475569;
    }

    .system-value {
        font-weight: 650;
        text-align: right;
    }

    .health-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 10px;
        border-radius: 30px;
        color: #047857;
        background: #d1fae5;
        font-size: 11px;
        font-weight: 700;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        padding: 15px 0;
        border-bottom: 1px solid #edf2f7;
    }

    .activity-item:first-child {
        padding-top: 0;
    }

    .activity-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .activity-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #dcfce7;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 17px;
    }

    .activity-text {
        flex: 1;
        min-width: 0;
    }

    .activity-text h4 {
        font-size: 13px;
        margin: 2px 0 5px;
        font-weight: 700;
    }

    .activity-text p {
        font-size: 12px;
        color: #718096;
        margin: 0;
        line-height: 1.6;
    }

    .activity-time {
        color: #94a3b8;
        font-size: 11px;
        white-space: nowrap;
        padding-top: 4px;
    }

    .admin-profile {
        border: 1px solid #e8edf4;
        border-radius: 12px;
        padding: 17px;
    }

    .profile-row {
        display: grid;
        grid-template-columns: 90px 1fr;
        gap: 12px;
        font-size: 13px;
        margin-bottom: 15px;
    }

    .profile-row:last-of-type {
        margin-bottom: 0;
    }

    .profile-label {
        color: #718096;
    }

    .profile-value {
        font-weight: 650;
        overflow-wrap: anywhere;
    }

    .admin-tip {
        margin-top: 20px;
        padding: 14px;
        border-radius: 11px;
        background: #ecfdf5;
        color: #047857;
        font-size: 12px;
        line-height: 1.7;
    }

    .dashboard-footer {
        text-align: center;
        color: #94a3b8;
        font-size: 11px;
        padding: 12px 0 3px;
    }

    @media (max-width: 1050px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .admin-dashboard {
            padding: 16px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .welcome-banner {
            padding: 22px;
        }

        .welcome-banner h2 {
            font-size: 20px;
        }

        .welcome-content {
            align-items: flex-start;
            flex-direction: column;
        }

        .dashboard-card {
            padding: 17px;
        }

        .activity-item {
            flex-wrap: wrap;
        }

        .activity-time {
            margin-left: 51px;
            padding-top: 0;
        }
    }
</style>

<div class="admin-dashboard">

    {{-- Judul Dashboard --}}
    <div class="dashboard-heading">
        <h1>Dashboard Admin</h1>
        <p>Ringkasan operasional EV Charge dan informasi sistem.</p>
    </div>

    {{-- Banner Sambutan --}}
    <div class="welcome-banner">
        <div class="welcome-content">
            <div>
                <h2>⚡ Selamat datang, {{ Auth::user()->nama ?? Auth::user()->name ?? 'Super Admin' }}!</h2>
                <p>Pantau stasiun, unit charger, dan aktivitas sistem melalui satu halaman.</p>
            </div>

            <a href="{{ route('admin.stations.index') }}" class="btn-kelola-spklu">
                🏢 Kelola SPKLU <span>→</span>
            </a>
        </div>
    </div>

    {{-- Statistik: data contoh --}}
    <div class="stats-grid">

        <div class="stat-card">
            <div class="stat-icon icon-green">🏢</div>
            <div>
                <div class="stat-label">Total Stasiun</div>
                <div class="stat-number">12</div>
                <div class="stat-note">Stasiun SPKLU terdaftar</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-blue">🔌</div>
            <div>
                <div class="stat-label">Total Unit Charger</div>
                <div class="stat-number">36</div>
                <div class="stat-note">Unit charger terdaftar</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-green">⚡</div>
            <div>
                <div class="stat-label">Charger Online</div>
                <div class="stat-number">28</div>
                <div class="stat-note">77% dari total unit</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-orange">⚠️</div>
            <div>
                <div class="stat-label">Perlu Perhatian</div>
                <div class="stat-number" style="color:#ea580c;">3</div>
                <div class="stat-note">Offline atau maintenance</div>
            </div>
        </div>

    </div>

    {{-- Status Operasional dan Ringkasan Sistem --}}
    <div class="dashboard-grid">

        <div class="dashboard-card">
            <div class="card-heading">
                <div class="heading-icon">📊</div>
                <div>
                    <h3>Status Operasional Charger</h3>
                    <p>Contoh distribusi status unit charger.</p>
                </div>
            </div>

            <div class="status-item">
                <div class="status-info">
                    <span class="status-name">
                        <span class="status-dot dot-green"></span>
                        Online
                    </span>
                    <span class="status-count">28 unit · 77%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill fill-green"></div>
                </div>
            </div>

            <div class="status-item">
                <div class="status-info">
                    <span class="status-name">
                        <span class="status-dot dot-gray"></span>
                        Offline
                    </span>
                    <span class="status-count">5 unit · 14%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill fill-gray"></div>
                </div>
            </div>

            <div class="status-item">
                <div class="status-info">
                    <span class="status-name">
                        <span class="status-dot dot-orange"></span>
                        Maintenance
                    </span>
                    <span class="status-count">3 unit · 8%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill fill-orange"></div>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-heading">
                <div class="heading-icon">⚙️</div>
                <div>
                    <h3>Ringkasan Sistem</h3>
                    <p>Informasi umum aplikasi.</p>
                </div>
            </div>

            <div class="system-list">
                <div class="system-row">
                    <span class="system-label">Status Sistem</span>
                    <span class="health-badge">
                        <span class="status-dot dot-green"></span>
                        Berjalan Normal
                    </span>
                </div>

                <div class="system-row">
                    <span class="system-label">Aplikasi</span>
                    <span class="system-value">EV Charge</span>
                </div>

                <div class="system-row">
                    <span class="system-label">Lingkungan</span>
                    <span class="system-value">Development</span>
                </div>

                <div class="system-row">
                    <span class="system-label">Hak Akses</span>
                    <span class="system-value">Administrator</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Aktivitas dan Informasi Admin --}}
    <div class="dashboard-grid">

        <div class="dashboard-card">
            <div class="card-heading">
                <div class="heading-icon">📋</div>
                <div>
                    <h3>Aktivitas Terbaru</h3>
                    <p>Contoh aktivitas yang dapat dicatat sistem.</p>
                </div>
            </div>

            <div class="activity-list">

                <div class="activity-item">
                    <div class="activity-icon">📝</div>
                    <div class="activity-text">
                        <h4>Pembaruan Data Stasiun</h4>
                        <p>Informasi stasiun diperbarui oleh administrator.</p>
                    </div>
                    <div class="activity-time">Contoh</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon" style="background:#dbeafe;">🔌</div>
                    <div class="activity-text">
                        <h4>Pengelolaan Unit Charger</h4>
                        <p>Data unit charger dapat dikelola melalui menu Kelola SPKLU.</p>
                    </div>
                    <div class="activity-time">Contoh</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon" style="background:#f3e8ff;">🔔</div>
                    <div class="activity-text">
                        <h4>Pemeriksaan Notifikasi</h4>
                        <p>Informasi dan pemberitahuan sistem tersedia di menu Notifikasi.</p>
                    </div>
                    <div class="activity-time">Contoh</div>
                </div>

            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-heading">
                <div class="heading-icon">👤</div>
                <div>
                    <h3>Informasi Administrator</h3>
                    <p>Informasi akun yang sedang login.</p>
                </div>
            </div>

            <div class="admin-profile">
                <div class="profile-row">
                    <span class="profile-label">Nama</span>
                    <span class="profile-value">
                        {{ Auth::user()->nama ?? Auth::user()->name ?? 'Administrator' }}
                    </span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Email</span>
                    <span class="profile-value">
                        {{ Auth::user()->email ?? '-' }}
                    </span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Peran</span>
                    <span class="profile-value">Administrator</span>
                </div>

                <div class="admin-tip">
                    💡 Gunakan menu Kelola SPKLU untuk mengelola data stasiun dan unit charger.
                    Periksa menu Notifikasi untuk melihat pemberitahuan sistem.
                </div>
            </div>
        </div>

    </div>

    <div class="dashboard-footer">
        EV Charge · Panel Administrator
    </div>

</div>
@endsection
