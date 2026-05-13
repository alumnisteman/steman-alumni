@extends('layouts.app')

@push('styles')
<style>
    #mapViz {
        width: 100vw;
        height: 100vh;
        border-radius: 0;
        overflow: hidden;
        border: none;
        z-index: 1;
        cursor: grab;
    }
    #mapViz:active {
        cursor: grabbing;
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
        left: 30px; /* Shifted slightly */
        z-index: 1000;
        color: white;
        text-shadow: 0 2px 8px rgba(0,0,0,0.8);
        background: linear-gradient(to right, rgba(0,0,0,0.6), transparent);
        padding: 10px 25px;
        border-radius: 0 50px 50px 0;
        backdrop-filter: blur(8px);
        border-left: 4px solid #4f46e5;
    }

    /* AI Insight Panel */
    #ai-insight-panel {
        position: absolute;
        top: 20px;
        right: 50px;
        width: 350px;
        z-index: 1000;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(67, 97, 238, 0.4);
        box-shadow: 0 0 20px rgba(67, 97, 238, 0.2);
        border-radius: 15px;
        color: white;
        transition: all 0.5s ease-in-out;
        opacity: 0;
        transform: translateX(50px);
        pointer-events: none;
    }
    #ai-insight-panel.active {
        opacity: 1;
        transform: translateX(0);
    }
    .ai-typing::after {
        content: '|';
        animation: blink 1s step-end infinite;
    }
    .glass-card {
        background: rgba(15, 23, 42, 0.65); /* Darker for better contrast */
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.5);
        color: white;
        max-width: 280px;
    }

    /* Cyberpunk HUD Styles */
    .hud-corner {
        position: fixed;
        width: 100px;
        height: 100px;
        border: 2px solid rgba(67, 97, 238, 0.3);
        z-index: 1001;
        pointer-events: none;
    }
    .hud-tl { top: 20px; left: 20px; border-right: none; border-bottom: none; }
    .hud-tr { top: 20px; right: 20px; border-left: none; border-bottom: none; }
    .hud-bl { bottom: 20px; left: 20px; border-right: none; border-top: none; }
    .hud-br { bottom: 20px; right: 20px; border-left: none; border-top: none; }

    .hud-data {
        position: fixed;
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-size: 12px; /* Increased size */
        color: rgba(6, 231, 255, 0.9); /* Brighter Cyan */
        z-index: 1001;
        pointer-events: none;
        text-transform: uppercase;
        background: rgba(15, 23, 42, 0.3);
        padding: 8px 12px;
        border-radius: 8px;
        backdrop-filter: blur(4px);
        letter-spacing: 1px;
    }

    .chat-bubble {
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(67, 97, 238, 0.5);
        border-radius: 12px;
        padding: 8px 12px;
        color: white;
        font-size: 11px;
        max-width: 150px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        animation: float-up 4s ease-out forwards;
        pointer-events: none;
    }

    @keyframes float-up {
        0% { margin-top: 0px; opacity: 0; }
        15% { margin-top: -20px; opacity: 1; }
        85% { margin-top: -50px; opacity: 1; }
        100% { margin-top: -65px; opacity: 0; }
    }

    /* Pulse Feed Panel */
    #pulse-feed {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 500px;
        z-index: 2000;
        pointer-events: none;
    }
    .feed-item {
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(67, 97, 238, 0.4);
        border-radius: 50px;
        padding: 10px 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        margin-bottom: 10px;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        opacity: 0;
        transform: translateY(20px);
    }
    .feed-item.active {
        opacity: 1;
        transform: translateY(0);
    }
    .feed-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid #4f46e5;
        object-fit: cover;
    }

    .fixed { position: fixed; }
    .top-20 { top: 5rem; }
    .left-6 { left: 1.5rem; }
    .bottom-10 { bottom: 2.5rem; }
    .z-20 { z-index: 20; }
    .space-y-4 > * + * { margin-top: 1rem; }
    .space-y-3 > * + * { margin-top: 0.75rem; }
    .gap-4 { gap: 1rem; }
    .transform { transform: translateX(0); }
    .translate-x-center { transform: translateX(-50%); }

    @keyframes blink {
        50% { opacity: 0; }
    }

    @keyframes pulse-avatar {
        0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.8; }
        100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
    }

    .avatar-marker {
        transition: transform 0.3s ease;
    }
    .avatar-marker:hover {
        transform: scale(1.5);
        z-index: 9999;
    }

    /* Earth Controls Professional Design */
    .earth-controls-container {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2000;
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: center;
        padding: 10px;
        width: auto;
        max-width: 95vw;
    }

    .btn-earth {
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(67, 97, 238, 0.4);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 11px;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .btn-earth:hover {
        background: #4361ee;
        border-color: #4cc9f0;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        color: white;
    }

    .btn-earth i, .btn-earth span {
        pointer-events: none;
    }

    .btn-earth.active {
        background: #4361ee;
        border-color: #4cc9f0;
    }

    /* Color variations */
    .btn-earth.btn-orange { color: #fb923c; border-color: rgba(251, 146, 60, 0.4); }
    .btn-earth.btn-purple { color: #c084fc; border-color: rgba(192, 132, 252, 0.4); }
    .btn-earth.btn-blue { color: #60a5fa; border-color: rgba(96, 165, 250, 0.4); }
    .btn-earth.btn-emerald { color: #34d399; border-color: rgba(52, 211, 153, 0.4); }
    .btn-earth.btn-teal { color: #2dd4bf; border-color: rgba(45, 212, 191, 0.4); }
    
    @media (max-width: 768px) {
        .earth-controls-container {
            flex-wrap: nowrap; /* Keep in one line */
            bottom: 15px;
            gap: 8px;
            overflow-x: auto;
            width: 100%;
            padding: 10px 20px;
            justify-content: flex-start;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .earth-controls-container::-webkit-scrollbar { display: none; }
        
        .btn-earth {
            padding: 10px;
            border-radius: 50%; /* Circle for mobile icons */
            min-width: 42px;
            height: 42px;
            justify-content: center;
        }
        .btn-earth span {
            display: none; /* Hide labels on mobile */
        }
        .btn-earth i {
            font-size: 16px;
            margin: 0;
        }

        /* Hide non-essential HUD on mobile */
        .hud-data, .hud-corner {
            display: none;
        }

        /* Adjust Stats for mobile */
        .fixed.top-20.left-6 {
            top: 80px;
            left: 10px;
            transform: scale(0.85);
            transform-origin: top left;
        }
        
        .header-network {
            left: 0;
            top: 0;
            width: 100%;
            border-radius: 0;
            text-align: center;
            padding: 15px;
            border-left: none;
            border-bottom: 2px solid #4f46e5;
        }
        
        #ai-insight-panel {
            right: 10px;
            left: 10px;
            width: auto;
            top: 70px;
            max-height: 30%;
            overflow-y: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0 m-0" style="background: #020617; height: 100vh; width: 100vw; position: fixed; top: 0; left: 0; z-index: 9999; overflow: hidden;">
    <!-- HUD ELEMENTS -->
    <div class="hud-corner hud-tl"></div>
    <div class="hud-corner hud-tr"></div>
    <div class="hud-corner hud-bl"></div>
    <div class="hud-corner hud-br"></div>
    
    <div class="hud-data" style="top: 350px; right: 50px; text-align: right; border-right: 3px solid #06e7ff;">
        SECTOR: [GLOBAL_TERNATE_HUB]<br>
        SAT_LINK: <span style="color: #10b981;">ENCRYPTED_ACTIVE</span><br>
        UPLINK: {{ now()->format('H:i:s') }} UTC+9
    </div>
    
    <div class="hud-data" style="bottom: 120px; right: 50px; text-align: right; border-right: 3px solid #10b981;">
        ALUMNI_CONNECTED: {{ number_format($nationalCount + $internationalCount) }}<br>
        CORE_STABILITY: 99.9%<br>
        SEC_LEVEL: ALPHA-6
    </div>

    <div class="position-relative h-100 w-100">
        <div class="header-network">
            <h2 class="fw-black mb-1 opacity-90" style="background: linear-gradient(135deg, #4f46e5, #06b6d4); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">STEMAN EARTH</h2>
            <p class="text-info small fw-bold mb-0" style="letter-spacing: 3px;"><i class="bi bi-globe-americas me-1"></i> GLOBAL NEXUS SYSTEM</p>
        </div>

        <!-- AI Insight Panel -->
        <div id="ai-insight-panel" class="p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="spinner-grow spinner-grow-sm text-primary me-2" role="status"></div>
                <h6 class="fw-bold mb-0 text-primary" style="letter-spacing: 1px;">STEMAN-AI INTEL</h6>
            </div>
            <h5 id="ai-city-name" class="fw-black text-white mb-2">SCANNING...</h5>
            <div id="ai-city-stats" class="small text-warning mb-3 fw-bold"></div>
            <p id="ai-insight-text" class="small opacity-75 mb-0" style="line-height: 1.6;"></p>
        </div>

        <!-- NEW: Live Pulse Feed Panel -->
        <div id="pulse-feed">
            <div id="pulse-feed-container"></div>
        </div>

        <!-- Radar Panel -->
        <div id="radar-panel" class="p-4" style="position: absolute; bottom: 120px; left: 50px; width: 350px; z-index: 1000; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(12px); border: 1px solid rgba(16, 185, 129, 0.4); box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); border-radius: 15px; color: white; display: none;">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-radar text-success fs-4 me-2"></i>
                <h6 class="fw-bold mb-0 text-success" style="letter-spacing: 1px;">ALUMNI RADAR</h6>
            </div>
            <p class="small text-white-50 mb-3" id="radar-desc">Temukan rekan alumni di sekitar lokasi Anda saat ini.</p>
            <button id="btn-scan-radar" class="btn btn-success rounded-pill w-100 fw-bold"><i class="bi bi-geo-alt-fill me-2"></i>Scan Sekitar Saya</button>
            <div id="radar-results-container" class="mt-3 d-none">
                <div id="radar-loading" class="text-center py-2 d-none">
                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                    <span class="small ms-2">Menyinkronkan satelit...</span>
                </div>
                <div id="radar-list" class="pe-2" style="max-height: 200px; overflow-y: auto;"></div>
            </div>
        </div>

        <div id="mapViz" style="width: 100%; height: 100%;"></div>

        <!-- Back Button -->
        <a href="{{ route('alumni.dashboard') }}" class="fixed top-6 left-6 z-30 flex items-center gap-2 px-4 py-2 rounded-full glass-card hover:bg-white/10 transition-colors no-underline">
            <i class="fas fa-arrow-left"></i>
            <span class="text-sm font-bold">Dashboard</span>
        </a>

        <!-- Steman Earth Stats & Legend -->
        <div id="stats-panel" class="fixed top-20 left-6 z-20 space-y-3 transition-all duration-500">
            <div class="glass-card p-3 border-l-4 border-blue-500 animate__animated animate__fadeInLeft">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Global Connectivity</h3>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] text-gray-300">National</span>
                    <span class="text-sm font-bold text-white">{{ number_format($nationalCount) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-gray-300">International</span>
                    <span class="text-sm font-bold text-blue-400">{{ number_format($internationalCount) }}</span>
                </div>
            </div>

            <!-- TOP REGIONS LEADERBOARD -->
            <div class="glass-card p-3 border-l-4 border-yellow-500 animate__animated animate__fadeInLeft" style="animation-delay: 0.2s;">
                <h3 class="text-[10px] font-bold text-yellow-500 uppercase tracking-widest mb-2">Top Regions</h3>
                <div class="space-y-2">
                    @foreach($topRegions as $region)
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-yellow-100/90 font-medium truncate max-w-[120px]" style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);">{{ $region->city_name }}</span>
                        <span class="px-2 py-0.5 rounded-full bg-yellow-500/20 text-yellow-400 text-[9px] font-bold">{{ $region->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Earth Controls -->
        <div class="earth-controls-container">
            <button onclick="toggleHeatmap()" class="btn-earth btn-orange">
                <i class="fas fa-fire"></i>
                <span id="heatmapText">Heatmap</span>
            </button>

            <button onclick="toggleConstellations()" class="btn-earth btn-purple">
                <i class="fas fa-star"></i>
                <span id="constellationText">Constellation</span>
            </button>

            <button onclick="flyToHub()" class="btn-earth btn-blue">
                <i class="fas fa-school"></i>
                <span>Almamater</span>
            </button>

            <button id="toggle-autopilot" class="btn-earth btn-emerald">
                <i class="bi bi-airplane-fill"></i>
                <span id="autopilot-text">Auto-Pilot: ON</span>
            </button>

            <button id="toggle-ui" onclick="toggleUIVisibility()" class="btn-earth btn-orange">
                <i class="bi bi-eye-fill"></i>
                <span id="ui-toggle-text">Toggle UI</span>
            </button>

            <button onclick="toggleRadar()" class="btn-earth btn-teal" id="radarBtn">
                <i class="fas fa-satellite-dish"></i>
                <span>Radar</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="//unpkg.com/globe.gl"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alumniData = JSON.parse(decodeURIComponent("{{ rawurlencode(json_encode($locations)) }}"));
        const heatmapDataRaw = JSON.parse(decodeURIComponent("{{ rawurlencode(json_encode($heatmapData)) }}"));
        const liveActivities = JSON.parse(decodeURIComponent("{{ rawurlencode(json_encode($liveActivities)) }}"));
        const jobVacancies = JSON.parse(decodeURIComponent("{{ rawurlencode(json_encode($jobVacancies)) }}"));
        const majorConstellations = JSON.parse(decodeURIComponent("{{ rawurlencode(json_encode($majorConstellations)) }}"));
        const timeCapsules = JSON.parse(decodeURIComponent("{{ rawurlencode(json_encode($timeCapsules)) }}"));
        const liveFeed = JSON.parse(decodeURIComponent("{{ rawurlencode(json_encode($liveFeed)) }}"));
        const HUB_LAT = 0.7935;
        const HUB_LNG = 127.3765;
        let isHeatmapMode = false;
        let isConstellationMode = false;

        // Auto-detect Day/Night based on Ternate Time (UTC+9)
        const hour = new Date(new Date().getTime() + (9 * 60 * 60 * 1000)).getUTCHours();
        const isDayTime = hour >= 6 && hour < 18;
        const globeTexture = isDayTime 
            ? 'https://unpkg.com/three-globe/example/img/earth-blue-marble.jpg' 
            : 'https://unpkg.com/three-globe/example/img/earth-night.jpg';
        
        const HUB_NODE = {
            lat: HUB_LAT,
            lng: HUB_LNG,
            count: 1,
            majors: ['Pusat Server'],
            city: 'Ternate (HUB)',
            isHub: true
        };

        const elem = document.getElementById('mapViz');
        if (!elem) return;

        let attempts = 0;
        function initGlobeMap() {
            if (typeof Globe === 'undefined') {
                attempts++;
                if (attempts < 10) {
                    setTimeout(initGlobeMap, 500);
                    return;
                } else {
                    elem.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center h-100 flex-column text-center p-5">
                            <i class="bi bi-wifi-off text-danger display-4 mb-3"></i>
                            <h4 class="text-white">Gagal Memuat Peta 3D</h4>
                            <p class="text-light opacity-75">Library WebGL gagal dimuat dari server utama. Mohon matikan adblocker atau gunakan koneksi lain.</p>
                        </div>`;
                    return;
                }
            }

        // Group data by City/Coordinate to form dense nodes
        const groupedNodes = {};
        if (alumniData && Array.isArray(alumniData)) {
            alumniData.forEach(alumni => {
                try {
                    if (alumni && alumni.latitude && alumni.longitude) {
                        const lat = parseFloat(alumni.latitude);
                        const lng = parseFloat(alumni.longitude);
                        if (isNaN(lat) || isNaN(lng)) return;

                        const key = `${Math.round(lat*10)/10}_${Math.round(lng*10)/10}`;
                        if (!groupedNodes[key]) {
                            groupedNodes[key] = {
                                lat: lat,
                                lng: lng,
                                count: 0,
                                majors: [],
                                city: (alumni.name || '').includes('Alumni') ? (alumni.name || '').replace('Alumni ', '') : (alumni.city || 'Alumni Hub')
                            };
                        }
                        groupedNodes[key].count++;
                        const major = alumni.major || 'Umum';
                        if (!groupedNodes[key].majors.includes(major) && groupedNodes[key].majors.length < 3) {
                            groupedNodes[key].majors.push(major);
                        }
                    }
                } catch (e) { console.error('Parse error:', e); }
            });
        }

        const nodes = Object.values(groupedNodes);
        
        // Ensure Hub is always visible even if no alumni exactly at hub
        const hasHub = nodes.some(n => Math.abs(n.lat - HUB_LAT) < 0.5 && Math.abs(n.lng - HUB_LNG) < 0.5);
        if (!hasHub) {
            nodes.push(HUB_NODE);
        } else {
            // Mark the existing Ternate node as Hub to make it glow bigger
            const hubNode = nodes.find(n => Math.abs(n.lat - HUB_LAT) < 0.5 && Math.abs(n.lng - HUB_LNG) < 0.5);
            if(hubNode) hubNode.isHub = true;
        }
        
        // Create Arcs from Hub to all nodes (excluding the Hub itself)
        const arcs = nodes
            .filter(node => !node.isHub && (Math.abs(node.lat - HUB_LAT) > 0.2 || Math.abs(node.lng - HUB_LNG) > 0.2))
            .map(node => ({
                startLat: HUB_LAT,
                startLng: HUB_LNG,
                endLat: node.lat,
                endLng: node.lng,
                color: node.lat > -11 && node.lat < 6 && node.lng > 95 && node.lng < 141 ? ['rgba(255, 204, 0, 0.1)', 'rgba(239, 35, 60, 0.8)'] : ['rgba(255, 204, 0, 0.1)', 'rgba(67, 97, 238, 0.8)']
            }));

        try {
            const world = Globe({ 
                waitForGlobeReady: true, 
                animateIn: true,
                rendererConfig: { antialias: true, alpha: true, precision: 'highp' }
            })
                (elem)
                .globeImageUrl(globeTexture)
                .bumpImageUrl('{{ asset("img/earth/earth-topology.png") }}')
                .backgroundImageUrl('{{ asset("img/earth/night-sky.png") }}')
                .showAtmosphere(true)
                .atmosphereColor('#4361ee')
                .atmosphereAltitude(0.15)
                .onGlobeClick(() => autoPilot = false);
            
            window.worldInstance = world; 

            // Live Activity Pulses
            const activityRings = liveActivities.map(a => ({
                latitude: a.latitude,
                longitude: a.longitude,
                color: '#4cc9f0'
            }));

            // Time Capsules (Memory Icons)
            const capsuleMarkers = timeCapsules.map(c => ({
                lat: c.user.latitude,
                lng: c.user.longitude,
                size: 20,
                color: '#ff006e',
                label: 'Memory: ' + c.content.substring(0, 30) + '...'
            }));

            // Job Satellites (Orbiting Objects)
            const jobSatellites = jobVacancies.map((j, i) => ({
                lat: j.user.latitude,
                lng: j.user.longitude,
                alt: 0.3 + (Math.random() * 0.2),
                radius: 1.5,
                color: '#3a0ca3',
                label: 'Job: ' + j.title + ' @ ' + j.company
            }));

            world.ringsData(activityRings)
                .ringColor(d => d.color)
                .ringMaxRadius(3)
                .ringPropagationSpeed(2)
                .ringRepeatPeriod(1200)
                // Satellites Layer
                .customLayerData(jobSatellites)
                .customLayerLabel('label')
                .customThreeObject(d => new THREE.Mesh(
                    new THREE.OctahedronGeometry(d.radius),
                    new THREE.MeshLambertMaterial({ color: d.color, transparent: true, opacity: 0.9 })
                ))
                .customThreeObjectUpdate((obj, d) => {
                    Object.assign(obj.position, world.getCoords(d.lat, d.lng, d.alt));
                })
                // Arcs
                .arcsData(arcs)
                .arcColor('color')
                .arcDashLength(0.5)
                .arcDashGap(4)
                .arcDashAnimateTime(1500)
                .arcAltitudeAutoScale(0.2)
        // HTML Elements for Avatars
        const avatarElements = alumniData.filter(a => a.latitude && a.longitude).map(a => ({ ...a, type: 'avatar' }));
        let chatElements = []; // Dynamic chat bubbles

        world.htmlElementsData(isHeatmapMode ? [] : avatarElements)
            .htmlLat('latitude')
            .htmlLng('longitude')
            .htmlElement(d => {
                if (d.type === 'chat') {
                    const el = document.createElement('div');
                    el.className = 'chat-bubble';
                    const city = d.user.city_name || 'Alumni Hub';
                    el.innerHTML = `<i class="bi bi-geo-alt-fill text-info me-1"></i><strong>${d.user.name.split(' ')[0]}:</strong> ${city}`;
                    return el;
                }

                const el = document.createElement('div');
                    
                    const getAvatarUrl = (pic, name) => {
                        if (!pic) return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random&color=fff`;
                        if (pic.startsWith('http')) return pic;
                        // Clean up path and ensure /storage prefix
                        const cleanPath = pic.replace(/^\/?storage\//, '').replace(/^\//, '');
                        return `/storage/${cleanPath}`;
                    };

                    const imgUrl = getAvatarUrl(d.profile_picture, d.name);
                    el.innerHTML = `
                        <div class="avatar-marker" style="position:relative;">
                            <img src="${imgUrl}" 
                                 style="width:30px;height:30px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 10px rgba(67,97,238,0.8);object-fit:cover;"
                                 alt="${d.name}">
                            <div class="marker-pulse" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:40px;height:40px;background:rgba(67,97,238,0.2);border-radius:50%;z-index:-1;animation: pulse-avatar 2s infinite;"></div>
                        </div>
                    `;
                    el.style.cursor = 'pointer';
                    el.onclick = () => {
                        window.location.href = `/alumni/${d.id}`;
                    };
                    return el;
                });

            // Force Size Update in case clientWidth was 0
            setTimeout(() => {
                if (elem.clientWidth > 0) {
                    world.width(elem.clientWidth);
                    world.height(elem.clientHeight);
                }
            }, 500);

        // Cinematic Zoom ke Hub SMKN 2
        world.pointOfView({ lat: HUB_LAT, lng: HUB_LNG, altitude: 2.0 }, 1000);
        
        // Optimasi Render
        world.controls().enableDamping = true;
        world.controls().dampingFactor = 0.1;
        world.controls().rotateSpeed = 0.5;

        let autoPilot = true;

        // --- NEW FEATURES LOGIC ---

        // --- LIVE PULSE CHAT LOGIC ---
        let currentFeedIndex = 0;
        function spawnChatBubble() {
            if (isHeatmapMode || liveFeed.length === 0) return;
            
            const post = liveFeed[currentFeedIndex];
            const chatBubble = {
                ...post,
                latitude: parseFloat(post.user.latitude),
                longitude: parseFloat(post.user.longitude),
                type: 'chat',
                id: 'chat-' + post.id + '-' + Date.now()
            };

            chatElements = [chatBubble]; // Show one at a time for clarity
            world.htmlElementsData([...avatarElements, ...chatElements]);

            currentFeedIndex = (currentFeedIndex + 1) % liveFeed.length;

            // Remove bubble after animation
            setTimeout(() => {
                chatElements = [];
                if (!isHeatmapMode) world.htmlElementsData(avatarElements);
            }, 4000);
        }

        if (liveFeed.length > 0) {
            setInterval(spawnChatBubble, 6000);
        }

        function updatePulseFeed(post) {
            const container = document.getElementById('pulse-feed-container');
            const avatarUrl = post.user.profile_picture || `https://ui-avatars.com/api/?name=${encodeURIComponent(post.user.name)}&background=4f46e5&color=fff`;
            const city = post.user.city_name || 'Alumni Hub';

            const html = `
                <div class="feed-item active">
                    <img src="${avatarUrl}" class="feed-avatar">
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span class="fw-black text-primary" style="font-size: 10px; letter-spacing: 1px;">${post.user.name.toUpperCase()}</span>
                            <span class="text-info" style="font-size: 9px; font-weight: bold;"><i class="bi bi-geo-alt-fill me-1"></i>LOKASI TERDETEKSI</span>
                        </div>
                        <div class="text-white small text-truncate" style="font-family: 'JetBrains Mono', monospace; letter-spacing: 1px;">
                            <span class="text-white-50">></span> ${city.toUpperCase()}
                        </div>
                    </div>
                </div>
            `;
            
            container.innerHTML = html;
            
            // Remove after 5 seconds
            setTimeout(() => {
                const item = container.querySelector('.feed-item');
                if (item) item.classList.remove('active');
            }, 5000);
        }

        // Hook into spawnChatBubble to update the feed panel too
        const originalSpawn = spawnChatBubble;
        spawnChatBubble = function() {
            if (isHeatmapMode || liveFeed.length === 0) return;
            const post = liveFeed[currentFeedIndex];
            updatePulseFeed(post);
            originalSpawn();
        };

        window.toggleHeatmap = function() {
            isHeatmapMode = !isHeatmapMode;
            const btnText = document.getElementById('heatmapText');
            
            if (isHeatmapMode) {
                btnText.innerText = 'Show Avatars';
                world.htmlElementsData([]) // Hide avatars
                     .heatmapsData([heatmapDataRaw])
                     .heatmapPointLat('latitude')
                     .heatmapPointLng('longitude')
                     .heatmapPointWeight('weight')
                     .heatmapBandwidth(0.8)
                     .heatmapColorSaturation(0.5);
            } else {
                btnText.innerText = 'Show Heatmap';
                world.heatmapsData([]) // Hide heatmap
                     .htmlElementsData(alumniData.filter(a => a.latitude && a.longitude));
            }
        };

        window.flyToHub = function() {
            autoPilot = false;
            world.pointOfView({ lat: HUB_LAT, lng: HUB_LNG, altitude: 0.8 }, 2000);
            
            // Trigger a celebration pulse at the hub
            setTimeout(() => {
                world.ringsData([...activityRings, { latitude: HUB_LAT, longitude: HUB_LNG, color: '#f72585' }]);
            }, 2000);
        };

        window.toggleRadar = function() {
            const panel = document.getElementById('radar-panel');
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
            } else {
                panel.style.display = 'none';
            }
        };

        window.toggleConstellations = function() {
            isConstellationMode = !isConstellationMode;
            const btnText = document.getElementById('constellationText');
            
            if (isConstellationMode) {
                btnText.innerText = 'Hide Constellations';
                const paths = [];
                Object.values(majorConstellations).forEach(group => {
                    for (let i = 0; i < group.length - 1; i++) {
                        paths.push({
                            startLat: group[i].latitude,
                            startLng: group[i].longitude,
                            endLat: group[i+1].latitude,
                            endLng: group[i+1].longitude,
                            color: ['rgba(76, 201, 240, 0.1)', 'rgba(76, 201, 240, 0.8)']
                        });
                    }
                });
                world.arcsData([...arcs, ...paths]);
            } else {
                btnText.innerText = 'Constellations';
                world.arcsData(arcs);
            }
        };
            let tourInterval;
            let currentNodeIndex = 0;
            
            // Only tour nodes with alumni (not just the synthetic Hub)
            const tourNodes = nodes.filter(n => n.count >= 1 && !n.isHub).sort((a,b) => b.count - a.count);
            // If no other nodes exist except hub, fallback to rotating hub
            if (tourNodes.length === 0) {
                tourNodes.push(HUB_NODE);
            }

            const typeWriter = (text, element, speed = 30) => {
                element.innerHTML = '';
                let i = 0;
                element.classList.add('ai-typing');
                
                function type() {
                    if (i < text.length) {
                        element.innerHTML += text.charAt(i);
                        i++;
                        setTimeout(type, speed);
                    } else {
                        element.classList.remove('ai-typing');
                    }
                }
                type();
            };

            const fetchAIInsight = async (node) => {
                const panel = document.getElementById('ai-insight-panel');
                const cityEl = document.getElementById('ai-city-name');
                const statsEl = document.getElementById('ai-city-stats');
                const textEl = document.getElementById('ai-insight-text');

                panel.classList.remove('active');
                
                // Allow exit animation
                await new Promise(r => setTimeout(r, 500));

                cityEl.textContent = `TARGET: ${node.city.toUpperCase()}`;
                statsEl.textContent = `ALUMNI DETECTED: ${node.count} | MAJORS: ${node.majors.join(', ')}`;
                textEl.innerHTML = '<span class="ai-typing">Menganalisis sinyal satelit...</span>';
                
                panel.classList.add('active');

                try {
                    const res = await fetch(`/api/v1/map-ai-insight?city=${encodeURIComponent(node.city)}&count=${node.count}&majors=${encodeURIComponent(node.majors.join(','))}`);
                    const data = await res.json();
                    
                    if (data.success && autoPilot) {
                        typeWriter(data.insight, textEl);
                    }
                } catch (err) {
                    if(autoPilot) textEl.textContent = "Data terenkripsi. Gagal memuat analisis AI.";
                }
            };

            const runTour = () => {
                if (!autoPilot || tourNodes.length === 0) return;
                
                const node = tourNodes[currentNodeIndex];
                
                // Fly to node
                world.pointOfView({ lat: node.lat, lng: node.lng, altitude: 0.8 }, 3000);
                
                // Fetch AI Insight after flight
                setTimeout(() => {
                    if(autoPilot) fetchAIInsight(node);
                }, 3000);

                currentNodeIndex = (currentNodeIndex + 1) % tourNodes.length;
            };

            // Start Auto Rotate
            world.controls().autoRotate = true;
            world.controls().autoRotateSpeed = 1.0;

            // Start Tour Cycle (Every 15s)
            setTimeout(() => {
                if(autoPilot) {
                    world.controls().autoRotate = false; // Stop basic rotation for tour
                    runTour();
                    tourInterval = setInterval(runTour, 15000);
                }
            }, 3000);

            // Toggle Auto-Pilot
            document.getElementById('toggle-autopilot').addEventListener('click', function() {
                autoPilot = !autoPilot;
                this.innerHTML = autoPilot 
                    ? '<i class="bi bi-airplane-fill me-2"></i>Auto-Pilot: ON' 
                    : '<i class="bi bi-pause-circle me-2"></i>Auto-Pilot: OFF';
                
                if (autoPilot) {
                    this.classList.replace('text-slate-400', 'text-emerald-400');
                    this.classList.replace('bg-slate-800', 'bg-slate-900/90');
                    world.controls().autoRotate = false;
                    runTour();
                    tourInterval = setInterval(runTour, 15000);
                } else {
                    this.classList.replace('text-emerald-400', 'text-slate-400');
                    clearInterval(tourInterval);
                    world.controls().autoRotate = true; // Fallback to basic spin
                    document.getElementById('ai-insight-panel').classList.remove('active');
                }
            });

            // User Interaction disables Auto-Pilot temporarily
            elem.addEventListener('mousedown', () => {
                if (autoPilot) {
                    clearInterval(tourInterval);
                    document.getElementById('ai-insight-panel').classList.remove('active');
                }
            });
            
            // Handle Window Resize
            window.addEventListener('resize', (event) => {
                if (elem.clientWidth > 0) {
                    world.width(elem.clientWidth);
                    world.height(elem.clientHeight);
                }
            });

            // ===== ALUMNI RADAR INTEGRATION =====
            document.getElementById('btn-scan-radar').addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert("Browser Anda tidak mendukung Geolocation.");
                    return;
                }

                const btn = this;
                const container = document.getElementById('radar-results-container');
                const loading = document.getElementById('radar-loading');
                const list = document.getElementById('radar-list');
                const desc = document.getElementById('radar-desc');

                btn.classList.add('d-none');
                desc.classList.add('d-none');
                container.classList.remove('d-none');
                loading.classList.remove('d-none');
                list.innerHTML = '';

                // Hentikan Auto Pilot
                autoPilot = false;
                document.getElementById('toggle-autopilot').innerHTML = '<i class="bi bi-pause-circle me-2"></i>Auto-Pilot: OFF';
                document.getElementById('toggle-autopilot').classList.replace('btn-primary', 'btn-secondary');
                clearInterval(tourInterval);
                world.controls().autoRotate = false;
                document.getElementById('ai-insight-panel').classList.remove('active');

                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Cinematic Zoom ke lokasi user
                    world.pointOfView({ lat: lat, lng: lng, altitude: 0.6 }, 3000);

                    // Panggil API
                    fetch(`/alumni/networking/nearby?lat=${lat}&lng=${lng}`)
                        .then(response => response.json())
                        .then(data => {
                            loading.classList.add('d-none');

                            if (data.success && data.recommendations.length > 0) {
                                let html = '';
                                const detectedAlumni = [];
                                
                                data.recommendations.forEach(rec => {
                                    // Tambahkan ke list deteksi globe
                                    detectedAlumni.push({
                                        lat: parseFloat(rec.latitude),
                                        lng: parseFloat(rec.longitude)
                                    });

                                    html += `
                                     <div class="d-flex align-items-center mb-2 p-2 bg-white bg-opacity-10 rounded-3 transition-all">
                                         <img src="${rec.profile_picture}" class="rounded-circle me-2 border border-info" width="35" height="35" style="object-fit: cover;">
                                         <div class="flex-grow-1 overflow-hidden">
                                             <h6 class="fw-bold mb-0 text-white text-truncate" style="font-size: 0.85rem;">${rec.name}</h6>
                                             <p class="mb-0 text-success text-truncate" style="font-size: 0.7rem;">
                                                 <i class="bi bi-geo-alt-fill me-1"></i>${rec.distance} km
                                             </p>
                                         </div>
                                         <div class="d-flex gap-1">
                                            <a href="/alumni/${rec.id}" class="btn btn-sm btn-outline-info rounded-circle p-1" style="width:24px;height:24px;"><i class="fas fa-user" style="font-size:10px;"></i></a>
                                            <a href="https://wa.me/${rec.phone_number?.replace(/[^0-9]/g, '')}" target="_blank" class="btn btn-sm btn-success rounded-circle p-1" style="width:24px;height:24px;"><i class="fab fa-whatsapp" style="font-size:10px;"></i></a>
                                         </div>
                                     </div>`;
                                });
                                
                                // Update globe rings
                                world.ringsData(detectedAlumni);

                                html += `<button class="btn btn-sm btn-link text-white-50 w-100 mt-2 text-decoration-none" onclick="resetRadarPanel()">Tutup Radar</button>`;
                                list.innerHTML = html;
                            } else {
                                list.innerHTML = `
                                <p class="small text-white-50 text-center py-3">Tidak ada alumni ditemukan dalam radius 50km.</p>
                                <button class="btn btn-sm btn-link text-white-50 w-100 text-decoration-none" onclick="resetRadarPanel()">Tutup Radar</button>
                                `;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            loading.classList.add('d-none');
                            list.innerHTML = `<p class="small text-danger text-center py-2">Gagal memuat data.</p>
                            <button class="btn btn-sm btn-link text-white-50 w-100 text-decoration-none" onclick="resetRadarPanel()">Tutup Radar</button>`;
                        });
                }, error => {
                    loading.classList.add('d-none');
                    list.innerHTML = `<p class="small text-warning text-center py-2">Izin lokasi ditolak.</p>
                    <button class="btn btn-sm btn-link text-white-50 w-100 text-decoration-none" onclick="resetRadarPanel()">Tutup Radar</button>`;
                });
            });

        } catch (error) {
            console.error("Globe.gl Error:", error);
            elem.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100 flex-column text-center p-5">
                    <i class="bi bi-exclamation-triangle-fill text-warning display-4 mb-3"></i>
                    <h4 class="text-white">Kesalahan WebGL</h4>
                    <p class="text-light opacity-75">${error.message}</p>
                    <p class="text-muted small">Harap pastikan perangkat/browser Anda mendukung akselerasi perangkat keras 3D (WebGL).</p>
                </div>
            `;
        }
        
        } // end of initGlobeMap function
        
        initGlobeMap();
    });

    // ===== RADAR HELPERS =====
    window.resetRadarPanel = function() {
        document.getElementById('radar-results-container').classList.add('d-none');
        document.getElementById('btn-scan-radar').classList.remove('d-none');
        document.getElementById('radar-desc').classList.remove('d-none');
        // Clear rings from globe
        if(window.worldInstance) window.worldInstance.ringsData([]);
    };

    window.toggleRadar = function() {
        const panel = document.getElementById('radar-panel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    };

    window.toggleUIVisibility = function() {
        const statsPanel = document.getElementById('stats-panel');
        const aiPanel = document.getElementById('ai-insight-panel');
        const btn = document.getElementById('toggle-ui');
        const icon = btn.querySelector('i');
        
        if (statsPanel.classList.contains('d-none')) {
            statsPanel.classList.remove('d-none');
            aiPanel.classList.remove('d-none');
            icon.className = 'bi bi-eye-fill';
        } else {
            statsPanel.classList.add('d-none');
            aiPanel.classList.add('d-none');
            icon.className = 'bi bi-eye-slash-fill';
        }
    };
</script>
@endpush
