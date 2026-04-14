@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapViz {
        width: 100%;
        height: 80vh;
        background-color: #020617;
        border-radius: 20px;
        overflow: hidden;
        border: 2px solid rgba(255,255,255,0.05);
        z-index: 1;
    }
    
    .network-stats {
        position: absolute;
        bottom: 40px;
        left: 40px;
        z-index: 1000;
        pointer-events: none;
    }

    .stat-card {
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 15px 20px;
        border-radius: 15px;
        color: white;
        margin-bottom: 10px;
        min-width: 200px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        display: block;
    }

    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        opacity: 0.7;
    }

    .header-network {
        position: absolute;
        top: 20px;
        left: 50px;
        z-index: 1000;
        color: white;
        text-shadow: 0 2px 8px rgba(0,0,0,0.8);
        background: rgba(0, 0, 0, 0.5);
        padding: 10px 20px;
        border-radius: 15px;
        backdrop-filter: blur(4px);
    }
    
    .leaflet-popup-content-wrapper {
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(8px);
        color: white;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
    }
    .leaflet-popup-tip {
        background: rgba(15, 23, 42, 0.95);
    }
    
    .marker-pin {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
    .marker-national { background: #ef233c; } 
    .marker-international { background: #4361ee; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" style="background: #020617; min-height: 90vh; position: relative;">
    <div class="position-relative">
        <div class="header-network">
            <h2 class="fw-black mb-1 opacity-90">GLOBAL NETWORK</h2>
            <p class="text-primary small fw-bold mb-0" style="letter-spacing: 3px;">STEMAN ALUMNI MESH</p>
        </div>

        <div id="mapViz"></div>

        <div class="network-stats">
            <div class="stat-card">
                <span class="stat-value text-danger" id="stat-national">{{ $nationalCount }}</span>
                <span class="stat-label">National Presence</span>
            </div>
            <div class="stat-card">
                <span class="stat-value text-primary" id="stat-international">{{ $internationalCount }}</span>
                <span class="stat-label">International Reach</span>
            </div>
            <a href="{{ route('alumni.index') }}" class="btn btn-outline-light rounded-pill px-4 mt-3" style="pointer-events: auto; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
                <i class="bi bi-person-lines-fill me-2"></i>Back to Directory
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Leaflet map centered on Indonesia
        const hasInt = {{ $internationalCount > 0 ? 'true' : 'false' }};
        const initialView = hasInt ? [10, 115] : [-2.5, 118];
        const initialZoom = hasInt ? 3 : 5;

        const map = L.map('mapViz', {
            zoomControl: false
        }).setView(initialView, initialZoom);
        
        L.control.zoom({ position: 'topright' }).addTo(map);

        // We use OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        const alumniData = @json($locations);
        
        const getMarkerHTML = (isInternational) => {
            const className = isInternational ? 'marker-international' : 'marker-national';
            return L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="marker-pin ${className}"></div>`,
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });
        };

        if (alumniData && alumniData.length > 0) {
            alumniData.forEach(function(alumni) {
                if (alumni.latitude && alumni.longitude) {
                    const isInt = alumni.is_international;
                    
                    L.marker([alumni.latitude, alumni.longitude], { icon: getMarkerHTML(isInt) })
                     .addTo(map)
                     .bindPopup(`
                        <div class="p-2" style="min-width: 180px;">
                            <div class="badge ${isInt ? 'bg-primary' : 'bg-danger'} mb-2" style="font-size: 0.65rem; letter-spacing: 1px;">
                                ${isInt ? '<i class="bi bi-globe-americas me-1"></i> Luar Negeri' : '<i class="bi bi-geo-alt-fill me-1"></i> Dalam Negeri'}
                            </div>
                            <h6 class="fw-bold text-white mb-1" style="font-size: 0.95rem;">${alumni.name}</h6>
                            <div class="small text-light opacity-75 mb-1">${alumni.major}</div>
                            <div class="small text-light opacity-75">Angkatan ${alumni.graduation_year}</div>
                        </div>
                     `);
                }
            });
        }
    });
</script>
@endpush
