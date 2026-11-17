
@extends('layouts.app')

@section('title', 'Laporan Pengguna')

@section('content')
<div class="container" style="max-width:1200px; margin:30px auto; padding:20px;">
    <h2>Laporan Pengguna</h2>
    <p>Daftar laporan masalah SPKLU dari pelanggan.</p>

    @if (session('success'))
        <div style="padding:12px; background:#d1fae5; color:#065f46; border-radius:8px; margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($reports as $report)
        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:20px; margin-bottom:15px;">
            <h3>{{ $report->jenis_masalah }}</h3>

            <p><strong>Pelapor:</strong> {{ $report->user->nama ?? 'Tidak diketahui' }}</p>
            <p><strong>Lokasi:</strong> {{ $report->location->nama_lokasi ?? 'Tidak diketahui' }}</p>
            <p><strong>Charger:</strong> {{ $report->charger->device_number ?? 'Tidak disebutkan' }}</p>
            <p><strong>Deskripsi:</strong> {{ $report->deskripsi }}</p>
            <p><strong>Tanggal:</strong> {{ $report->created_at?->format('d-m-Y H:i') }}</p>

            @if ($report->foto)
                <p>
                    <strong>Foto bukti:</strong><br>
                    <a href="{{ asset('storage/' . $report->foto) }}" target="_blank">
                        <img src="{{ asset('storage/' . $report->foto) }}"
                             alt="Foto bukti laporan"
                             style="max-width:200px; margin-top:8px; border-radius:8px;">
                    </a>
                </p>
            @endif

            <p><strong>Status saat ini:</strong> {{ ucfirst($report->status) }}</p>

            <form action="{{ route('operator.reports.users.status.update', $report->id_laporan) }}" method="POST">
                @csrf
                @method('PUT')

                <label for="status-{{ $report->id_laporan }}">Ubah status:</label>

                <select name="status" id="status-{{ $report->id_laporan }}" required>
                    <option value="menunggu" @selected($report->status === 'menunggu')>Menunggu</option>
                    <option value="diproses" @selected($report->status === 'diproses')>Diproses</option>
                    <option value="selesai" @selected($report->status === 'selesai')>Selesai</option>
                </select>

                <button type="submit">Simpan Status</button>
            </form>
        </div>
    @empty
        <p>Belum ada laporan dari pelanggan.</p>
    @endforelse
</div>
@endsection
