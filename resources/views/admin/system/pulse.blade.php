@extends('layouts.admin')

@section('title', 'System Pulse - Admin Dashboard')

@section('admin-content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white fw-bold">SYSTEM <span class="text-info">PULSE</span></h1>
            <p class="text-muted small">Live Architecture Health & Data Flow Visualization</p>
        </div>
        <div class="badge bg-dark border border-secondary p-2 d-flex align-items-center gap-2">
            <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
            <span class="text-uppercase tracking-widest small">Live Monitoring</span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card bg-dark border-secondary shadow-lg overflow-hidden" style="min-height: 600px; background-color: #0a0a0a !important;">
                <div class="card-body position-relative d-flex justify-content-center align-items-center">
                    
                    <!-- SVG Background for Lines -->
                    <svg id="architecture-svg" class="position-absolute w-100 h-100" style="z-index: 1; pointer-events: none;">
                        <defs>
                            <marker id="arrow" markerWidth="10" markerHeight="10" refX="8" refY="3" orientation="auto" markerUnits="strokeWidth">
                                <path d="M0,0 L0,6 L9,3 z" fill="#334155" />
                            </marker>
                            <filter id="glow">
                                <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
                                <feMerge>
                                    <feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/>
                                </feMerge>
                            </filter>
                        </defs>
                        <!-- Connections will be drawn by JS -->
                        <g id="connections-group"></g>
                    </svg>

                    <!-- Nodes -->
                    <div id="nodes-container" class="position-relative w-100 h-100" style="z-index: 2; min-height: 500px;">
                        
                        <!-- Client Layer -->
                        <div class="node" id="node-user" style="top: 10%; left: 10%;" data-connections="nginx">
                            <div class="node-icon bg-secondary"><i class="bi bi-phone"></i></div>
                            <div class="node-label">User Device</div>
                            <div class="node-status"><span class="dot"></span> <span>Checking...</span></div>
                        </div>

                        <!-- Infrastructure Layer -->
                        <div class="node" id="node-nginx" style="top: 30%; left: 30%;" data-connections="laravel">
                            <div class="node-icon bg-info bg-opacity-25 text-info"><i class="bi bi-hdd-network"></i></div>
                            <div class="node-label">Nginx Proxy</div>
                            <div class="node-status"><span class="dot"></span> <span>Checking...</span></div>
                        </div>

                        <div class="node" id="node-laravel" style="top: 50%; left: 50%;" data-connections="mysql,redis,newsapi,rsshub">
                            <div class="node-icon bg-danger bg-opacity-25 text-danger"><i class="bi bi-layers-half"></i></div>
                            <div class="node-label">Laravel App</div>
                            <div class="node-status"><span class="dot"></span> <span>Checking...</span></div>
                        </div>

                        <!-- Data Layer -->
                        <div class="node" id="node-mysql" style="top: 80%; left: 30%;">
                            <div class="node-icon bg-primary bg-opacity-25 text-primary"><i class="bi bi-database"></i></div>
                            <div class="node-label">MySQL DB</div>
                            <div class="node-status"><span class="dot"></span> <span>Checking...</span></div>
                        </div>

                        <div class="node" id="node-redis" style="top: 80%; left: 50%;">
                            <div class="node-icon bg-warning bg-opacity-25 text-warning"><i class="bi bi-lightning-charge"></i></div>
                            <div class="node-label">Redis Cache</div>
                            <div class="node-status"><span class="dot"></span> <span>Checking...</span></div>
                        </div>

                        <!-- External Layer -->
                        <div class="node" id="node-newsapi" style="top: 30%; left: 80%;">
                            <div class="node-icon bg-success bg-opacity-25 text-success"><i class="bi bi-newspaper"></i></div>
                            <div class="node-label">News API</div>
                            <div class="node-status"><span class="dot"></span> <span>Checking...</span></div>
                        </div>

                        <div class="node" id="node-rsshub" style="top: 60%; left: 80%;">
                            <div class="node-icon bg-white bg-opacity-10 text-white"><i class="bi bi-rss"></i></div>
                            <div class="node-label">RSSHub Proxy</div>
                            <div class="node-status"><span class="dot"></span> <span>Checking...</span></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .node {
        position: absolute;
        width: 140px;
        text-align: center;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .node-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 10px;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        background: #1e293b;
    }
    .node-label {
        color: white;
        font-weight: bold;
        font-size: 0.85rem;
        margin-bottom: 4px;
    }
    .node-status {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .node-status .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #64748b;
    }

    /* States */
    .node.up .node-icon { border-color: #10b981; box-shadow: 0 0 15px rgba(16, 185, 129, 0.3); }
    .node.up .node-status { color: #10b981; }
    .node.up .dot { background: #10b981; box-shadow: 0 0 8px #10b981; }

    .node.down .node-icon { border-color: #ef4444; box-shadow: 0 0 15px rgba(239, 68, 68, 0.3); }
    .node.down .node-status { color: #ef4444; }
    .node.down .dot { background: #ef4444; box-shadow: 0 0 8px #ef4444; }

    /* Animating Flow Path */
    .flow-line {
        stroke: #334155;
        stroke-width: 2;
        fill: none;
        stroke-dasharray: 5, 5;
    }
    .flow-line.active {
        stroke: #06b6d4;
        stroke-width: 2.5;
        animation: dash 10s linear infinite;
        filter: url(#glow);
    }
    @keyframes dash {
        to { stroke-dashoffset: -100; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const svg = document.getElementById('architecture-svg');
        const connectionsGroup = document.getElementById('connections-group');
        const nodes = document.querySelectorAll('.node');

        function drawConnections() {
            connectionsGroup.innerHTML = '';
            nodes.forEach(startNode => {
                const targets = startNode.getAttribute('data-connections');
                if (!targets) return;

                targets.split(',').forEach(targetId => {
                    const endNode = document.getElementById('node-' + targetId);
                    if (!endNode) return;

                    const startRect = startNode.getBoundingClientRect();
                    const endRect = endNode.getBoundingClientRect();
                    const containerRect = svg.getBoundingClientRect();

                    const x1 = (startRect.left + startRect.width / 2) - containerRect.left;
                    const y1 = (startRect.top + startRect.height / 2) - containerRect.top;
                    const x2 = (endRect.left + endRect.width / 2) - containerRect.left;
                    const y2 = (endRect.top + endRect.height / 2) - containerRect.top;

                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    // Create a curve
                    const dx = x2 - x1;
                    const dy = y2 - y1;
                    const dr = Math.sqrt(dx * dx + dy * dy);
                    const d = `M${x1},${y1} A${dr},${dr} 0 0,1 ${x2},${y2}`;
                    
                    path.setAttribute('d', d);
                    path.setAttribute('class', 'flow-line');
                    path.setAttribute('id', `line-${startNode.id.split('-')[1]}-${targetId}`);
                    
                    connectionsGroup.appendChild(path);
                });
            });
        }

        function updateHealth() {
            fetch("{{ route('admin.api.system.health') }}")
                .then(res => res.json())
                .then(data => {
                    Object.keys(data.nodes).forEach(key => {
                        const node = document.getElementById('node-' + key);
                        if (!node) return;

                        const status = data.nodes[key].status;
                        node.classList.remove('up', 'down');
                        node.classList.add(status);
                        node.querySelector('.node-status span:last-child').innerText = status.toUpperCase();

                        // Update lines
                        const outgoingLines = document.querySelectorAll(`[id^="line-${key}-"]`);
                        outgoingLines.forEach(line => {
                            if (status === 'up') line.classList.add('active');
                            else line.classList.remove('active');
                        });
                    });
                });
        }

        window.addEventListener('resize', drawConnections);
        setTimeout(drawConnections, 500); // Wait for nodes to settle
        updateHealth();
        setInterval(updateHealth, 10000); // Update every 10s
    });
</script>
@endsection
