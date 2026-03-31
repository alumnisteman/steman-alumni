@props(['locations', 'nationalCount', 'internationalCount'])

<div class="card border-0 shadow-sm glass-card overflow-hidden">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-geo-alt-fill text-danger me-2"></i>Persebaran Alumni 
            <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fw-normal" style="font-size: 0.7rem;">Nasional & Internasional</span>
        </h5>
        <div class="d-flex gap-2">
            <button id="zoom-indo-{{ $attributes->get('id', 'main') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold border-2">Fokus Indonesia</button>
            <button id="zoom-world-{{ $attributes->get('id', 'main') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Dunia</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="alumni-map-{{ $attributes->get('id', 'main') }}" style="height: {{ $attributes->get('height', '400px') }}; width: 100%;"></div>
    </div>
    <div class="card-footer bg-transparent border-0 pb-3 px-4 d-flex justify-content-between align-items-center">
        <p class="small text-muted mb-0">
            <i class="bi bi-info-circle me-1"></i> Area berwarna menunjukkan konsentrasi alumni di berbagai penjuru dunia.
        </p>
        <div class="text-end">
            <span class="badge bg-danger rounded-pill me-1">{{ $nationalCount }} Nasional</span>
            <span class="badge bg-primary rounded-pill">{{ $internationalCount }} Internasional</span>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .custom-div-icon div {
        transition: all 0.3s ease;
    }
    .custom-div-icon:hover div {
        transform: scale(1.5);
        filter: brightness(1.2);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mapId = 'alumni-map-{{ $attributes->get('id', 'main') }}';
        const hasInt = {{ $internationalCount > 0 ? 'true' : 'false' }};
        const initialView = hasInt ? [20, 100] : [-2.5, 118];
        const initialZoom = hasInt ? 2 : 5;

        const map = L.map(mapId).setView(initialView, initialZoom);
        
        const baseTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const alumniData = @json($locations);
        
        const getMarkerIcon = (isInternational) => {
            return L.divIcon({
                className: 'custom-div-icon',
                html: `<div style='background-color:${isInternational ? "#4361ee" : "#ef233c"}; width:12px; height:12px; border-radius:50%; border:2px solid white; box-shadow:0 0 8px rgba(0,0,0,0.4);'></div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });
        };

        const idBounds = { lat: [-11, 6], lng: [95, 141] };
        
        alumniData.forEach(function(alumni) {
            if (alumni.latitude && alumni.longitude) {
                const isInt = alumni.is_international;
                
                L.marker([alumni.latitude, alumni.longitude], { icon: getMarkerIcon(isInt) })
                 .addTo(map)
                 .bindPopup(`
                    <div class="p-1" style="min-width: 150px;">
                        <div class="badge ${isInt ? 'bg-primary' : 'bg-danger'} mb-2" style="font-size: 0.6rem;">${isInt ? 'Internasional' : 'Nasional'}</div>
                        <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">${alumni.name}</h6>
                        <div class="small text-muted mb-2">${alumni.jurusan} - ${alumni.tahun_lulus}</div>
                        <button class="btn btn-sm btn-light w-100 py-1 fw-bold" style="font-size:0.65rem;">Profil Lengkap</button>
                    </div>
                 `);
            }
        });

        // Zoom Controls
        document.getElementById('zoom-indo-{{ $attributes->get('id', 'main') }}').addEventListener('click', () => map.setView([-2.5, 118], 5));
        document.getElementById('zoom-world-{{ $attributes->get('id', 'main') }}').addEventListener('click', () => map.setView([20, 100], 2));

        // Theme Sync
        const syncMapTheme = () => {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            if (isDark) {
                baseTiles.setUrl('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png');
            } else {
                baseTiles.setUrl('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
            }
        };
        syncMapTheme();
        
        // Listen for theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => setTimeout(syncMapTheme, 250));
        }
    });
</script>
@endpush
