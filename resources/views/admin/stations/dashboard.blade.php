
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

    .stat-card-button {
        width: 100%;
        color: inherit;
        font: inherit;
        text-align: left;
        cursor: pointer;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .stat-card-button:hover,
    .stat-card-button[aria-expanded="true"] {
        border-color: #fdba74;
        box-shadow: 0 8px 20px rgba(234, 88, 12, .09);
        transform: translateY(-1px);
    }

    .stat-card-link {
        color: inherit;
        text-decoration: none;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .stat-card-link:hover {
        border-color: #6ee7b7;
        box-shadow: 0 8px 20px rgba(5, 150, 105, .09);
        transform: translateY(-2px);
    }

    .stat-card-link:active,
    .stat-card-button:active {
        transform: translateY(0);
    }

    .stat-card-link:focus-visible {
        outline: 3px solid rgba(16, 185, 129, .3);
        outline-offset: 3px;
    }

    .stat-card-button:focus-visible {
        outline: 3px solid rgba(249, 115, 22, .3);
        outline-offset: 3px;
    }

    .stat-card-link .stat-note {
        color: #047857;
        font-weight: 650;
    }

    .stat-card-button .stat-note {
        color: #c2410c;
        font-weight: 650;
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

    .fill-green { background: #10b981; }
    .fill-gray { background: #94a3b8; }
    .fill-orange { background: #f59e0b; }

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

    .inspection-panel {
        margin-bottom: 20px;
    }

    .inspection-panel[hidden] {
        display: none;
    }

    .inspection-panel {
        animation: inspection-panel-in .2s ease-out;
    }

    @keyframes inspection-panel-in {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .inspection-list {
        display: grid;
        gap: 12px;
    }

    .inspection-report {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(240px, .75fr);
        gap: 18px;
        padding: 17px;
        border: 1px solid #f1e4c6;
        border-radius: 13px;
        background: linear-gradient(110deg, #fffdf7, #ffffff);
    }

    .inspection-report-main {
        min-width: 0;
    }

    .inspection-report-title {
        margin: 0 0 6px;
        color: #1e293b;
        font-size: 13px;
        font-weight: 750;
    }

    .inspection-report-meta,
    .inspection-report-time {
        color: #64748b;
        font-size: 11px;
        line-height: 1.6;
    }

    .inspection-report-note {
        margin: 10px 0 0;
        padding: 10px 12px;
        border-radius: 9px;
        color: #78350f;
        background: #fffbeb;
        font-size: 12px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .inspection-report-form {
        display: grid;
        align-content: start;
        gap: 8px;
    }

    .inspection-report-form form {
        display: grid;
        gap: 8px;
    }

    .inspection-report-form label {
        color: #475569;
        font-size: 11px;
        font-weight: 700;
    }

    .inspection-report-form textarea {
        width: 100%;
        min-height: 66px;
        padding: 9px 10px;
        box-sizing: border-box;
        resize: vertical;
        border: 1px solid #dbe3eb;
        border-radius: 9px;
        color: #334155;
        font: inherit;
        font-size: 12px;
    }

    .inspection-report-form textarea:focus {
        border-color: #34d399;
        outline: none;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, .12);
    }

    .inspection-report-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .inspection-report-button {
        min-height: 34px;
        padding: 7px 10px;
        border: 0;
        border-radius: 8px;
        color: #fff;
        background: #059669;
        font: inherit;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    .inspection-report-button:hover {
        background: #047857;
    }

    .inspection-report-button.resolve {
        color: #047857;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
    }

    .inspection-report-button.resolve:hover {
        background: #d1fae5;
    }

    .inspection-empty {
        padding: 17px;
        border: 1px solid #a7f3d0;
        border-radius: 11px;
        color: #047857;
        background: #f0fdf4;
        font-size: 12px;
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

        .inspection-report {
            grid-template-columns: minmax(0, 1fr);
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
                <p>Pantau ringkasan operasional dan informasi sistem melalui satu halaman.</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div role="status" style="margin-bottom:18px;padding:12px 15px;border:1px solid #a7f3d0;border-radius:10px;color:#047857;background:#ecfdf5;font-size:13px;">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div role="alert" style="margin-bottom:18px;padding:12px 15px;border:1px solid #fecaca;border-radius:10px;color:#b91c1c;background:#fef2f2;font-size:13px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="stats-grid">

        <a class="stat-card stat-card-link" href="{{ route('locations.index') }}" aria-label="Lihat daftar stasiun SPKLU">
            <div class="stat-icon icon-green">🏢</div>
            <div>
                <div class="stat-label">Total Stasiun</div>
                <div class="stat-number" id="admin-total-stations">{{ $stats['locations'] }}</div>
                <div class="stat-note">Klik untuk melihat stasiun →</div>
            </div>
        </a>

        <a class="stat-card stat-card-link" href="{{ route('locations.index') }}" aria-label="Lihat stasiun dan unit charger">
            <div class="stat-icon icon-blue">🔌</div>
            <div>
                <div class="stat-label">Total Unit Charger</div>
                <div class="stat-number" id="admin-total-chargers">{{ $stats['chargers'] }}</div>
                <div class="stat-note">Klik untuk melihat unit charger →</div>
            </div>
        </a>

        <a class="stat-card stat-card-link" href="{{ route('locations.index') }}" aria-label="Lihat ketersediaan charger di stasiun">
            <div class="stat-icon icon-green">⚡</div>
            <div>
                <div class="stat-label">Charger Online</div>
                <div class="stat-number" id="admin-online-chargers">{{ $stats['online_chargers'] }}</div>
                <div class="stat-note">{{ $chargerStatuses[0]['percentage'] }}% online · Klik untuk detail →</div>
            </div>
        </a>

        <button
            class="stat-card stat-card-button"
            id="admin-attention-toggle"
            type="button"
            aria-expanded="false"
            aria-controls="admin-inspection-reports"
        >
            <span class="stat-icon icon-orange" aria-hidden="true">⚠️</span>
            <span>
                <span class="stat-label" style="display:block;">Perlu Perhatian</span>
                <span class="stat-number" id="admin-attention-chargers" style="display:block;color:#ea580c;">{{ $stats['attention_chargers'] }}</span>
                <span class="stat-note" style="display:block;">Klik untuk melihat laporan</span>
            </span>
        </button>

    </div>

    <section class="dashboard-card inspection-panel" id="admin-inspection-reports" aria-labelledby="inspection-reports-heading" hidden>
        <div class="card-heading">
            <div class="heading-icon" style="background:#fff7ed;">🛠️</div>
            <div>
                <h3 id="inspection-reports-heading">Laporan Pemeriksaan Charger</h3>
                <p>Laporan operator tersimpan bersama data charger dan dapat diperbarui di sini.</p>
            </div>
        </div>

        <div class="inspection-list">
            @forelse($inspectionReports as $report)
                <article class="inspection-report">
                    <div class="inspection-report-main">
                        <h4 class="inspection-report-title">{{ $report->device_number }}</h4>
                        <div class="inspection-report-meta">
                            {{ $report->location?->nama_lokasi ?? 'Lokasi tidak tersedia' }}
                            · Status charger: {{ ucfirst($report->status) }}{{ $report->is_online ? ' · Online' : ' · Offline' }}
                        </div>
                        <p class="inspection-report-note">{{ $report->inspection_note }}</p>
                        <div class="inspection-report-time">
                            Dilaporkan oleh {{ $report->inspectionReporter?->nama ?? 'Operator' }}
                            @if($report->inspection_reported_at)
                                · {{ $report->inspection_reported_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                    <div class="inspection-report-form">
                        <form action="{{ route('admin.chargers.inspection.update', $report->id_charger) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="needs_inspection" value="1">
                            <label for="inspection-note-{{ $report->id_charger }}">Perbarui catatan</label>
                            <textarea id="inspection-note-{{ $report->id_charger }}" name="inspection_note" maxlength="1000" required>{{ $report->inspection_note }}</textarea>
                            <div class="inspection-report-actions">
                                <button class="inspection-report-button" type="submit">Simpan perubahan</button>
                            </div>
                        </form>
                        <form action="{{ route('admin.chargers.inspection.update', $report->id_charger) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="needs_inspection" value="0">
                            <button class="inspection-report-button resolve" type="submit">Tandai selesai</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="inspection-empty">Tidak ada laporan pemeriksaan yang menunggu tindak lanjut.</div>
            @endforelse
        </div>
    </section>

    {{-- Status Operasional dan Ringkasan Sistem --}}
    <div class="dashboard-grid">

        <div class="dashboard-card">
            <div class="card-heading">
                <div class="heading-icon">📊</div>
                <div>
                    <h3>Status Operasional Charger</h3>
                    <p>Status terkini unit charger yang terdaftar.</p>
                </div>
            </div>

            @foreach ($chargerStatuses as $status)
                <div class="status-item">
                    <div class="status-info">
                        <span class="status-name">
                            <span class="status-dot dot-{{ $status['color'] }}"></span>
                            {{ $status['label'] }}
                        </span>
                        <span class="status-count">{{ $status['count'] }} unit · {{ $status['percentage'] }}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill fill-{{ $status['color'] }}" style="width: {{ $status['percentage'] }}%;"></div>
                    </div>
                </div>
            @endforeach

            @if ($stats['chargers'] === 0)
                <p style="margin: 18px 0 0; color: #718096; font-size: 12px;">Belum ada data charger.</p>
            @endif
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
                    <span class="system-value">{{ ucfirst(app()->environment()) }}</span>
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
                    <p>Sesi charging terbaru yang tercatat di sistem.</p>
                </div>
            </div>

            <div class="activity-list">

                @forelse ($recentSessions as $session)
                    @php
                        $statusLabel = match ($session->status) {
                            'ongoing' => 'Sedang berlangsung',
                            'paid' => 'Selesai dan dibayar',
                            'completed' => 'Menunggu pembayaran',
                            default => ucfirst($session->status),
                        };
                    @endphp
                    <div class="activity-item">
                        <div class="activity-icon" style="background:#dbeafe;">🔌</div>
                        <div class="activity-text">
                            <h4>Sesi #{{ $session->id }} · {{ $session->user?->nama ?? 'Pengguna' }}</h4>
                            <p>{{ $session->charger?->device_number ?? 'Charger' }} · {{ $session->charger?->location?->nama_lokasi ?? 'Lokasi tidak tersedia' }}<br>{{ $statusLabel }}</p>
                        </div>
                        <div class="activity-time">{{ $session->start_time?->timezone('Asia/Jakarta')->format('d/m H:i') ?? '-' }}</div>
                    </div>
                @empty
                    <p style="margin: 0; color: #718096; font-size: 12px;">Belum ada aktivitas charging.</p>
                @endforelse

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
                    💡 Pengelolaan stasiun dan unit charger ditangani oleh operator.
                    Periksa menu Notifikasi untuk melihat notifikasi sistem.
                </div>
            </div>
        </div>

    </div>

    <div class="dashboard-footer">
        EV Charge · Panel Administrator
    </div>

</div>

<script>
    (() => {
        const toggle = document.getElementById('admin-attention-toggle');
        const panel = document.getElementById('admin-inspection-reports');
        if (!toggle || !panel) return;

        toggle.addEventListener('click', () => {
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!isExpanded));
            panel.hidden = isExpanded;

            if (!isExpanded) {
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    })();
</script>
@endsection
