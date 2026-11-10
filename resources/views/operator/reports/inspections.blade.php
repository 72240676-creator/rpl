@extends('layouts.app')

@section('title', 'Laporan Pemeriksaan')

@section('content')
<style>
    .operator-page { width: min(1180px, 100%); margin: 0 auto; padding: 34px 24px 56px; color: #172554; }
    .page-back { margin-bottom: 20px; }
    .page-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; margin-bottom: 22px; }
    .page-eyebrow { margin-bottom: 8px; color: #b45309; font-size: 10px; font-weight: 800; letter-spacing: 1.15px; text-transform: uppercase; }
    .page-heading h1 { margin: 0 0 7px; color: #0f172a; font-size: clamp(25px, 4vw, 32px); letter-spacing: -.6px; }
    .page-heading p { margin: 0; color: #64748b; font-size: 13px; line-height: 1.6; }
    .report-count { flex: 0 0 auto; padding: 9px 13px; color: #b45309; background: #fffbeb; border: 1px solid #fde68a; border-radius: 999px; font-size: 11px; font-weight: 750; }
    .report-panel { overflow: hidden; background: #fff; border: 1px solid #e2e8f0; border-radius: 17px; box-shadow: 0 7px 22px rgba(15, 23, 42, .04); }
    .panel-heading { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 19px 22px; border-bottom: 1px solid #eef2f7; }
    .panel-heading h2 { margin: 0 0 4px; color: #0f172a; font-size: 15px; }
    .panel-heading p { margin: 0; color: #94a3b8; font-size: 11px; }
    .report-list { padding: 3px 22px; }
    .report-item { display: grid; grid-template-columns: auto minmax(0, 1fr) minmax(190px, .7fr); align-items: start; gap: 15px; padding: 18px 0; border-bottom: 1px solid #f1f5f9; }
    .report-item:last-child { border-bottom: 0; }
    .report-icon { display: grid; width: 42px; height: 42px; place-items: center; color: #b45309; background: #fffbeb; border: 1px solid #fde68a; border-radius: 13px; font-size: 18px; }
    .report-main { min-width: 0; }
    .report-title { margin-bottom: 5px; color: #1e293b; font-size: 13px; font-weight: 750; }
    .report-location, .report-meta { color: #64748b; font-size: 11px; line-height: 1.65; }
    .report-note { margin-top: 9px; padding: 10px 12px; color: #78350f; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px; font-size: 11px; line-height: 1.55; white-space: pre-line; }
    .report-actions { display: grid; gap: 10px; }
    .report-actions summary { color: #047857; font-size: 11px; font-weight: 750; cursor: pointer; }
    .report-form { display: grid; gap: 8px; }
    .report-form textarea { width: 100%; min-height: 72px; padding: 9px 10px; box-sizing: border-box; color: #334155; border: 1px solid #cbd5e1; border-radius: 9px; font: inherit; font-size: 11px; resize: vertical; }
    .report-form button { justify-self: start; padding: 8px 11px; color: #fff; background: #b45309; border: 0; border-radius: 8px; font: inherit; font-size: 11px; font-weight: 700; cursor: pointer; }
    .report-form button:hover { background: #92400e; }
    .resolve-form button { padding: 7px 10px; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; font: inherit; font-size: 10px; font-weight: 700; cursor: pointer; }
    .resolve-form button:hover { background: #d1fae5; }
    .empty-state { padding: 52px 20px; color: #64748b; text-align: center; }
    .empty-icon { display: grid; width: 52px; height: 52px; margin: 0 auto 13px; place-items: center; color: #047857; background: #ecfdf5; border-radius: 16px; font-size: 23px; }
    .alert { margin-bottom: 16px; padding: 13px 16px; border: 1px solid transparent; border-radius: 12px; font-size: 13px; }
    .alert.success { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
    .alert.error { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }
    @media (max-width: 760px) { .operator-page { padding: 25px 14px 40px; } .page-heading { align-items: flex-start; flex-direction: column; } .report-list { padding-right: 15px; padding-left: 15px; } .report-item { grid-template-columns: auto minmax(0, 1fr); gap: 11px; } .report-actions { grid-column: 2; } .panel-heading { padding: 16px; } }
</style>

<div class="operator-page">
    <div class="page-back">
        <x-back-link href="{{ route('operator.dashboard') }}" label="Kembali ke Dashboard Operator" />
    </div>

    <header class="page-heading">
        <div>
            <div class="page-eyebrow">Keselamatan & kesiapan unit</div>
            <h1>Laporan pemeriksaan</h1>
            <p>Tinjau kendala yang dilaporkan dan tandai pemeriksaan selesai setelah ditindaklanjuti.</p>
        </div>
        <span class="report-count">{{ $chargers->count() }} laporan terbuka</span>
    </header>

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

    <section class="report-panel" aria-labelledby="inspection-list-title">
        <header class="panel-heading">
            <div>
                <h2 id="inspection-list-title">Perlu ditindaklanjuti</h2>
                <p>Laporan terbaru ditampilkan lebih dahulu.</p>
            </div>
        </header>
        <div class="report-list">
            @forelse ($chargers as $charger)
                <article class="report-item">
                    <div class="report-icon" aria-hidden="true">!</div>
                    <div class="report-main">
                        <div class="report-title">{{ $charger->device_number }} <span class="status-badge" style="display:inline-flex;margin-left:5px;padding:4px 8px;color:#b45309;background:#fef3c7;border-radius:999px;font-size:9px;font-weight:750;">Perlu pemeriksaan</span></div>
                        <div class="report-location">{{ $charger->location?->nama_lokasi ?? 'Lokasi tidak tersedia' }} · {{ $charger->connector_type }}</div>
                        @if($charger->inspection_note)
                            <div class="report-note">{{ $charger->inspection_note }}</div>
                        @endif
                        <div class="report-meta" style="margin-top:7px;">
                            Dilaporkan {{ $charger->inspection_reported_at?->timezone('Asia/Jakarta')->format('d M Y, H:i') ?? '-' }} WIB
                            @if($charger->inspectionReporter)
                                · oleh {{ $charger->inspectionReporter->nama }}
                            @endif
                        </div>
                    </div>
                    <div class="report-actions">
                        <details>
                            <summary>Perbarui catatan</summary>
                            <form class="report-form" action="{{ route('operator.chargers.inspection.update', $charger->id_charger) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="needs_inspection" value="1">
                                <textarea name="inspection_note" maxlength="1000" required>{{ $charger->inspection_note }}</textarea>
                                <button type="submit">Simpan catatan</button>
                            </form>
                        </details>
                        <form class="resolve-form" action="{{ route('operator.chargers.inspection.update', $charger->id_charger) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="needs_inspection" value="0">
                            <button type="submit">Tandai selesai</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <span class="empty-icon" aria-hidden="true">✓</span>
                    <strong>Semua charger dalam kondisi terpantau</strong>
                    <div style="margin-top:5px;font-size:12px;">Belum ada laporan pemeriksaan yang perlu ditindaklanjuti.</div>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
