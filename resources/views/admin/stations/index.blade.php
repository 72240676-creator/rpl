@extends('layouts.app')

@section('title', 'Manajemen SPKLU & Charger')

@section('content')
<div style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 20px;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Manajemen Lokasi SPKLU & Charger</h2>
        <p style="color: #64748b; margin: 0;">Kelola pendaftaran stasiun SPKLU, status operasional, dan unit charger.</p>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div style="display: grid; grid-template-columns: 380px 1fr; gap: 25px; align-items: start;">
        
        <!-- FORM TAMBAH LOKASI STASIUN -->
        <div style="background: #ffffff; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 20px;">+ Tambah Stasiun SPKLU Baru</h3>
            
            <form action="{{ route('admin.stations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Nama Stasiun</label>
                    <input type="text" name="nama_lokasi" placeholder="SPKLU Duta Wacana" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Jam Operasional</label>
                    <input type="text" name="jam_operasional" placeholder="24 Jam / 08.00 - 22.00" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Status Lokasi</label>
                    <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; /* styling Anda yang lain... */ ">
                        <option value="aktif">Aktif</option>
                        <option value="tutup_sementara">Tutup Sementara</option>
                        <option value="penuh">Penuh</option>
                        <option value="perawatan">Dalam Perawatan</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Latitude</label>
                        <input type="text" name="latitude" placeholder="-7.7828" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Longitude</label>
                        <input type="text" name="longitude" placeholder="110.3671" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Fasilitas</label>
                    <input type="text" name="fasilitas" placeholder="Toilet, Rest Area, WiFi" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Foto Lokasi</label>
                    <input type="file" name="foto" accept="image/*" style="width: 100%; font-size: 12px; color: #64748b;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" placeholder="Jl. Dr. Wahidin Sudirohusodo No. 5-25..." required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box; resize: vertical;"></textarea>
                </div>

                <button type="submit" style="width: 100%; background: #059669; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s;">
                    + Simpan Stasiun
                </button>
            </form>
        </div>

        <!-- DAFTAR STASIUN TERDAFTAR -->
        <div style="background: #ffffff; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 20px;">Daftar Stasiun SPKLU Terdaftar</h3>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b;">
                            <th style="padding: 12px; font-weight: 600;">Stasiun</th>
                            <th style="padding: 12px; font-weight: 600;">Operasional</th>
                            <th style="padding: 12px; font-weight: 600;">Status</th>
                            <th style="padding: 12px; font-weight: 600; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($locations) && count($locations) > 0)
                            @foreach($locations as $location)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px;">
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            @if(!empty($location->foto))
                                                <img src="{{ asset('storage/' . $location->foto) }}" alt="Foto" style="width: 45px; height: 45px; border-radius: 8px; object-fit: cover;">
                                            @else
                                                <div style="width: 45px; height: 45px; border-radius: 8px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 18px;">⚡</div>
                                            @endif
                                            <div>
                                                <strong style="color: #1e293b; display: block;">{{ $location->nama_lokasi }}</strong>
                                                <small style="color: #64748b; font-size: 11px;">{{ Str::limit($location->alamat, 45) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 12px; color: #475569;">{{ $location->jam_operasional }}</td>
                                    <td style="padding: 12px;">
                                        @php
                                            $statusVal =$location->status ?? 'aktif';
                                            $badgeColor = match($statusVal) {
                                                'aktif' => 'background: #dcfce7; color: #15803d;',
                                                'tutup sementara' => 'background: #fef3c7; color: #b45309;',
                                                'penuh' => 'background: #ffedd5; color: #c2410c;',
                                                'dalam perawatan' => 'background: #fee2e2; color: #b91c1c;',
                                                default => 'background: #f1f5f9; color: #475569;'
                                            };
                                        @endphp
                                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; {{ $badgeColor }}">
                                            {{ $statusVal }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; text-align: right;">
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <a href="{{ route('admin.stations.edit', $location->id_location) }}" style="background: #e0e7ff; color: #4338ca; border: none; padding: 6px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none;">Edit</a>
                                            <button type="button" onclick="openChargerModal('{{ $location->id_location }}', '{{$location->nama_lokasi }}')" style="background: #2563eb; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                                🔌 Charger
                                            </button>
                                            <form action="{{ route('admin.stations.destroy', $location->id_location) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus stasiun ini?')" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; color: #ef4444; font-weight: 600; font-size: 12px; cursor: pointer; padding: 6px;">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">Belum ada stasiun SPKLU terdaftar.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KELOLA CHARGER -->
<div id="chargerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center;">
    <div style="background: white; padding: 30px; border-radius: 16px; width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; color: #1e293b;" id="modalStationTitle">Kelola Charger</h3>
            <button type="button" onclick="closeChargerModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">✕</button>
        </div>

        <!-- FORM TAMBAH CHARGER BARU -->
        <form id="chargerForm" method="POST" style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
            @csrf
            <input type="hidden" name="location_id" id="modal_location_id">
            <h4 style="margin: 0 0 12px 0; font-size: 13px; color: #334155;">+ Tambah Unit Charger Baru</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                <input type="text" name="nomor_perangkat" placeholder="No. Perangkat (Contoh: CHG-01)" required style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
                <select name="tipe_konektor" required style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: white;">
                    <option value="">-- Pilih Konektor --</option>
                    <option value="CCS2">CCS2 (Fast Charging)</option>
                    <option value="Type 2">Type 2 (AC)</option>
                    <option value="CHAdeMO">CHAdeMO</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                <input type="number" name="kapasitas_daya" placeholder="Daya (kW)" required style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
                
                <select name="status_koneksi" style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: white;">
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                </select>

                <select name="status_penggunaan" style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: white;">
                    <option value="tersedia">Tersedia</option>
                    <option value="digunakan">Digunakan</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>

            <button type="submit" style="width: 100%; background: #10b981; color: white; border: none; padding: 8px; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">Simpan Charger</button>
        </form>

        <p style="font-size: 12px; color: #64748b; margin-bottom: 10px;">Fitur pendaftaran charger akan langsung terhubung ke database stasiun ini.</p>
    </div>
</div>

<script>
    function openChargerModal(locationId, stationName) {
        document.getElementById('chargerForm').action = `/admin/stations/${locationId}/chargers`;
        document.getElementById('modal_location_id').value = locationId;
        document.getElementById('modalStationTitle').innerText = 'Kelola Charger - ' + stationName;
        document.getElementById('chargerModal').style.display = 'flex';
    }

    function closeChargerModal() {
        document.getElementById('chargerModal').style.display = 'none';
    }
</script>
@endsection