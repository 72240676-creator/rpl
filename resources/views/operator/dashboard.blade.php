@extends('layouts.app')

@section('title', 'Dashboard Operator')

@section('content')
<style>
    .operator-dashboard { width: min(1280px, 100%); margin: 0 auto; padding: 38px 28px 64px; color: #172554; }
    .operator-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; margin-bottom: 20px; }
    .heading-eyebrow { display: inline-flex; align-items: center; gap: 7px; margin-bottom: 9px; color: #047857; font-size: 10px; font-weight: 800; letter-spacing: 1.2px; text-transform: uppercase; }
    .heading-eyebrow::before { width: 7px; height: 7px; border-radius: 50%; background: #10b981; content: ""; }
    .operator-heading h1 { margin: 0 0 7px; color: #0f172a; font-size: clamp(27px, 4vw, 35px); letter-spacing: -.8px; }
    .operator-heading p { margin: 0; color: #64748b; line-height: 1.6; }
    .heading-date { flex: 0 0 auto; padding: 11px 15px; color: #475569; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(15, 23, 42, .03); font-size: 12px; }
    .live-status { display: inline-flex; align-items: center; gap: 8px; margin: 13px 0 19px; padding: 7px 10px; color: #475569; background: #fff; border: 1px solid #e2e8f0; border-radius: 999px; box-shadow: 0 3px 10px rgba(15, 23, 42, .025); font-size: 10px; }
    .live-indicator { width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 3px #d1fae5; }
    .live-status.error { color: #b91c1c; }
    .live-status.error .live-indicator { background: #ef4444; box-shadow: 0 0 0 3px #fee2e2; }
    .welcome-card { position: relative; display: flex; justify-content: space-between; align-items: center; gap: 24px; overflow: hidden; margin-bottom: 22px; padding: 28px 30px; color: #fff; background: radial-gradient(ellipse at 82% 15%, rgba(52, 211, 153, .38), transparent 34%), linear-gradient(115deg, #065f46, #059669 65%, #10b981); border: 1px solid rgba(5, 150, 105, .25); border-radius: 20px; box-shadow: 0 14px 32px rgba(5, 150, 105, .16); }
    .welcome-card::after { position: absolute; right: 190px; bottom: -88px; width: 220px; height: 220px; border: 1px solid rgba(255,255,255,.13); border-radius: 50%; box-shadow: 0 0 0 24px rgba(255,255,255,.035), 0 0 0 48px rgba(255,255,255,.025); content: ""; pointer-events: none; }
    .welcome-icon { display: grid; flex: 0 0 58px; width: 58px; height: 58px; place-items: center; color: #047857; background: rgba(255,255,255,.95); border: 1px solid rgba(255,255,255,.55); border-radius: 18px; box-shadow: 0 8px 18px rgba(6, 78, 59, .15); font-size: 28px; }
    .welcome-copy { position: relative; z-index: 1; flex: 1; }
    .welcome-kicker { margin-bottom: 7px; color: rgba(209, 250, 229, .9); font-size: 10px; font-weight: 800; letter-spacing: 1.25px; text-transform: uppercase; }
    .welcome-card h2 { margin: 0 0 7px; font-size: 22px; letter-spacing: -.25px; }
    .welcome-card p { margin: 0; color: rgba(255,255,255,.86); font-size: 14px; line-height: 1.6; }
    .quick-link { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 8px; flex: 0 0 auto; padding: 12px 16px; color: #047857; background: #fff; border: 1px solid rgba(255,255,255,.65); border-radius: 10px; box-shadow: 0 5px 12px rgba(6, 78, 59, .12); font-size: 13px; font-weight: 750; text-decoration: none; transition: transform .2s, background .2s, box-shadow .2s; }
    .quick-link:hover { transform: translateY(-2px); background: #ecfdf5; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 30px; }
    .stat-card { display: flex; align-items: center; gap: 15px; min-width: 0; min-height: 102px; padding: 18px 19px; background: linear-gradient(145deg, #fff, #fcfefd); border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 5px 18px rgba(15, 23, 42, .035); }
    .stat-icon { display: grid; flex: 0 0 46px; width: 46px; height: 46px; place-items: center; border-radius: 14px; font-size: 20px; }
    .stat-icon.green { color: #047857; background: #d1fae5; }
    .stat-icon.blue { color: #1d4ed8; background: #dbeafe; }
    .stat-icon.amber { color: #b45309; background: #fef3c7; }
    .stat-icon.red { color: #b91c1c; background: #fee2e2; }
    .stat-label { margin-bottom: 5px; color: #64748b; font-size: 11px; }
    .stat-value { color: #0f172a; font-size: 24px; font-weight: 750; line-height: 1.15; }
    .stat-note { margin-top: 5px; color: #94a3b8; font-size: 10px; }
    .section-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 15px; margin-bottom: 13px; }
    .section-heading h2 { margin: 0; color: #0f172a; font-size: 17px; letter-spacing: -.2px; }
    .section-heading p { margin: 5px 0 0; color: #64748b; font-size: 12px; }
    .destination-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
    .destination-card { position: relative; display: flex; flex-direction: column; min-height: 205px; padding: 20px; overflow: hidden; color: inherit; background: #fff; border: 1px solid #e2e8f0; border-radius: 17px; box-shadow: 0 5px 18px rgba(15, 23, 42, .035); text-decoration: none; transition: transform .2s, border-color .2s, box-shadow .2s; }
    .destination-card:hover { transform: translateY(-3px); border-color: #6ee7b7; box-shadow: 0 14px 28px rgba(5, 150, 105, .10); }
    .destination-card:focus-visible { outline: 3px solid rgba(16, 185, 129, .3); outline-offset: 3px; }
    .destination-card::after { position: absolute; top: -34px; right: -34px; width: 100px; height: 100px; background: #f0fdf4; border-radius: 50%; content: ""; transition: transform .2s; }
    .destination-card:hover::after { transform: scale(1.2); }
    .destination-top, .destination-content, .destination-footer { position: relative; z-index: 1; }
    .destination-top { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; }
    .destination-icon { display: grid; width: 42px; height: 42px; place-items: center; color: #047857; background: #ecfdf5; border: 1px solid #d1fae5; border-radius: 13px; font-size: 19px; }
    .destination-tag { padding: 5px 8px; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 999px; font-size: 9px; font-weight: 750; white-space: nowrap; }
    .destination-tag.warning { color: #b45309; background: #fffbeb; border-color: #fde68a; }
    .destination-tag.safe { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
    .destination-card h3 { margin: 0 0 6px; color: #0f172a; font-size: 15px; }
    .destination-card p { margin: 0; color: #64748b; font-size: 11px; line-height: 1.6; }
    .destination-footer { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: auto; padding-top: 18px; }
    .destination-metric { color: #047857; font-size: 11px; font-weight: 700; }
    .destination-action { display: inline-flex; align-items: center; gap: 6px; color: #047857; font-size: 11px; font-weight: 750; }
    .alert { margin-bottom: 16px; padding: 13px 16px; border: 1px solid transparent; border-radius: 12px; font-size: 13px; }
    .alert.success { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
    .alert.error { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }
    @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .destination-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 600px) { .operator-dashboard { padding: 25px 14px 40px; } .operator-heading { align-items: flex-start; flex-direction: column; gap: 13px; } .welcome-card { align-items: flex-start; flex-wrap: wrap; gap: 14px; padding: 21px; } .welcome-icon { flex-basis: 46px; width: 46px; height: 46px; border-radius: 14px; } .welcome-copy { flex-basis: calc(100% - 62px); } .welcome-card h2 { font-size: 18px; } .welcome-card p { font-size: 12px; } .quick-link { margin-left: 60px; padding: 10px 13px; } .stats-grid { gap: 10px; margin-bottom: 24px; } .stat-card { align-items: flex-start; flex-direction: column; gap: 10px; min-height: 0; padding: 14px; } .stat-value { font-size: 21px; } .stat-icon { flex-basis: 40px; width: 40px; height: 40px; } .destination-grid { grid-template-columns: 1fr; gap: 11px; } .destination-card { min-height: 175px; padding: 17px; } .section-heading { align-items: flex-start; flex-direction: column; } }
</style>

<div class="operator-dashboard">
    <header class="operator-heading">
        <div>
            <div class="heading-eyebrow">Pusat kendali operasional</div>
            <h1>Dashboard Operator</h1>
            <p>Pantau stasiun, kesiapan charger, dan aktivitas pengisian EV dalam satu tempat.</p>
        </div>
        <time
            class="heading-date"
            id="operator-clock"
            data-timezone="Asia/Jakarta"
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
            <div class="welcome-kicker">EV Charge · Operator</div>
            <h2>Selamat bertugas, {{ auth()->user()->nama }}.</h2>
            <p>Kelola ketersediaan stasiun, pantau pengisian aktif, dan tindak lanjuti laporan pemeriksaan.</p>
        </div>
        <a class="quick-link" href="{{ route('operator.stations.index') }}">Kelola SPKLU <span aria-hidden="true">→</span></a>
    </section>

    <section class="stats-grid" aria-label="Ringkasan operasional">
        <div class="stat-card">
            <div class="stat-icon green" aria-hidden="true">🏢</div>
            <div>
                <div class="stat-label">Stasiun aktif</div>
                <div class="stat-value">{{ $stats['active_locations'] }}<span style="color:#94a3b8;font-size:15px;font-weight:500;"> / {{ $stats['locations'] }}</span></div>
                <div class="stat-note">Lokasi berstatus operasional</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue" aria-hidden="true">🔌</div>
            <div>
                <div class="stat-label">Charger tersedia</div>
                <div class="stat-value">{{ $stats['available_chargers'] }}<span style="color:#94a3b8;font-size:15px;font-weight:500;"> / {{ $stats['chargers'] }}</span></div>
                <div class="stat-note">Siap digunakan pengemudi</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber" aria-hidden="true">⚡</div>
            <div>
                <div class="stat-label">Sesi berlangsung</div>
                <div class="stat-value" data-live-stat="ongoing_sessions">{{ $stats['ongoing_sessions'] }}</div>
                <div class="stat-note"><span data-live-stat="sessions_today">{{ $stats['sessions_today'] }}</span> sesi dimulai hari ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $stats['attention_chargers'] ? 'red' : 'green' }}" aria-hidden="true" data-inspection-icon>{{ $stats['attention_chargers'] ? '!' : '✓' }}</div>
            <div>
                <div class="stat-label">Perlu pemeriksaan</div>
                <div class="stat-value" data-live-stat="attention_chargers">{{ $stats['attention_chargers'] }}</div>
                <div class="stat-note">{{ $stats['attention_chargers'] ? 'Laporan menunggu tindak lanjut' : 'Tidak ada laporan tertunda' }}</div>
            </div>
        </div>
    </section>

    <section aria-labelledby="operator-menu-title">
        <div class="section-heading">
            <div>
                <h2 id="operator-menu-title">Menu operasional</h2>
                <p>Pilih area kerja untuk membuka data dan tindakan terkait.</p>
            </div>
        </div>

        <div class="destination-grid">
            <a class="destination-card" href="{{ route('operator.stations.index') }}">
                <div class="destination-top">
                    <span class="destination-icon" aria-hidden="true">🔌</span>
                    <span class="destination-tag">Stasiun & charger</span>
                </div>
                <div class="destination-content">
                    <h3>SPKLU & tarif charger</h3>
                    <p>Kelola lokasi, pantau status unit, dan perbarui tarif pengisian.</p>
                </div>
                <div class="destination-footer">
                    <span class="destination-metric">{{ $stats['chargers'] }} unit charger · {{ $stats['locations'] }} lokasi</span>
                    <span class="destination-action">Buka menu <span aria-hidden="true">→</span></span>
                </div>
            </a>

            <a class="destination-card" href="{{ route('operator.activity') }}">
                <div class="destination-top">
                    <span class="destination-icon" aria-hidden="true">⚡</span>
                    <span class="destination-tag">Aktivitas terbaru</span>
                </div>
                <div class="destination-content">
                    <h3>Aktivitas charging</h3>
                    <p>Tinjau sesi terbaru, status pembayaran, energi, dan waktu pengisian.</p>
                </div>
                <div class="destination-footer">
                    <span class="destination-metric"><span data-live-stat="sessions_today">{{ $stats['sessions_today'] }}</span> sesi hari ini · {{ number_format($stats['energy_today'], 1, ',', '.') }} kWh</span>
                    <span class="destination-action">Lihat aktivitas <span aria-hidden="true">→</span></span>
                </div>
            </a>

            <a class="destination-card" href="{{ route('operator.reports.inspections') }}">
                <div class="destination-top">
                    <span class="destination-icon" aria-hidden="true">🛠</span>
                    <span class="destination-tag {{ $stats['attention_chargers'] ? 'warning' : 'safe' }}">
                        {{ $stats['attention_chargers'] ? 'Perlu ditinjau' : 'Semua aman' }}
                    </span>
                </div>
                <div class="destination-content">
                    <h3>Laporan pemeriksaan</h3>
                    <p>Tinjau catatan kendala charger dan tandai pemeriksaan yang sudah selesai.</p>
                </div>
                <div class="destination-footer">
                    <span class="destination-metric" data-live-stat="attention_chargers">{{ $stats['attention_chargers'] }} laporan terbuka</span>
                    <span class="destination-action">Tinjau laporan <span aria-hidden="true">→</span></span>
                </div>
            </a>
        </div>
    </section>
</div>

<script>
    (() => {
        const liveStatus = document.getElementById('operator-live-status');
        const liveUrl = @json(route('operator.dashboard.live'));

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

                const inspectionIcon = document.querySelector('[data-inspection-icon]');
                const needsAttention = Number(payload.stats.attention_chargers) > 0;
                if (inspectionIcon) {
                    inspectionIcon.textContent = needsAttention ? '!' : '✓';
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
