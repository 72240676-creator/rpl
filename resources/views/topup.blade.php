@extends('layouts.app')

@section('title', 'Isi Saldo')

@section('content')
<style>
    .wallet-page { width: min(1120px, 100%); margin: 0 auto; padding: 8px 0 42px; color: #172b4d; }
    .wallet-page * { box-sizing: border-box; }
    .wallet-back { margin-bottom: 18px; }
    .wallet-heading { margin-bottom: 20px; }
    .wallet-eyebrow { margin-bottom: 7px; color: #047857; font-size: 10px; font-weight: 800; letter-spacing: 1.15px; text-transform: uppercase; }
    .wallet-heading h1 { margin: 0 0 7px; color: #102a43; font-size: clamp(24px, 3vw, 30px); letter-spacing: -.6px; }
    .wallet-heading p { max-width: 650px; margin: 0; color: #64748b; font-size: 13px; line-height: 1.65; }
    .wallet-alert { margin-bottom: 16px; padding: 13px 15px; border: 1px solid #a7f3d0; border-radius: 12px; background: #ecfdf5; color: #065f46; font-size: 12px; line-height: 1.55; }
    .wallet-alert.error { border-color: #fecaca; background: #fef2f2; color: #991b1b; }
    .wallet-layout { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr); gap: 20px; align-items: start; }
    .wallet-panel { min-width: 0; padding: 22px; border: 1px solid #e5ebf2; border-radius: 18px; background: #fff; box-shadow: 0 7px 22px rgba(15, 23, 42, .04); }
    .wallet-panel-heading { margin-bottom: 17px; }
    .wallet-panel-heading h2 { margin: 0 0 5px; color: #102a43; font-size: 16px; }
    .wallet-panel-heading p { margin: 0; color: #718096; font-size: 11px; line-height: 1.55; }
    .wallet-balance { display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-bottom: 22px; padding: 17px; border: 1px solid #bbf7d0; border-radius: 14px; background: linear-gradient(110deg, #f0fdf4, #ecfdf5); }
    .wallet-balance-label { margin-bottom: 5px; color: #52736a; font-size: 10px; font-weight: 650; }
    .wallet-balance-value { color: #065f46; font-size: clamp(22px, 4vw, 29px); font-weight: 800; letter-spacing: -.7px; }
    .wallet-balance-icon { display: grid; width: 44px; height: 44px; flex: 0 0 44px; place-items: center; border-radius: 14px; background: #d1fae5; font-size: 21px; }
    .wallet-field { margin-bottom: 20px; }
    .wallet-field-label { display: block; margin-bottom: 9px; color: #334e68; font-size: 11px; font-weight: 750; }
    .amount-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 9px; margin-bottom: 13px; }
    .amount-option { position: relative; }
    .amount-option input { position: absolute; width: 1px; height: 1px; overflow: hidden; opacity: 0; }
    .amount-option label { display: grid; min-height: 44px; place-items: center; padding: 8px 5px; border: 1px solid #dbe3eb; border-radius: 10px; background: #fff; color: #475569; font-size: 11px; font-weight: 750; cursor: pointer; transition: border-color .2s, color .2s, background .2s; }
    .amount-option label:hover { border-color: #6ee7b7; }
    .amount-option input:checked + label { border-color: #10b981; background: #ecfdf5; color: #047857; }
    .amount-option input:focus-visible + label { outline: 3px solid rgba(16, 185, 129, .22); outline-offset: 2px; }
    .amount-input-wrap { display: flex; align-items: center; overflow: hidden; border: 1px solid #cbd5e1; border-radius: 10px; background: #fff; transition: border-color .2s, box-shadow .2s; }
    .amount-input-wrap:focus-within { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, .12); }
    .amount-prefix { padding-left: 13px; color: #64748b; font-size: 12px; font-weight: 700; }
    .amount-input-wrap input { width: 100%; min-height: 44px; padding: 0 12px; border: 0; outline: 0; color: #102a43; font: inherit; font-size: 13px; font-weight: 700; }
    .field-hint { margin: 6px 0 0; color: #94a3b8; font-size: 10px; }
    .field-error { margin: 6px 0 0; color: #dc2626; font-size: 10px; font-weight: 650; }
    .wallet-submit { display: flex; width: 100%; min-height: 46px; align-items: center; justify-content: center; gap: 8px; border: 0; border-radius: 11px; background: linear-gradient(110deg, #059669, #10b981); color: #fff; font: inherit; font-size: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 5px 13px rgba(5, 150, 105, .17); transition: transform .2s, box-shadow .2s; }
    .wallet-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 17px rgba(5, 150, 105, .23); }
    .wallet-submit:focus-visible { outline: 3px solid rgba(16, 185, 129, .3); outline-offset: 3px; }
    .wallet-summary { display: grid; gap: 11px; padding: 15px; border: 1px solid #e5ebf2; border-radius: 13px; background: #f8fafc; }
    .summary-row { display: flex; justify-content: space-between; gap: 12px; color: #64748b; font-size: 11px; }
    .summary-row strong { color: #334e68; font-weight: 700; text-align: right; }
    .summary-total { padding-top: 10px; border-top: 1px solid #e2e8f0; }
    .summary-total strong { color: #047857; font-size: 14px; }
    .wallet-history { margin-top: 20px; }
    .history-list { display: grid; }
    .history-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 13px 0; border-top: 1px solid #eef2f7; }
    .history-main { min-width: 0; }
    .history-main strong { display: block; overflow: hidden; margin-bottom: 4px; color: #334e68; font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
    .history-main span { color: #94a3b8; font-size: 9px; }
    .history-amount { flex: 0 0 auto; color: #047857; font-size: 11px; font-weight: 800; }
    .history-empty { padding: 18px 0; border-top: 1px solid #eef2f7; color: #94a3b8; font-size: 11px; line-height: 1.6; }
    .history-pagination { margin-top: 10px; font-size: 11px; }
    .wallet-side-note { margin-top: 14px; padding: 13px; border-radius: 12px; background: #f8fafc; color: #64748b; font-size: 10px; line-height: 1.6; }
    .wallet-side-note strong { color: #334e68; }

    @media (max-width: 850px) { .wallet-layout { grid-template-columns: 1fr; } }
    @media (max-width: 560px) {
        .wallet-page { padding-top: 0; }
        .wallet-panel { padding: 17px 14px; border-radius: 15px; }
        .amount-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .wallet-balance { padding: 14px; }
    }
</style>

<div class="wallet-page">
    <div class="wallet-back"><x-back-link href="{{ route('dashboard') }}" /></div>

    <header class="wallet-heading">
        <div class="wallet-eyebrow">Dompet EV Charge</div>
        <h1>Isi saldo</h1>
        <p>Tambahkan saldo untuk membayar sesi pengisian daya kendaraan Anda.</p>
    </header>

    @if (session('success'))
        <div class="wallet-alert" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="wallet-alert error" role="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="wallet-alert error" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="wallet-layout">
        <section class="wallet-panel" aria-labelledby="topupHeading">
            <header class="wallet-panel-heading">
                <h2 id="topupHeading">Isi saldo</h2>
                <p>Pilih nominal isi saldo. Minimal Rp 10.000 dan maksimal Rp 5.000.000.</p>
            </header>

            <div class="wallet-balance" aria-label="Saldo saat ini">
                <div>
                    <div class="wallet-balance-label">Saldo Anda</div>
                    <div class="wallet-balance-value">Rp {{ number_format((float) ($user->saldo ?? 0), 0, ',', '.') }}</div>
                </div>
                <span class="wallet-balance-icon" aria-hidden="true">💳</span>
            </div>

            <form action="{{ route('topup.store') }}" method="POST" id="topupForm">
                @csrf
                <input type="hidden" name="topup_reference" value="{{ old('topup_reference', $topupReference) }}">

                <div class="wallet-field">
                    <span class="wallet-field-label">Pilih jumlah top up</span>
                    <div class="amount-grid">
                        @foreach ([50000, 100000, 250000, 500000] as $preset)
                            <div class="amount-option">
                                <input type="radio" id="preset_{{ $preset }}" name="nominal_preset" value="{{ $preset }}" @checked((int) old('nominal') === $preset)>
                                <label for="preset_{{ $preset }}">Rp {{ number_format($preset, 0, ',', '.') }}</label>
                            </div>
                        @endforeach
                    </div>
                    <label class="wallet-field-label" for="nominal">Atau masukkan jumlah lain</label>
                    <div class="amount-input-wrap">
                        <span class="amount-prefix">Rp</span>
                        <input type="number" id="nominal" name="nominal" min="10000" max="5000000" step="1000" inputmode="numeric" placeholder="Minimal 10.000" value="{{ old('nominal') }}" required aria-describedby="amountHint @error('nominal') nominalError @enderror">
                    </div>
                    <p class="field-hint" id="amountHint">Kelipatan Rp 1.000 · maksimal Rp 5.000.000 per transaksi.</p>
                    @error('nominal') <p class="field-error" id="nominalError">{{ $message }}</p> @enderror
                </div>

                <div class="wallet-side-note" style="margin: 0 0 16px;">
                    Saldo akan langsung dicatat ke dompet akun Anda.
                </div>

                <button class="wallet-submit" type="submit"><span aria-hidden="true">⚡</span> Isi saldo sekarang</button>
            </form>
        </section>

        <aside>
            <section class="wallet-panel" aria-labelledby="summaryHeading">
                <header class="wallet-panel-heading">
                    <h2 id="summaryHeading">Ringkasan transaksi</h2>
                    <p>Periksa jumlah sebelum mengisi saldo.</p>
                </header>
                <div class="wallet-summary" aria-live="polite">
                    <div class="summary-row"><span>Jumlah</span><strong id="summaryAmount">Belum dipilih</strong></div>
                    <div class="summary-row summary-total"><span>Saldo bertambah</span><strong id="summaryTotal">Rp 0</strong></div>
                </div>
                <div class="wallet-side-note"><strong>Catatan</strong><br>Pengisian saldo ini langsung tercatat pada aplikasi.</div>
            </section>

            <section class="wallet-panel wallet-history" aria-labelledby="historyHeading">
                <header class="wallet-panel-heading">
                    <h2 id="historyHeading">Riwayat top up</h2>
                    <p>Aktivitas penambahan saldo terbaru pada akun ini.</p>
                </header>
                <div class="history-list">
                    @forelse ($recentTopUps as $topUp)
                        <article class="history-item">
                            <div class="history-main">
                                <strong>TOPUP-{{ str_pad((string) $topUp->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                <span>{{ $topUp->created_at?->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB · Isi saldo</span>
                            </div>
                            <span class="history-amount">
                                @if ($topUp->status === 'success') + @endif
                                Rp {{ number_format((float) $topUp->amount, 0, ',', '.') }}
                                · {{ $topUp->status === 'success' ? 'Berhasil' : ($topUp->status === 'pending' ? 'Menunggu' : 'Gagal') }}
                            </span>
                        </article>
                    @empty
                        <div class="history-empty">Belum ada riwayat top up.</div>
                    @endforelse
                </div>
                @if ($recentTopUps->hasPages())
                    <div class="history-pagination">{{ $recentTopUps->links() }}</div>
                @endif
            </section>
        </aside>
    </div>
</div>

<script>
    (() => {
        const amountInput = document.getElementById('nominal');
        const presetInputs = document.querySelectorAll('input[name="nominal_preset"]');
        const amountSummary = document.getElementById('summaryAmount');
        const totalSummary = document.getElementById('summaryTotal');
        const formatCurrency = (value) => `Rp ${new Intl.NumberFormat('id-ID').format(value)}`;

        const updateSummary = () => {
            const amount = Number(amountInput.value || 0);
            amountSummary.textContent = amount > 0 ? formatCurrency(amount) : 'Belum dipilih';
            totalSummary.textContent = formatCurrency(amount);
        };

        presetInputs.forEach((preset) => {
            preset.addEventListener('change', () => {
                amountInput.value = preset.value;
                updateSummary();
            });
        });

        amountInput.addEventListener('input', () => {
            presetInputs.forEach((preset) => {
                preset.checked = preset.value === amountInput.value;
            });
            updateSummary();
        });

        updateSummary();
    })();
</script>
@endsection
