@extends('layouts.app')

@section('content')
<style>
    #globeViz {
        width: 100%;
        height: 80vh;
        background-color: #020617;
        border-radius: 20px;
        overflow: hidden;
        cursor: grab;
    }
    #globeViz:active { cursor: grabbing; }

    .network-stats {
        position: absolute;
        bottom: 40px;
        left: 40px;
        z-index: 10;
        pointer-events: none;
    }

    .stat-card {
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 20px;
        border-radius: 15px;
        color: white;
        margin-bottom: 10px;
        min-width: 200px;
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
        opacity: 0.6;
    }

    .globe-tooltip {
        background: rgba(0,0,0,0.8);
        color: #fff;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        pointer-events: none;
    }

    .header-network {
        position: absolute;
        top: 40px;
        left: 40px;
        z-index: 10;
        color: white;
    }
</style>

<div class="container-fluid py-4" style="background: #020617; min-height: 90vh; position: relative;">
    <div class="header-network">
        <h2 class="fw-black mb-1 opacity-90">GLOBAL NETWORK</h2>
        <p class="text-primary small fw-bold mb-0" style="letter-spacing: 3px;">STEMAN ALUMNI MESH</p>
    </div>

    <div id="globeViz"></div>

    <div class="network-stats">
        <div class="stat-card">
            <span class="stat-value text-primary">{{ $nationalCount }}</span>
            <span class="stat-label">National Presence</span>
        </div>
        <div class="stat-card">
            <span class="stat-value" style="color: #4cc9f0;">{{ $internationalCount }}</span>
            <span class="stat-label">International Reach</span>
        </div>
        <a href="{{ route('alumni.index') }}" class="btn btn-outline-light rounded-pill px-4 mt-3" style="pointer-events: auto;">
            <i class="bi bi-person-lines-fill me-2"></i>Back to Directory
        </a>
    </div>

    <!-- Instruction Overlay -->
    <div class="position-absolute translate-middle-x start-50 bottom-0 mb-4 text-white opacity-50 small">
        <i class="bi bi-mouse me-1"></i> Klik & Seret untuk memutar bola dunia
    </div>
</div>

<script src="//unpkg.com/three"></script>
<script src="//unpkg.com/globe.gl"></script>
<script>
    const alumniData = {!! json_encode($locations) !!};
    
    // Process data for globe
    const gData = alumniData.map(loc => ({
        lat: loc.latitude,
        lng: loc.longitude,
        name: loc.name,
        major: loc.jurusan,
        year: loc.tahun_lulus,
        size: 0.5,
        color: loc.is_international ? '#4cc9f0' : '#4361ee'
    }));

    const world = Globe()
        (document.getElementById('globeViz'))
        .globeImageUrl('//unpkg.com/three-globe/example/img/earth-night.jpg')
        .bumpImageUrl('//unpkg.com/three-globe/example/img/earth-topology.png')
        .backgroundImageUrl(null)
        .backgroundColor('#020617')
        .showAtmosphere(true)
        .atmosphereColor('#3f37c9')
        .atmosphereDaylightAlpha(0.1)
        .pointsData(gData)
        .pointAltitude(0.05)
        .pointColor('color')
        .pointRadius(0.12)
        .pointsMerge(true)
        .pointLabel(d => `
            <div class="globe-tooltip">
                <b style="color: #4cc9f0">${d.name}</b><br/>
                ${d.major} - Angkatan ${d.year}
            </div>
        `)
        .onPointClick(d => {
            // Auto focus on click
            world.pointOfView({ lat: d.lat, lng: d.lng, altitude: 2 }, 1000);
        });

    // Auto-rotate
    world.controls().autoRotate = true;
    world.controls().autoRotateSpeed = 0.5;

    // Responsive resize
    window.addEventListener('resize', () => {
        world.width(window.innerWidth);
        world.height(window.innerHeight * 0.8);
    });
</script>
@endsection
