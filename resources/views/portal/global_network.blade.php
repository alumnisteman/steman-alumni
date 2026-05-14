@extends('layouts.app')

@section('title', 'Global Network - STEMAN Alumni')

@push('styles')
<style>
    #globeViz {
        height: 80vh;
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
        border-radius: 20px;
        background: transparent;
        z-index: 1;
        cursor: grab;
    }
    
    #globeViz:active {
        cursor: grabbing;
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

    .globe-tooltip {
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(59, 130, 246, 0.5);
        color: white;
        padding: 10px;
        border-radius: 8px;
        font-family: sans-serif;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-12 map-container">
    <div class="mb-8 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            STEMAN <span class="text-blue-500">Global Network</span>
        </h1>
        <p class="text-gray-400 max-w-2xl mx-auto">
            Menghubungkan masa lalu, membangun masa depan. Visualisasi jaringan Forum Silaturahmi Alumni Steman Ternate di seluruh belahan dunia.
        </p>
    </div>

    <div class="map-card relative">
        <div id="globeViz"></div>
        
        <div class="map-overlay-info d-none d-md-block">
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
                
                <hr class="border-gray-700 my-3">
                
                <a href="{{ route('matchmaking.index') }}" class="btn btn-primary w-100 rounded-pill fw-bold" style="background: linear-gradient(45deg, #ec4899, #8b5cf6); border: none; box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4);">
                    <i class="bi bi-fire me-2"></i> Cari Partner (Matchmaking)
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="//unpkg.com/globe.gl"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('globeViz');
        
        // Initialize 3D Globe with explicit dimensions and centered POV
        const world = Globe()(container)
            .width(container.clientWidth)
            .height(container.clientHeight)
            .globeImageUrl('//unpkg.com/three-globe/example/img/earth-night.jpg')
            .bumpImageUrl('//unpkg.com/three-globe/example/img/earth-topology.png')
            .backgroundImageUrl('//unpkg.com/three-globe/example/img/night-sky.png')
            .showAtmosphere(true)
            .atmosphereColor('#3b82f6')
            .atmosphereAltitude(0.15)
            .pointOfView({ lat: 0.7856, lng: 127.3719, altitude: 2.8 }); // altitude increased to 2.8 for better framing

        // Resize globe when window resizes
        window.addEventListener('resize', () => {
            world.width(container.clientWidth).height(container.clientHeight);
        });

        fetch("{{ route('api.map.data') }}")
            .then(res => res.json())
            .then(data => {
                if(data.success && data.alumni) {
                    const origin = data.origin;
                    document.getElementById('connection-count').innerText = data.alumni.length;

                    // 1. Prepare Arcs (Lines from Hub to Alumni)
                    const arcsData = data.alumni.map(alumni => ({
                        startLat: origin.lat,
                        startLng: origin.lng,
                        endLat: alumni.lat,
                        endLng: alumni.lng,
                        color: ['rgba(255, 255, 255, 0)', 'rgba(59, 130, 246, 1)']
                    }));

                    world
                        .arcsData(arcsData)
                        .arcColor('color')
                        .arcDashLength(0.4)
                        .arcDashGap(4)
                        .arcDashInitialGap(() => Math.random() * 5)
                        .arcDashAnimateTime(1500)
                        .arcStroke(0.5);

                    // 2. Prepare Labels/Markers
                    // Add Hub
                    const labelsData = [{
                        lat: origin.lat,
                        lng: origin.lng,
                        name: origin.name,
                        city: 'Ternate',
                        isHub: true,
                        size: 2,
                        color: '#fbbf24' // Warning/Gold
                    }];

                    // Add Alumni
                    data.alumni.forEach(alumni => {
                        labelsData.push({
                            lat: alumni.lat,
                            lng: alumni.lng,
                            name: alumni.name,
                            city: alumni.city,
                            major: alumni.major,
                            year: alumni.year,
                            isHub: false,
                            size: 1,
                            color: '#3b82f6' // Blue
                        });
                    });

                    world
                        .labelsData(labelsData)
                        .labelLat(d => d.lat)
                        .labelLng(d => d.lng)
                        .labelText(d => d.isHub ? 'HUB' : '') // Only show text for Hub, dots for alumni
                        .labelSize(d => d.size)
                        .labelDotRadius(d => d.isHub ? 1 : 0.5)
                        .labelColor(d => d.color)
                        .labelResolution(2)
                        .labelLabel(d => {
                            if(d.isHub) return `<div class="globe-tooltip"><b>${d.name}</b><br>Central Server</div>`;
                            return `
                                <div class="globe-tooltip text-center">
                                    <div class="font-bold text-white mb-1">${d.name}</div>
                                    <div class="text-xs text-gray-400 mb-1">${d.major} - Lulus ${d.year}</div>
                                    <div class="text-xs text-blue-400"><i class="bi bi-geo-alt-fill"></i> ${d.city}</div>
                                </div>
                            `;
                        });

                    // Auto-rotate the globe slowly
                    world.controls().autoRotate = true;
                    world.controls().autoRotateSpeed = 0.5;
                }
            });
    });
</script>
@endpush
