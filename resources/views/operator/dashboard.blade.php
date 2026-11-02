@extends('layouts.app')

@section('title', 'Dashboard Operator')

@section('content')
<style>
    .operator-dashboard { width: min(1200px, 100%); margin: 0 auto; padding: 34px 24px 56px; color: #172554; }
    .operator-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; margin-bottom: 24px; }
    .operator-heading h1 { margin: 0 0 8px; color: #0f172a; font-size: clamp(25px, 4vw, 32px); }
    .operator-heading p { margin: 0; color: #64748b; line-height: 1.6; }
    .heading-date { flex: 0 0 auto; padding: 10px 14px; color: #475569; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px; }
    .welcome-card { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 22px; padding: 26px 28px; color: #fff; background: linear-gradient(115deg, #047857, #10b981); border-radius: 18px; box-shadow: 0 12px 28px rgba(5, 150, 105, .17); }
    .welcome-card h2 { margin: 0 0 7px; font-size: 21px; }
    .welcome-card p { margin: 0; color: rgba(255,255,255,.86); font-size: 14px; line-height: 1.6; }
    .welcome-icon { display: grid; flex: 0 0 54px; width: 54px; height: 54px; place-items: center; color: #047857; background: rgba(255,255,255,.92); border-radius: 17px; font-size: 27px; }
    .welcome-copy { flex: 1; }
    .quick-link { flex: 0 0 auto; padding: 12px 16px; color: #047857; background: #fff; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .2s, background .2s; }
    .quick-link:hover { transform: translateY(-2px); background: #ecfdf5; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 15px; margin-bottom: 22px; }
    .stat-card { display: flex; align-items: center; gap: 14px; min-width: 0; padding: 19px; background: #fff; border: 1px solid #e2e8f0; border-radius: 15px; box-shadow: 0 5px 16px rgba(15, 23, 42, .035); }
    .stat-card-link { color: inherit; text-decoration: none; transition: border-color .2s, box-shadow .2s, transform .2s; }
    .stat-card-link:hover { border-color: #6ee7b7; box-shadow: 0 10px 24px rgba(5, 150, 105, .11); transform: translateY(-2px); }
    .stat-card-link:active { transform: translateY(0); }
    .stat-card-link:focus-visible { outline: 3px solid rgba(16, 185, 129, .3); outline-offset: 3px; }
    .stat-card-link .stat-note { color: #047857; font-weight: 650; }
    .stat-icon { display: grid; flex: 0 0 46px; width: 46px; height: 46px; place-items: center; border-radius: 14px; font-size: 21px; }
    .stat-icon.green { color: #047857; background: #d1fae5; }
    .stat-icon.blue { color: #1d4ed8; background: #dbeafe; }
    .stat-icon.amber { color: #b45309; background: #fef3c7; }
    .stat-icon.red { color: #b91c1c; background: #fee2e2; }
    .stat-label { margin-bottom: 5px; color: #64748b; font-size: 12px; }
    .stat-value { color: #0f172a; font-size: 24px; font-weight: 750; line-height: 1.15; }
    .stat-note { margin-top: 5px; color: #94a3b8; font-size: 11px; }
    .live-status { display: flex; align-items: center; gap: 7px; margin-top: 8px; color: #64748b; font-size: 11px; }
    .live-indicator { width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 3px #d1fae5; }
    .live-status.error { color: #b91c1c; }
    .live-status.error .live-indicator { background: #ef4444; box-shadow: 0 0 0 3px #fee2e2; }
    .dashboard-grid { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(300px, .85fr); gap: 18px; align-items: start; }
    .panel { min-width: 0; overflow: hidden; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 6px 20px rgba(15, 23, 42, .04); }
    .panel-heading { display: flex; justify-content: space-between; align-items: center; gap: 14px; padding: 19px 21px; border-bottom: 1px solid #eef2f7; }
    .panel-heading h2 { margin: 0; color: #0f172a; font-size: 16px; }
    .panel-heading p { margin: 5px 0 0; color: #94a3b8; font-size: 12px; }
    .panel-link { flex: 0 0 auto; color: #047857; font-size: 12px; font-weight: 700; text-decoration: none; }
    .panel-link:hover { text-decoration: underline; }
    .table-wrap { overflow-x: auto; }
    .operator-table { width: 100%; border-collapse: collapse; text-align: left; }
    .operator-table th { padding: 12px 16px; color: #64748b; background: #f8fafc; font-size: 11px; font-weight: 650; white-space: nowrap; }
    .operator-table td { padding: 14px 16px; color: #334155; border-top: 1px solid #f1f5f9; font-size: 12px; vertical-align: middle; }
    .charger-name { color: #0f172a; font-size: 13px; font-weight: 700; }
    .charger-location { margin-top: 4px; color: #94a3b8; font-size: 11px; }
    .status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 9px; color: #047857; background: #d1fae5; border-radius: 999px; font-size: 10px; font-weight: 700; white-space: nowrap; }
    .status-badge.busy { color: #1d4ed8; background: #dbeafe; }
    .status-badge.offline { color: #b91c1c; background: #fee2e2; }
    .status-badge.warning { color: #b45309; background: #fef3c7; }
    .tariff-form { display: flex; align-items: center; gap: 6px; min-width: 150px; }
    .tariff-form input { width: 95px; min-width: 0; padding: 7px 8px; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; }
    .tariff-form button { padding: 7px 9px; color: #fff; background: #059669; border: 0; border-radius: 8px; font: inherit; font-weight: 700; cursor: pointer; }
    .tariff-form button:hover { background: #047857; }
    .recent-list { padding: 4px 20px 10px; }
    .recent-item { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid #f1f5f9; }
    .recent-item:last-child { border-bottom: 0; }
    .recent-icon { display: grid; flex: 0 0 34px; width: 34px; height: 34px; place-items: center; color: #047857; background: #ecfdf5; border-radius: 11px; }
    .recent-content { flex: 1; min-width: 0; }
    .recent-title { overflow: hidden; color: #1e293b; font-size: 12px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
    .recent-meta { margin-top: 5px; color: #64748b; font-size: 11px; line-height: 1.5; }
    .empty-state { padding: 28px 20px; color: #94a3b8; text-align: center; font-size: 13px; }
    .alert { margin-bottom: 18px; padding: 12px 15px; border-radius: 10px; font-size: 13px; }
    .alert.success { color: #047857; background: #d1fae5; }
    .alert.error { color: #b91c1c; background: #fee2e2; }
    .attention-note { margin: 16px 20px 20px; padding: 13px 14px; color: #92400e; background: #fffbeb; border: 1px solid #fde68a; border-radius: 11px; font-size: 12px; line-height: 1.5; }
    .inspection-flag { display: inline-flex; margin-top: 7px; padding: 4px 8px; color: #b45309; background: #fef3c7; border-radius: 999px; font-size: 10px; font-weight: 700; }
    .inspection-note { max-width: 220px; margin-top: 5px; color: #92400e; font-size: 10px; line-height: 1.45; }
    .inspection-reported { margin-top: 4px; color: #94a3b8; font-size: 10px; }
    .inspection-controls { min-width: 170px; margin-top: 9px; }
    .inspection-controls summary { color: #047857; font-size: 11px; font-weight: 700; cursor: pointer; }
    .inspection-form { display: grid; gap: 7px; margin-top: 8px; }
    .inspection-form textarea { width: 100%; min-height: 54px; padding: 7px 8px; box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 7px; font: inherit; font-size: 11px; resize: vertical; }
    .inspection-form button { justify-self: start; padding: 7px 9px; color: #fff; background: #b45309; border: 0; border-radius: 7px; font: inherit; font-size: 10px; font-weight: 700; cursor: pointer; }
    .inspection-form button:hover { background: #92400e; }
    .inspection-clear-form { margin-top: 6px; }
    .inspection-clear-form button { padding: 5px 8px; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 7px; font: inherit; font-size: 10px; font-weight: 700; cursor: pointer; }
    .inspection-clear-form button:hover { background: #d1fae5; }
    @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .dashboard-grid { grid-template-columns: 1fr; } }
    @media (max-width: 600px) { .operator-dashboard { padding: 24px 14px 40px; } .operator-heading { align-items: flex-start; flex-direction: column; } .heading-date { align-self: flex-start; } .welcome-card { align-items: flex-start; flex-wrap: wrap; padding: 21px; } .welcome-copy { flex-basis: calc(100% - 74px); } .quick-link { margin-left: 68px; } .stats-grid { gap: 10px; } .stat-card { align-items: flex-start; flex-direction: column; gap: 10px; padding: 14px; } .stat-value { font-size: 21px; } .panel-heading { padding: 16px; } }
</style>

<div class="operator-dashboard">
    <header class="operator-heading">
        <div>
            <h1>Dashboard Operator</h1>
            <p>Pantau operasional stasiun, kesiapan charger, dan sesi pengisian EV.</p>
        </div>
        <time
            class="heading-date"
            id="operator-clock"
            data-timezone="Asia/Jakarta"
            data-timestamp="{{ now()->timestamp * 1000 }}"
            aria-label="Tanggal dan waktu"
        >{{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</time>
    </header>
    <div class="live-status" id="operator-live-status" role="status" aria-live="polite">
        <span class="live-indicator" aria-hidden="true"></span>
        <span>Data diperbarui otomatis</span>
        <span id="operator-live-updated"></span>
    </div>

    @if (session('success'))
        <div class="alert success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert error" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="welcome-card">
        <div class="welcome-icon" aria-hidden="true">⚡</div>
        <div class="welcome-copy">
            <h2>Selamat bertugas, {{ auth()->user()->nama }}.</h2>
            <p>Kelola ketersediaan unit, pantau aktivitas pengisian, dan pastikan tarif setiap charger selalu sesuai.</p>
        </div>
        <a class="quick-link" href="{{ route('operator.stations.index') }}">Kelola stasiun & charger <span aria-hidden="true">→</span></a>
    </section>

    <section class="stats-grid" aria-label="Ringkasan operasional">
        <a class="stat-card stat-card-link" href="{{ route('operator.stations.index') }}" aria-label="Kelola stasiun aktif">
            <div class="stat-icon green" aria-hidden="true">🏢</div>
            <div><div class="stat-label">Stasiun aktif</div><div class="stat-value">{{ $stats['active_locations'] }}<span style="color:#94a3b8;font-size:15px;font-weight:500;"> / {{ $stats['locations'] }}</span></div><div class="stat-note">Kelola stasiun →</div></div>
        </a>
        <a class="stat-card stat-card-link" href="{{ route('operator.stations.index') }}" aria-label="Kelola ketersediaan charger">
            <div class="stat-icon blue" aria-hidden="true">🔌</div>
            <div><div class="stat-label">Charger tersedia</div><div class="stat-value">{{ $stats['available_chargers'] }}<span style="color:#94a3b8;font-size:15px;font-weight:500;"> / {{ $stats['chargers'] }}</span></div><div class="stat-note">Lihat dan kelola charger →</div></div>
        </a>
        <a class="stat-card stat-card-link" href="#recent-activity" aria-label="Lihat sesi charging terbaru">
            <div class="stat-icon amber" aria-hidden="true">⚡</div>
            <div><div class="stat-label">Sesi berlangsung</div><div class="stat-value" data-live-stat="ongoing_sessions">{{ $stats['ongoing_sessions'] }}</div><div class="stat-note"><span data-live-stat="sessions_today">{{ $stats['sessions_today'] }}</span> sesi hari ini · Lihat aktivitas →</div></div>
        </a>
        <a class="stat-card stat-card-link" href="#charger-status" data-open-inspection aria-label="Lihat laporan dan kontrol pemeriksaan charger">
            <div class="stat-icon {{ $stats['attention_chargers'] ? 'red' : 'green' }}" aria-hidden="true" data-inspection-icon>{{ $stats['attention_chargers'] ? '⚠️' : '✓' }}</div>
            <div><div class="stat-label">Perlu pemeriksaan</div><div class="stat-value" data-live-stat="attention_chargers">{{ $stats['attention_chargers'] }}</div><div class="stat-note">Buka laporan pemeriksaan →</div></div>
        </a>
    </section>

    <section class="dashboard-grid">
        <article class="panel" id="charger-status" tabindex="-1">
            <header class="panel-heading">
                <div><h2>Status charger & tarif</h2><p>Pantau kesiapan unit dan perbarui tarif per kWh.</p></div>
                <a class="panel-link" href="{{ route('operator.stations.index') }}">Semua stasiun →</a>
            </header>

            <div class="table-wrap">
                <table class="operator-table">
                    <thead>
                        <tr><th>Charger / lokasi</th><th>Status</th><th>Tarif & pemeriksaan</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($chargers as $charger)
                            @php
                                $chargerStatus = !$charger->is_online || $charger->status === 'rusak'
                                    ? 'offline'
                                    : ($charger->status === 'digunakan' ? 'busy' : 'available');
                                $chargerLabel = !$charger->is_online
                                    ? 'Offline'
                                    : match ($charger->status) {
                                        'rusak' => 'Rusak',
                                        'digunakan' => 'Digunakan',
                                        default => 'Tersedia',
                                    };
                            @endphp
                            <tr>
                                <td>
                                    <div class="charger-name">{{ $charger->device_number }}</div>
                                    <div class="charger-location">{{ $charger->location?->nama_lokasi ?? 'Lokasi tidak tersedia' }} · {{ $charger->connector_type }}</div>
                                </td>
                                <td>
                                    <span class="status-badge {{ $chargerStatus }}">{{ $chargerLabel }}</span>
                                    <div data-inspection-display="{{ $charger->id_charger }}" @if(!$charger->needs_inspection) hidden @endif>
                                        <div><span class="inspection-flag">Perlu pemeriksaan</span></div>
                                        <div class="inspection-note" data-inspection-note>{{ $charger->inspection_note }}</div>
                                        <div class="inspection-reported" data-inspection-reported @if(!$charger->inspection_reported_at) hidden @endif>
                                            @if($charger->inspection_reported_at)
                                                Dilaporkan {{ $charger->inspection_reported_at->timezone('Asia/Jakarta')->diffForHumans() }}{{ $charger->inspectionReporter ? ' oleh ' . $charger->inspectionReporter->nama : '' }}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form class="tariff-form" action="{{ route('operator.chargers.tariff.update', $charger->id_charger) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input aria-label="Tarif per kWh {{ $charger->device_number }}" type="number" name="price_per_kwh" min="1" max="99999999.99" step="1" value="{{ (float) $charger->price_per_kwh }}" required>
                                        <button type="submit">Simpan</button>
                                    </form>
                                    <details class="inspection-controls" data-inspection-controls="{{ $charger->id_charger }}">
                                        <summary data-inspection-summary>{{ $charger->needs_inspection ? 'Perbarui catatan pemeriksaan' : 'Laporkan perlu pemeriksaan' }}</summary>
                                        <form class="inspection-form" action="{{ route('operator.chargers.inspection.update', $charger->id_charger) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="needs_inspection" value="1">
                                            <textarea data-inspection-textarea name="inspection_note" maxlength="1000" placeholder="Jelaskan masalah yang perlu diperiksa" required>{{ $charger->inspection_note }}</textarea>
                                            <button type="submit">{{ $charger->needs_inspection ? 'Simpan catatan' : 'Tandai untuk diperiksa' }}</button>
                                        </form>
                                        <form class="inspection-clear-form" data-inspection-clear-form @if(!$charger->needs_inspection) hidden @endif action="{{ route('operator.chargers.inspection.update', $charger->id_charger) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="needs_inspection" value="0">
                                            <button type="submit">Tandai pemeriksaan selesai</button>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">Belum ada unit charger. Tambahkan charger dari menu Kelola SPKLU.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($stats['attention_chargers'] > 0)
                <div class="attention-note">Ada {{ $stats['attention_chargers'] }} charger yang dilaporkan operator dan perlu ditindaklanjuti. Perbarui catatan atau tandai selesai dari daftar charger.</div>
            @else
                <div class="attention-note" style="color:#047857;background:#ecfdf5;border-color:#a7f3d0;">Belum ada charger yang ditandai perlu pemeriksaan. Tandai unit dari daftar jika ditemukan kendala.</div>
            @endif
        </article>

        <article class="panel" id="recent-activity" tabindex="-1">
            <header class="panel-heading">
                <div><h2>Aktivitas charging terbaru</h2><p>Sesi terbaru di seluruh stasiun.</p></div>
                <span class="panel-link">{{ $recentSessions->count() }} sesi</span>
            </header>
            <div class="recent-list">
                @forelse ($recentSessions as $session)
                    @php
                        $sessionStatus = match ($session->status) {
                            'ongoing' => ['Sedang mengisi', 'busy'],
                            'paid' => ['Selesai · Lunas', 'available'],
                            'completed' => ['Selesai · Belum dibayar', 'warning'],
                            default => [ucfirst($session->status), 'offline'],
                        };
                    @endphp
                    <div class="recent-item">
                        <div class="recent-icon" aria-hidden="true">{{ $session->status === 'ongoing' ? '⚡' : '🔋' }}</div>
                        <div class="recent-content">
                            <div class="recent-title">{{ $session->charger?->device_number ?? 'Charger' }} · {{ $session->user?->nama ?? 'Pengguna' }}</div>
                            <div class="recent-meta">
                                {{ $session->charger?->location?->nama_lokasi ?? 'Lokasi tidak tersedia' }}<br>
                                {{ $session->start_time?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-' }} WIB
                                · <span class="status-badge {{ $sessionStatus[1] }}">{{ $sessionStatus[0] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada aktivitas charging.</div>
                @endforelse
            </div>
        </article>
    </section>
</div>
<script>
    (() => {
        const liveStatus = document.getElementById('operator-live-status');
        const updatedAt = document.getElementById('operator-live-updated');
        const liveUrl = @json(route('operator.dashboard.live'));

        document.querySelectorAll('a[href^="#"]').forEach((link) => {
            link.addEventListener('click', () => {
                if (!link.hasAttribute('data-open-inspection')) return;

                const reportedControl = document.querySelector('[data-inspection-display]:not([hidden])')
                    ?.closest('tr')
                    ?.querySelector('.inspection-controls');
                if (reportedControl) reportedControl.open = true;
            });
        });

        const refreshLiveStats = async () => {
            try {
                const response = await fetch(liveUrl, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                    credentials: 'same-origin',
                });
                if (!response.ok) throw new Error('Status server ' + response.status);

                const payload = await response.json();
                document.querySelectorAll('[data-live-stat]').forEach((element) => {
                    const value = payload.stats[element.dataset.liveStat];
                    if (value !== undefined) element.textContent = value;
                });

                payload.inspection_reports.forEach((report) => {
                    const display = document.querySelector(`[data-inspection-display="${report.id}"]`);
                    const controls = document.querySelector(`[data-inspection-controls="${report.id}"]`);
                    if (!display || !controls) return;

                    display.hidden = !report.needs_inspection;
                    display.querySelector('[data-inspection-note]').textContent = report.note || '';
                    const reported = display.querySelector('[data-inspection-reported]');
                    reported.hidden = !report.needs_inspection || !report.reported_at;
                    reported.textContent = report.reported_at
                        ? `Dilaporkan ${report.reported_at}${report.reporter ? ' oleh ' + report.reporter : ''}`
                        : '';

                    controls.querySelector('[data-inspection-summary]').textContent = report.needs_inspection
                        ? 'Perbarui catatan pemeriksaan'
                        : 'Laporkan perlu pemeriksaan';
                    controls.querySelector('[data-inspection-textarea]').value = report.note || '';
                    controls.querySelector('[data-inspection-clear-form]').hidden = !report.needs_inspection;
                });

                const inspectionIcon = document.querySelector('[data-inspection-icon]');
                const needsAttention = Number(payload.stats.attention_chargers) > 0;
                if (inspectionIcon) {
                    inspectionIcon.textContent = needsAttention ? '⚠️' : '✓';
                    inspectionIcon.classList.toggle('red', needsAttention);
                    inspectionIcon.classList.toggle('green', !needsAttention);
                }

                liveStatus.classList.remove('error');
                liveStatus.querySelector('span:last-child').textContent = 'Diperbarui ' +
                    new Intl.DateTimeFormat('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        timeZone: 'Asia/Jakarta',
                        hourCycle: 'h23',
                    }).format(new Date(payload.updated_at));
            } catch (error) {
                liveStatus.classList.add('error');
                liveStatus.querySelector('span:last-child').textContent = 'Koneksi terputus; mencoba kembali';
            }
        };

        refreshLiveStats();
        window.setInterval(refreshLiveStats, 15000);

        const clock = document.getElementById('operator-clock');
        if (!clock) return;

        const formatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: clock.dataset.timezone,
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hourCycle: 'h23',
        });

        const renderClock = () => {
            clock.textContent = formatter.format(new Date());
        };

        renderClock();
        window.setInterval(renderClock, 1000);
    })();
</script>
@endsection
