@extends('layouts.app')

@push('styles')
<style>
    #mapViz {
        width: 100%;
        height: 80vh;
        border-radius: 20px;
        overflow: hidden;
        border: 2px solid rgba(255,255,255,0.05);
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
        left: 50px;
        z-index: 1000;
        color: white;
        text-shadow: 0 2px 8px rgba(0,0,0,0.8);
        background: rgba(0, 0, 0, 0.5);
        padding: 10px 20px;
        border-radius: 15px;
        backdrop-filter: blur(4px);
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
    @keyframes blink {
        50% { opacity: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" style="background: #020617; min-height: 90vh; position: relative;">
    <div class="position-relative">
        <div class="header-network">
            <h2 class="fw-black mb-1 opacity-90">GLOBAL MESH</h2>
            <p class="text-primary small fw-bold mb-0" style="letter-spacing: 3px;"><i class="bi bi-robot me-1"></i> AI-DRIVEN 3D NETWORK</p>
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

        <!-- 3D Globe Container -->
        <div id="mapViz"></div>

        <div class="network-stats">
            <div class="stat-card">
                <span class="stat-value text-danger" id="stat-national">{{ $nationalCount }}</span>
                <span class="stat-label">National Nodes</span>
            </div>
            <div class="stat-card">
                <span class="stat-value text-primary" id="stat-international">{{ $internationalCount }}</span>
                <span class="stat-label">Global Reach</span>
            </div>
            <a href="{{ route('alumni.index') }}" class="btn btn-outline-light rounded-pill px-4 mt-3" style="pointer-events: auto; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
                <i class="bi bi-person-lines-fill me-2"></i>Back to Directory
            </a>
            
            <button id="toggle-autopilot" class="btn btn-primary rounded-pill px-4 mt-3 ms-2" style="pointer-events: auto; box-shadow: 0 0 15px rgba(67,97,238,0.5);">
                <i class="bi bi-airplane-fill me-2"></i>Auto-Pilot: ON
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/globe.gl.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alumniData = @json($locations);
        
        // Hub Location (SMKN 2 Ternate)
        const HUB_LAT = 0.7856;
        const HUB_LNG = 127.3719;
        
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
            const world = Globe()
                (elem)
                .globeImageUrl('{{ asset("img/earth/earth-dark.jpg") }}')
                .bumpImageUrl('{{ asset("img/earth/earth-topology.png") }}')
                .backgroundImageUrl('{{ asset("img/earth/night-sky.png") }}')
                .showAtmosphere(true)
                .atmosphereColor('#4361ee')
                .atmosphereAltitude(0.25)
                // Points
                .pointsData(nodes)
                .pointLat('lat')
                .pointLng('lng')
                .pointColor(d => d.isHub ? '#ffeb3b' : (d.lat > -11 && d.lat < 6 && d.lng > 95 && d.lng < 141 ? '#ef233c' : '#4361ee'))
                .pointAltitude(d => d.isHub ? 0.05 : Math.min(d.count * 0.01, 0.1))
                .pointRadius(d => d.isHub ? 1.5 : Math.min(d.count * 0.2, 1.5) + 0.3)
                .pointsMerge(false) // Disable merge so individual point colors & sizes stand out
                // Arcs
                .arcsData(arcs)
                .arcStartLat('startLat')
                .arcStartLng('startLng')
                .arcEndLat('endLat')
                .arcEndLng('endLng')
                .arcColor('color')
                .arcDashLength(0.4)
                .arcDashGap(2)
                .arcDashInitialGap(() => Math.random() * 5)
                .arcDashAnimateTime(2000)
                .arcAltitudeAutoScale(0.3);

            // Force Size Update in case clientWidth was 0
            setTimeout(() => {
                if (elem.clientWidth > 0) {
                    world.width(elem.clientWidth);
                    world.height(elem.clientHeight);
                }
            }, 500);

            // Initial View
            world.pointOfView({ lat: -2.5, lng: 118, altitude: 2.5 }, 2000);

            // Auto-Pilot Logic
            let autoPilot = true;
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
                
                this.classList.toggle('btn-primary', autoPilot);
                this.classList.toggle('btn-secondary', !autoPilot);

                if (autoPilot) {
                    world.controls().autoRotate = false;
                    runTour();
                    tourInterval = setInterval(runTour, 15000);
                } else {
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
</script>
@endpush
