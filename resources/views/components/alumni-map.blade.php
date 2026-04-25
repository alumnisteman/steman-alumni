@props(['locations', 'nationalCount', 'internationalCount'])

<div class="card border-0 shadow-sm glass-card" style="border-radius: 20px; overflow: hidden;">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-geo-alt-fill text-danger me-2"></i>Persebaran Alumni
            <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fw-normal" style="font-size: 0.7rem;">Nasional & Internasional</span>
        </h5>
        <div class="d-flex gap-2">
            <button id="zoom-indo-{{ $attributes->get('id', 'main') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold border-2" aria-label="Fokus peta ke Indonesia">Fokus Indonesia</button>
            <button id="zoom-world-{{ $attributes->get('id', 'main') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" aria-label="Fokus peta ke seluruh dunia">Dunia</button>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" media="print" onload="this.media='all'" />
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"></noscript>
<style>
    /* JANGAN override z-index Leaflet internal — biarkan Leaflet yang kelola */
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
(function() {
    const mapId = 'alumni-map-{{ $attributes->get('id', 'main') }}';
    const el = document.getElementById(mapId);
    if (!el) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                initMap();
                observer.unobserve(el);
            }
        });
    }, { rootMargin: '200px' });

    observer.observe(el);

    function initMap() {
        if (typeof L === 'undefined') {
            console.error('Leaflet not loaded');
            el.innerHTML = '<div class="p-4 text-center text-muted">Peta tidak dapat dimuat. Silakan refresh halaman.</div>';
            return;
        }

        const hasInt = {{ $internationalCount > 0 ? 'true' : 'false' }};
        const initialView = hasInt ? [5, 118] : [-2.5, 118];
        const initialZoom = hasInt ? 3 : 5;

        const map = L.map(mapId, {
            scrollWheelZoom: false,
            zoomControl: true
        }).setView(initialView, initialZoom);

        // Gunakan CartoDB Light
        const tileLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> &copy; <a href="https://carto.com">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        const alumniData = @json($locations ?? []);

        if (Array.isArray(alumniData) && alumniData.length > 0) {
            alumniData.forEach(function(alumni) {
                if (alumni.latitude && alumni.longitude) {
                    const isInt = alumni.is_international;
                    const color = isInt ? '#3b82f6' : '#ef4444';
                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div style="background:${color};width:14px;height:14px;border-radius:50%;border:2px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.4);"></div>`,
                        iconSize: [14, 14],
                        iconAnchor: [7, 7]
                    });
                    L.marker([alumni.latitude, alumni.longitude], { 
                        icon, 
                        title: alumni.name,
                        alt: 'Lokasi Alumni: ' + alumni.name 
                    })
                        .addTo(map)
                        .bindPopup(`
                            <div style="min-width:150px;padding:4px;">
                                <span class="badge" style="background:${color};font-size:0.6rem;">${isInt ? 'Internasional' : 'Nasional'}</span>
                                <h6 style="font-weight:700;margin:6px 0 2px;font-size:0.85rem;">${alumni.name}</h6>
                                <div style="font-size:0.8rem;color:#666;">${alumni.major || ''} ${alumni.graduation_year ? '· ' + alumni.graduation_year : ''}</div>
                            </div>
                        `);
                }
            });
        }

        setTimeout(() => { map.invalidateSize(); }, 300);

        // Zoom controls
        document.getElementById('zoom-indo-{{ $attributes->get('id', 'main') }}').addEventListener('click', () => map.setView([-2.5, 118], 5));
        document.getElementById('zoom-world-{{ $attributes->get('id', 'main') }}').addEventListener('click', () => map.setView([5, 118], 3));

        // Dark mode support
        document.addEventListener('themeChanged', function() {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const darkUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
            const lightUrl = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
            map.eachLayer(layer => { if (layer.setUrl) layer.setUrl(isDark ? darkUrl : lightUrl); });
        });
    }
})();
</script>
@endpush
