@extends('layouts.app')

@section('title', 'Kelola Lokasi Operator')

@section('content')
    <div style="width: 100%; max-width: 1000px; margin: 30px auto; padding: 24px; background: #fff; border-radius: 16px;">
        <h2>Kelola Lokasi SPKLU</h2>

        @if (session('success'))
            <p role="status" style="color: #15803d;">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div role="alert" style="color: #b91c1c;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h3>Tambah lokasi</h3>
        <form action="{{ route('operator.locations.store') }}" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
            @csrf
            <input name="nama_lokasi" placeholder="Nama lokasi" value="{{ old('nama_lokasi') }}" required>
            <input name="alamat" placeholder="Alamat" value="{{ old('alamat') }}" required>
            <input name="latitude" type="number" step="any" placeholder="Latitude" value="{{ old('latitude') }}" required>
            <input name="longitude" type="number" step="any" placeholder="Longitude" value="{{ old('longitude') }}" required>
            <input name="jam_operasional" placeholder="Jam operasional" value="{{ old('jam_operasional', '24 Jam') }}" required>
            <input name="fasilitas" placeholder="Fasilitas (opsional)" value="{{ old('fasilitas') }}">
            <select name="status" required>
                @foreach (['aktif', 'tutup_sementara', 'penuh', 'perawatan'] as $status)
                    <option value="{{ $status }}" @selected(old('status', 'aktif') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                @endforeach
            </select>
            <button type="submit" style="padding: 10px 16px; background: #059669; color: #fff; border: 0; border-radius: 8px; cursor: pointer;">Tambah lokasi</button>
        </form>

        <h3 style="margin-top: 32px;">Daftar lokasi</h3>
        @forelse ($locations as $location)
            <form action="{{ route('operator.locations.update', $location->id_location) }}" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; padding: 16px 0; border-top: 1px solid #e2e8f0;">
                @csrf
                @method('PUT')
                <input name="nama_lokasi" aria-label="Nama lokasi" value="{{ $location->nama_lokasi }}" required>
                <input name="alamat" aria-label="Alamat" value="{{ $location->alamat }}" required>
                <input name="latitude" type="number" step="any" aria-label="Latitude" value="{{ $location->latitude }}" required>
                <input name="longitude" type="number" step="any" aria-label="Longitude" value="{{ $location->longitude }}" required>
                <input name="jam_operasional" aria-label="Jam operasional" value="{{ $location->jam_operasional }}" required>
                <input name="fasilitas" aria-label="Fasilitas" value="{{ $location->fasilitas }}">
                <select name="status" aria-label="Status" required>
                    @foreach (['aktif', 'tutup_sementara', 'penuh', 'perawatan'] as $status)
                        <option value="{{ $status }}" @selected($location->status === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                    @endforeach
                </select>
                <button type="submit" style="padding: 10px 16px; background: #2563eb; color: #fff; border: 0; border-radius: 8px; cursor: pointer;">Simpan perubahan</button>
            </form>
        @empty
            <p>Belum ada lokasi SPKLU.</p>
        @endforelse
    </div>
@endsection
