@extends('layouts.app')

@section('title', 'Cari Lokasi SPKLU')

@section('content')
<!-- Memuat CSS & JS Leaflet secara Gratis (Tanpa API Key) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="dashboard-container" style="max-width: 1100px; margin: 0 auto; padding: 20px;">
    
    <a href="/dashboard" style="color: #64748b; text-decoration: none; font-weight: 600; font-size: 14px;">← Kembali ke Dashboard</a>

    <h2 style="font-size: 22px; color: #1e293b; margin-top: 15px; margin-bottom: 20px;">🗺️ Peta & Daftar SPKLU Terdekat</h2>

    <!-- Grid Konten: Kiri Daftar Stasiun, Kanan Peta Interaktif -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        
        <!-- Kolom Kiri: Daftar Lokasi & Filter -->
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <form action="{{ route('locations.index') }}" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lokasi atau alamat..." style="flex: 1; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px;">
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer;">Cari</button>
            </form>

            <div style="display: flex; flex-direction: column; gap: 12px; max-height: 500px; overflow-y: auto;">
                @forelse($locations as $loc)
                    <div style="background: white; padding: 15px 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <h4 style="margin: 0 0 5px 0; color: #1e293b; font-size: 16px;">{{ $loc->nama_lokasi }}</h4>
                            <span style="font-size: 11px; padding: 3px 8px; border-radius: 20px; font-weight: bold; background: {{ $loc->status == 'aktif' ? '#d1fae5; color: #065f46;' : '#fee2e2; color: #991b1b;' }}; text-transform: uppercase;">
                                {{ str_replace('_', ' ', $loc->status) }}
                            </span>
                        </div>
                        <p style="margin: 0 0 10px 0; font-size: 13px; color: #64748b;">📍 {{ $loc->alamat }}</p>
                        <p style="margin: 0 0 12px 0; font-size: 12px; color: #475569;">🕒 Jam Operasional: {{ $loc->jam_operasional }}</p>
                        
                        <!-- Tombol Navigasi Menuju Stasiun -->
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $loc->latitude }},{{ $loc->longitude }}" target="_blank" style="background: #0284c7; color: white; text-decoration: none; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; display: inline-block;">
                            🧭 Navigasi ke Lokasi
                        </a>
                    </div>
                @empty
                    <p style="color: #64748b; font-style: italic;">Belum ada stasiun SPKLU yang terdaftar.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom Kanan: Tampilan Peta Interaktif Leaflet -->
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; height: 550px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); z-index: 1;">
            <div id="map" style="width: 100%; height: 100%;"></div>
        </div>

    </div>
</div>

<!-- Skrip Inisialisasi Peta Leaflet dengan Pelacakan Lokasi (Geolocation) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Inisialisasi peta awal (Kota Yogyakarta)
        const map = L.map('map').setView([-7.7956, 110.3695], 13); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Ambil data lokasi SPKLU dari database
        const locations = @json($locations);
        const spkluMarkers = [];

        // 2. Pasang semua marker SPKLU ke peta
        locations.forEach(loc => {
            if(loc.latitude && loc.longitude) {
                const marker = L.marker([loc.latitude, loc.longitude]).addTo(map);
                marker.bindPopup(`
                    <div style="text-align: center; padding: 5px;">
                        <strong style="font-size: 14px;">${loc.nama_lokasi}</strong><br>
                        <span style="font-size: 12px; color: #64748b;">${loc.alamat}</span><br><br>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=${loc.latitude},${loc.longitude}" target="_blank" style="background: #10b981; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; display: inline-block;">🧭 Buka Navigasi</a>
                    </div>
                `);
                
                spkluMarkers.push({
                    data: loc,
                    marker: marker,
                    latlng: L.latLng(loc.latitude, loc.longitude)
                });
            }
        });

        // 3. Deteksi GPS Pengguna secara Otomatis
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const userLocation = L.latLng(position.coords.latitude, position.coords.longitude);

                // Geser peta ke lokasi Anda
                map.flyTo(userLocation, 14);

                // Tambahkan marker lingkaran biru untuk posisi Pengguna
                L.circleMarker(userLocation, {
                    color: '#2563eb',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.8,
                    radius: 8
                }).addTo(map).bindPopup("<b>📍 Posisi Anda Saat Ini</b>").openPopup();

                // 4. Hitung dan cari SPKLU terdekat
                if (spkluMarkers.length > 0) {
                    let nearestLoc = null;
                    let minDistance = Infinity;

                    spkluMarkers.forEach(item => {
                        const distanceInMeters = map.distance(userLocation, item.latlng);
                        if (distanceInMeters < minDistance) {
                            minDistance = distanceInMeters;
                            nearestLoc = item;
                        }
                    });

                    // Jika SPKLU terdekat ketemu, buka pop-up nya secara otomatis
                    if (nearestLoc) {
                        setTimeout(() => {
                            nearestLoc.marker.openPopup();
                        }, 1500);
                    }
                }

            }, function(error) {
                console.log("GPS Ditolak: ", error.message);
            });
        }
    });
</script>
@endsection