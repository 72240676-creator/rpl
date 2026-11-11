 @extends('layouts.app')

@section('title', 'Laporan Pemeriksaan Charger')

@section('content')
<style>
    .report-page{max-width:1100px;margin:0 auto;padding:35px 22px;color:#172554}
    .report-heading{display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:24px;flex-wrap:wrap}
    .report-heading p{color:#64748b;margin:7px 0 0}
    .report-back{color:#047857;text-decoration:none;font-weight:700}
    .report-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:22px;margin-bottom:15px;box-shadow:0 5px 18px rgba(15,23,42,.04)}
    .report-top{display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}
    .report-name{font-size:18px;font-weight:750;color:#0f172a}
    .report-location{font-size:13px;color:#64748b;margin-top:5px}
    .report-badge{padding:6px 11px;border-radius:30px;background:#fef3c7;color:#92400e;font-size:12px;font-weight:700}
    .report-note{margin:18px 0;padding:15px;background:#fffbeb;border:1px solid #fde68a;border-radius:11px;color:#78350f;line-height:1.6}
    .report-meta{font-size:12px;color:#64748b;margin-bottom:15px}
    .report-button{padding:10px 15px;border:0;border-radius:9px;background:#059669;color:white;font-weight:700;cursor:pointer}
    .report-empty{text-align:center;padding:55px 20px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:16px;color:#047857}
    .report-alert{padding:12px 15px;background:#d1fae5;color:#047857;border-radius:10px;margin-bottom:17px}
</style>

<div class="report-page">
    <div class="report-heading">
        <div>
            <h1 style="margin:0">Laporan Pemeriksaan</h1>
            <p>Daftar charger yang dilaporkan membutuhkan pemeriksaan.</p>
        </div>
        <a class="report-back" href="{{ route('operator.dashboard') }}">← Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div class="report-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="report-alert" style="background:#fee2e2;color:#b91c1c">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @forelse($chargers as $charger)
        <article class="report-card">
            <div class="report-top">
                <div>
                    <div class="report-name">{{ $charger->device_number }}</div>
                    <div class="report-location">
                        {{ $charger->location?->nama_lokasi ?? 'Lokasi tidak tersedia' }}
                        · {{ $charger->connector_type }}
                    </div>
                </div>
                <span class="report-badge">Perlu pemeriksaan</span>
            </div>

            <div class="report-note">
                <strong>Catatan kendala:</strong><br>
                {{ $charger->inspection_note ?: 'Tidak ada catatan.' }}
            </div>

            <div class="report-meta">
                Dilaporkan:
                {{ $charger->inspection_reported_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-' }} WIB
                · Oleh: {{ $charger->inspectionReporter?->nama ?? 'Operator' }}
            </div>

            <form action="{{ route('operator.chargers.inspection.update', $charger->id_charger) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="needs_inspection" value="0">
                <button class="report-button" type="submit"
                    onclick="return confirm('Tandai pemeriksaan charger ini sudah selesai?')">
                    Tandai Pemeriksaan Selesai
                </button>
            </form>
        </article>
    @empty
        <div class="report-empty">
            <div style="font-size:35px;margin-bottom:10px">✓</div>
            <h2 style="margin:0 0 8px">Semua charger dalam kondisi terpantau</h2>
            <p style="margin:0">Belum ada laporan charger yang membutuhkan pemeriksaan.</p>
        </div>
    @endforelse
</div>
@endsection