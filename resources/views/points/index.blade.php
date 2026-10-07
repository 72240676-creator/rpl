@extends('layouts.app')

@section('title', 'EV Reward & Poin')

@section('content')
<!-- Memastikan CDN Tailwind CSS ter-load untuk tampilan rapi -->
<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-5xl mx-auto px-4 py-8">

    <!-- Tombol Kembali & Header Halaman -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" 
               style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background-color: #ffffff; border: 1px solid #e5e7eb; color: #4b5563; text-decoration: none; flex-shrink: 0;"
               class="hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">EV Reward & Poin</h1>
                <p class="text-xs text-gray-500">Tukarkan poin pengisian daya dengan berbagai voucher menarik</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
            ⚡ Member Rewards
        </span>
    </div>

    {{-- Alert Pesan Notifikasi (Sukses / Error / Validasi) --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl flex items-center gap-3 font-medium">
            <span>✅</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm rounded-2xl flex items-center gap-3 font-medium">
            <span>⚠️</span>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm rounded-2xl">
            @foreach($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    @php
        // Pembacaan poin yang fleksibel (poin atau points)
        $userPoin = (int) (Auth::user()->poin ?? Auth::user()->points ?? 0);
    @endphp

    <!-- Hero Card Poin -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 p-6 md:p-8 text-white shadow-xl mb-8">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-20 -top-10 w-32 h-32 bg-yellow-300/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-medium text-amber-100 border border-white/20 mb-3">
                    <span>⚡ Saldo Poin EV Charge</span>
                </div>
                <h2 class="text-4xl font-extrabold tracking-tight mb-2">
                    {{ number_format($userPoin, 0, ',', '.') }} 
                    <span class="text-lg font-normal text-amber-100">Poin</span>
                </h2>
                <p class="text-xs md:text-sm text-amber-100">
                    Setiap transaksi pengisian daya Rp 10.000 = <strong class="text-white">1 Poin</strong>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-center min-w-[120px]">
                    <span class="text-xs text-amber-100 block">Status Akun</span>
                    <span class="text-sm font-bold text-white">Aktif</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-center min-w-[120px]">
                    <span class="text-xs text-amber-100 block">Nilai Tukar</span>
                    <span class="text-sm font-bold text-white">1 Poin = Rp 1.000</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Katalog Penukaran Hadiah -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                🎁 Katalog Penukaran Hadiah
            </h2>
            <span class="text-xs text-gray-500">Pilih voucher sesuai poin kamu</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            
            <!-- Voucher 1 (5 Poin -> Saldo Rp 5.000) -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                            Diskon Charge
                        </span>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                            ⚡ 5 Poin
                        </span>
                    </div>
                    <h3 class="font-bold text-gray-800 text-base mb-1">Voucher Potongan Rp 5.000</h3>
                    <p class="text-xs text-gray-500 mb-4">Tukarkan 5 poin menjadi saldo e-wallet sebesar Rp 5.000.</p>
                </div>
                <form action="{{ route('poin.redeem') }}" method="POST">
                    @csrf
                    <input type="hidden" name="points" value="5">
                    <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-bold transition {{ $userPoin >= 5 ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                        {{ $userPoin < 5 ? 'disabled' : '' }}>
                        {{ $userPoin >= 5 ? 'Tukarkan Poin' : 'Poin Belum Cukup' }}
                    </button>
                </form>
            </div>

            <!-- Voucher 2 (10 Poin -> Saldo Rp 10.000) -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 text-xs font-bold border border-sky-100">
                            Saldo Gratis
                        </span>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                            ⚡ 10 Poin
                        </span>
                    </div>
                    <h3 class="font-bold text-gray-800 text-base mb-1">Voucher Saldo Rp 10.000</h3>
                    <p class="text-xs text-gray-500 mb-4">Tukarkan 10 poin menjadi saldo e-wallet sebesar Rp 10.000.</p>
                </div>
                <form action="{{ route('poin.redeem') }}" method="POST">
                    @csrf
                    <input type="hidden" name="points" value="10">
                    <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-bold transition {{ $userPoin >= 10 ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                        {{ $userPoin < 10 ? 'disabled' : '' }}>
                        {{ $userPoin >= 10 ? 'Tukarkan Poin' : 'Poin Belum Cukup' }}
                    </button>
                </form>
            </div>

            <!-- Voucher 3 (20 Poin -> Saldo Rp 20.000) -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-bold border border-purple-100">
                            Cashback Saldo
                        </span>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                            ⚡ 20 Poin
                        </span>
                    </div>
                    <h3 class="font-bold text-gray-800 text-base mb-1">Cashback Saldo Rp 20.000</h3>
                    <p class="text-xs text-gray-500 mb-4">Klaim cashback Rp 20.000 langsung masuk ke saldo dompet aplikasi.</p>
                </div>
                <form action="{{ route('poin.redeem') }}" method="POST">
                    @csrf
                    <input type="hidden" name="points" value="20">
                    <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-bold transition {{ $userPoin >= 20 ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                        {{ $userPoin < 20 ? 'disabled' : '' }}>
                        {{ $userPoin >= 20 ? 'Tukarkan Poin' : 'Poin Belum Cukup' }}
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection