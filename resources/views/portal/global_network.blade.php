@extends('layouts.portal')

@section('title', 'Global Network - STEMAN Alumni')

@base_css
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map {
        height: 80vh;
        width: 100%;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        background: #1a1a1a;
        z-index: 1;
    }

    .map-container {
        position: relative;
        padding: 2rem;
        background: radial-gradient(circle at top right, rgba(29, 78, 216, 0.1), transparent);
    }

    .map-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        overflow: hidden;
    }

    .map-overlay-info {
        position: absolute;
        top: 40px;
        left: 40px;
        z-index: 1000;
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(12px);
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        max-width: 300px;
        color: white;
    }

    /* Pulse Animation for Origin Hub */
    .hub-icon {
        width: 12px;
        height: 12px;
        background: #3b82f6;
        border-radius: 50%;
        box-shadow: 0 0 0 rgba(59, 130, 246, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }

    /* Alumni Marker Styles */
    .alumni-marker {
        background: white;
        border: 2px solid #3b82f6;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
    }

    .leaflet-popup-content-wrapper {
        background: rgba(15, 23, 42, 0.95) !important;
        color: white !important;
        border-radius: 12px !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .leaflet-popup-tip {
        background: rgba(15, 23, 42, 0.95) !important;
    }
</style>
@end_css

@section('content')
<div class="container mx-auto px-4 py-12 map-container">
    <div class="mb-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
            STEMAN <span class="text-blue-500">Global Network</span>
        </h1>
        <p class="text-gray-400 max-w-2xl mx-auto">
            Menghubungkan masa lalu, membangun masa depan. Visualisasi jaringan alumni SMKN 2 Ternate di seluruh belahan dunia.
        </p>
    </div>

    <div class="map-card relative">
        <div id="map"></div>
        
        <div class="map-overlay-info hidden md:block">
            <h3 class="font-bold text-lg mb-2">STEMAN Alumni Mesh</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="hub-icon"></div>
                    <span class="text-sm text-gray-300">Hub: SMKN 2 Ternate</span>
                </div>
                <p class="text-xs text-gray-400">
                    Setiap garis melambangkan koneksi emosional dan profesional alumni kembali ke almamater tercinta.
                </p>
                <div class="pt-2">
                    <div class="text-xs font-semibold text-blue-400 mb-1">TOTAL KONEKSI</div>
                    <div class="text-2xl font-bold" id="connection-count">0</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@base_js
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map
        const map = L.map('map', {
            zoomControl: false,
            attributionControl: false
        }).setView([0.7856, 127.3719], 3);

        // Add Dark Mode Tiles
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19
        }).addTo(map);

        // Fetch Data from API
        fetch("{{ route('api.map-data') }}")
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const origin = data.origin;
                    document.getElementById('connection-count').innerText = data.alumni.length;

                    // Add Origin Hub Marker
                    const hubIcon = L.divIcon({
                        className: 'hub-icon',
                        iconSize: [12, 12]
                    });
                    L.marker([origin.lat, origin.lng], { icon: hubIcon })
                        .addTo(map)
                        .bindPopup(`<b>${origin.name}</b><br>The Central Hub`);

                    // Add Alumni Markers & Mesh Lines
                    data.alumni.forEach(alumni => {
                        const alumniIcon = L.divIcon({
                            className: 'alumni-marker',
                            html: `<img src="${alumni.avatar}" style="width:100%;height:100%;object-fit:cover;">`,
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        });

                        // Marker
                        L.marker([alumni.lat, alumni.lng], { icon: alumniIcon })
                            .addTo(map)
                            .bindPopup(`
                                <div class="text-center p-2">
                                    <img src="${alumni.avatar}" class="w-12 h-12 rounded-full mx-auto mb-2 border-2 border-blue-500">
                                    <div class="font-bold">${alumni.name}</div>
                                    <div class="text-xs text-gray-400">${alumni.major} - Lulus ${alumni.year}</div>
                                    <div class="text-xs text-blue-400 mt-1"><i class="fas fa-map-marker-alt"></i> ${alumni.city}</div>
                                </div>
                            `);

                        // Create Mesh Line (Curved effect using opacity/weight)
                        const linePoints = [
                            [origin.lat, origin.lng],
                            [alumni.lat, alumni.lng]
                        ];
                        
                        L.polyline(linePoints, {
                            color: '#3b82f6',
                            weight: 1.5,
                            opacity: 0.3,
                            dashArray: '5, 10',
                            lineCap: 'round'
                        }).addTo(map);
                    });

                    // Refit map bounds if there are alumni
                    if(data.alumni.length > 0) {
                        const group = new L.featureGroup(data.alumni.map(a => L.marker([a.lat, a.lng])));
                        map.fitBounds(group.getBounds().pad(0.5));
                    }
                }
            });
    });
</script>
@end_js
