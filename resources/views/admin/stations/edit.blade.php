@extends('layouts.app')

@section('title', 'Edit Stasiun SPKLU')

@section('content')
<div style="width: 100%; max-width: 800px; margin: 0 auto; padding: 20px;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Edit Stasiun SPKLU</h2>
        <p style="color: #64748b; margin: 0;">Perbarui informasi detail stasiun, jam operasional, dan lokasi SPKLU.</p>
    </div>

    @if ($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: #ffffff; padding: 30px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        <form action="{{ route('admin.stations.update', $station->id_location) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Nama Stasiun</label>
                <input type="text" name="nama_lokasi" value="{{ old('nama_lokasi', $station->nama_lokasi) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Jam Operasional</label>
                <input type="text" name="jam_operasional" value="{{ old('jam_operasional', $station->jam_operasional) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Status Lokasi</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; background: white;">
                    <option value="aktif" {{ old('status', $station->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tutup_sementara" {{ old('status', $station->status) == 'tutup_sementara' ? 'selected' : '' }}>Tutup Sementara</option>
                    <option value="penuh" {{ old('status', $station->status) == 'penuh' ? 'selected' : '' }}>Penuh</option>
                    <option value="perawatan" {{ old('status', $station->status) == 'perawatan' ? 'selected' : '' }}>Dalam Perawatan</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Latitude</label>
                    <input type="text" name="latitude" value="{{ old('latitude', $station->latitude) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Longitude</label>
                    <input type="text" name="longitude" value="{{ old('longitude', $station->longitude) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Fasilitas</label>
                <input type="text" name="fasilitas" value="{{ old('fasilitas', $station->fasilitas) }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box; resize: vertical;">{{ old('alamat', $station->alamat) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.stations.index') }}" style="background: #64748b; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; cursor: pointer;">Batal</a>
                <button type="submit" style="background: #059669; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection