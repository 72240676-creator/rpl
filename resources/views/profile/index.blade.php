@extends('layouts.app')

@section('title', 'Profil & Kendaraan')

@section('content')
<div class="profile-wrapper" style="display: flex; flex-direction: column; gap: 20px; width: 100%; max-width: 800px; margin: 0 auto; padding: 20px;">
    
    <x-back-link href="{{ route('dashboard') }}" />

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; font-size: 14px; border: 1px solid #a7f3d0;">{{ session('success') }}</div>
    @endif

    <!-- Bagian 1: Informasi Profil & Mode Edit Interaktif -->
    <div style="background: #ffffff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">👤 Informasi Profil</h3>
            <button type="button" id="btn-toggle-edit" onclick="toggleEditMode()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s;">
                ✏️ Edit Profil
            </button>
        </div>

        <!-- TAMPILAN VIEW (Default: Menampilkan data teks rapi) -->
        <div id="profile-view-mode">
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <tr><th style="padding: 10px 0; color: #64748b; font-weight: 500; font-size: 14px; width: 150px;">Nama Lengkap</th><td style="padding: 10px 0; color: #1e293b; font-weight: 600; font-size: 14px;">{{ $user->nama }}</td></tr>
                <tr><th style="padding: 10px 0; color: #64748b; font-weight: 500; font-size: 14px;">Alamat Email</th><td style="padding: 10px 0; color: #1e293b; font-weight: 600; font-size: 14px;">{{ $user->email }}</td></tr>
                <tr><th style="padding: 10px 0; color: #64748b; font-weight: 500; font-size: 14px;">Nomor Telepon</th><td style="padding: 10px 0; color: #1e293b; font-weight: 600; font-size: 14px;">{{ $user->nomor_telepon ?? '-' }}</td></tr>
                <tr><th style="padding: 10px 0; color: #64748b; font-weight: 500; font-size: 14px;">Peran Akun</th><td style="padding: 10px 0; color: #1e293b; font-weight: 600; font-size: 14px; text-transform: capitalize;">{{ $user->peran }}</td></tr>
                <tr><th style="padding: 10px 0; color: #64748b; font-weight: 500; font-size: 14px;">Status Akun</th><td style="padding: 10px 0; color: #059669; font-weight: 600; font-size: 14px; text-transform: uppercase;">{{ $user->status_akun }}</td></tr>
            </table>
        </div>

        <!-- TAMPILAN FORM EDIT (Tersembunyi secara default, muncul jika tombol edit diklik) -->
        <div id="profile-edit-mode" style="display: none;">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="input-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required style="width: 100%; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; color: #1e293b; font-size: 14px;">
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Alamat Email (Tidak dapat diubah)</label>
                    <input type="email" value="{{ $user->email }}" disabled style="width: 100%; padding: 12px; background: #e2e8f0; border: 1px solid #cbd5e1; border-radius: 10px; color: #64748b; font-size: 14px; cursor: not-allowed;">
                </div>

                <div class="input-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Nomor Telepon</label>
                    <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $user->nomor_telepon) }}" required style="width: 100%; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; color: #1e293b; font-size: 14px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; border-top: 1px dashed #e2e8f0; padding-top: 20px;">
                    <div class="input-group">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Password Baru (Opsional)</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah" style="width: 100%; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; color: #1e293b; font-size: 14px;">
                    </div>
                    <div class="input-group">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" style="width: 100%; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; color: #1e293b; font-size: 14px;">
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" style="background: #10b981; color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.2s;">Simpan Perubahan</button>
                    <button type="button" onclick="toggleEditMode()" style="background: #e2e8f0; color: #475569; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bagian 2: Daftar Kendaraan Listrik -->
    <div style="background: #ffffff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">🚘 Kendaraan Listrik Saya ({{ $vehicles->count() }})</h3>
        
        @if($vehicles->isEmpty())
            <p style="color: #64748b; font-size: 14px; font-style: italic;">Belum ada kendaraan yang didaftarkan.</p>
        @else
            <div style="display: grid; gap: 15px; margin-top: 15px;">
                @php
                    $selectedVehId = session('selected_vehicle_id');
                @endphp
                @foreach($vehicles as $vehicle)
                    @php
                        $isActive = ($selectedVehId && (string)$selectedVehId === (string)$vehicle->id_vehicle) || (!$selectedVehId && $loop->first);
                    @endphp
                    <div style="background: #f8fafc; padding: 15px 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid {{ $isActive ? '#10b981' : '#94a3b8' }};">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <h4 style="margin: 0; font-size: 15px; color: #1e293b; font-weight: 700;">{{ $vehicle->nomor_polisi }}</h4>
                                @if($isActive)
                                    <span style="background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">✓ Kendaraan Aktif</span>
                                @else
                                    <form action="{{ route('vehicles.select') }}" method="POST" style="margin: 0; display: inline;">
                                        @csrf
                                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id_vehicle }}">
                                        <button type="submit" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                                            Gunakan Mobil Ini
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">{{ $vehicle->merek }} - {{ $vehicle->model }}</p>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            <span style="background: #e0f2fe; color: #0284c7; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold;">🔋 {{ $vehicle->battery_capacity_kwh ?? 50 }} kWh</span>
                            <span style="background: #10b981; color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">🔌 {{ $vehicle->tipe_konektor }}</span>
                            
                            {{-- Tombol Edit --}}
                            <button 
                                type="button" 
                                onclick="openEditVehicleModal({{ json_encode([
                                    'id' => $vehicle->id_vehicle,
                                    'merek' => $vehicle->merek,
                                    'model' => $vehicle->model,
                                    'nomor_polisi' => $vehicle->nomor_polisi,
                                    'tipe_konektor' => $vehicle->tipe_konektor,
                                    'battery_capacity_kwh' => $vehicle->battery_capacity_kwh ?? 50
                                ]) }})" 
                                title="Edit Kendaraan" 
                                style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                            >
                                ✏️ Edit
                            </button>

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('vehicles.destroy', $vehicle->id_vehicle) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kendaraan {{ $vehicle->nomor_polisi }}?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Kendaraan" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s;">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bagian 3: Formulir Tambah Kendaraan Baru -->
    <div style="background: #ffffff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">➕ Daftarkan Kendaraan Baru</h3>
        
        <form action="/profile/vehicle" method="POST">
            @csrf
            
            <div class="input-group">
                <label>Merek Kendaraan (Contoh: Hyundai, Wuling)</label>
                <input type="text" name="merek" placeholder="Masukkan merek" required>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label>Model Kendaraan (Contoh: Ioniq 5, Air EV)</label>
                <input type="text" name="model" placeholder="Masukkan model" required>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label>Nomor Polisi (Contoh: AB 1234 CD)</label>
                <input type="text" name="nomor_polisi" placeholder="Masukkan nomor polisi" required>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label>Tipe Konektor Pengisian</label>
                <select name="tipe_konektor" required style="width: 100%; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; color: #334155; font-size: 14px; outline: none;">
                    <option value="" disabled selected>-- Pilih Tipe Konektor --</option>
                    <option value="Type 2 (AC)">Type 2 (AC)</option>
                    <option value="CCS 2 (DC)">CCS 2 (DC Fast Charging)</option>
                    <option value="CHAdeMO (DC)">CHAdeMO (DC)</option>
                    <option value="GB/T">GB/T</option>
                </select>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label>Kapasitas Baterai (kWh) - Opsional</label>
                <input type="number" step="0.1" min="5" max="250" name="battery_capacity_kwh" placeholder="Contoh: 58 (Standar bawaan: 50 kWh)">
                <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">Membantu sistem menghitung persentase baterai secara akurat saat sesi pengisian.</small>
            </div>

            <button type="submit" class="btn-submit" style="width: 200px; margin-top: 20px;">Simpan Kendaraan</button>
        </form>
    </div>

</div>

<!-- Modal Pop-up Edit Kendaraan -->
<div id="modalEditVehicle" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: #ffffff; width: 100%; max-width: 500px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a;">✏️ Edit Data Kendaraan</h3>
            <button type="button" onclick="closeEditVehicleModal()" style="border: none; background: transparent; font-size: 20px; cursor: pointer; color: #64748b; line-height: 1;">✕</button>
        </div>

        <form id="formEditVehicle" method="POST" style="padding: 24px;">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Merek Kendaraan</label>
                <input type="text" id="edit_merek" name="merek" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Model Kendaraan</label>
                <input type="text" id="edit_model" name="model" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Nomor Polisi</label>
                <input type="text" id="edit_nomor_polisi" name="nomor_polisi" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Tipe Konektor Pengisian</label>
                <select id="edit_tipe_konektor" name="tipe_konektor" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; box-sizing: border-box; background: #fff;">
                    <option value="Type 2 (AC)">Type 2 (AC)</option>
                    <option value="CCS 2 (DC)">CCS 2 (DC Fast Charging)</option>
                    <option value="CHAdeMO (DC)">CHAdeMO (DC)</option>
                    <option value="GB/T">GB/T</option>
                </select>
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Kapasitas Baterai (kWh)</label>
                <input type="number" step="0.1" min="5" max="250" id="edit_battery_capacity_kwh" name="battery_capacity_kwh" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditVehicleModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer;">Batal</button>
                <button type="submit" style="background: #10b981; color: #ffffff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Script Interaktif untuk Toggle Mode Edit Profil & Modal Kendaraan -->
<script>
    function toggleEditMode() {
        const viewMode = document.getElementById('profile-view-mode');
        const editMode = document.getElementById('profile-edit-mode');
        const btnToggle = document.getElementById('btn-toggle-edit');

        if (viewMode.style.display === 'none') {
            viewMode.style.display = 'block';
            editMode.style.display = 'none';
            btnToggle.innerHTML = '✏️ Edit Profil';
            btnToggle.style.background = '#f1f5f9';
            btnToggle.style.color = '#475569';
        } else {
            viewMode.style.display = 'none';
            editMode.style.display = 'block';
            btnToggle.innerHTML = '✕ Batal';
            btnToggle.style.background = '#ef4444';
            btnToggle.style.color = '#ffffff';
        }
    }

    function openEditVehicleModal(data) {
        const modal = document.getElementById('modalEditVehicle');
        const form = document.getElementById('formEditVehicle');
        form.action = `/profile/vehicle/${data.id}`;
        document.getElementById('edit_merek').value = data.merek || '';
        document.getElementById('edit_model').value = data.model || '';
        document.getElementById('edit_nomor_polisi').value = data.nomor_polisi || '';
        document.getElementById('edit_tipe_konektor').value = data.tipe_konektor || 'Type 2 (AC)';
        document.getElementById('edit_battery_capacity_kwh').value = data.battery_capacity_kwh || 50;
        
        modal.style.display = 'flex';
    }

    function closeEditVehicleModal() {
        document.getElementById('modalEditVehicle').style.display = 'none';
    }

    // Tutup modal jika klik di luar area konten
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalEditVehicle');
        if (e.target === modal) {
            closeEditVehicleModal();
        }
    });
</script>
@endsection