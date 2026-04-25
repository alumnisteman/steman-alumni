<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>WebAR Nostalgia Scanner - STEMAN Alumni</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- MindAR & A-Frame -->
    <script src="https://aframe.io/releases/1.4.2/aframe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mind-ar@1.2.5/dist/mindar-image-aframe.prod.js"></script>

    <style>
        body {
            margin: 0;
            overflow: hidden;
            background-color: #000;
        }

        /* UI Overlay */
        #ar-ui-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            pointer-events: none; /* Let clicks pass through to AR canvas */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .ar-header {
            background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);
            padding: 20px;
            pointer-events: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ar-footer {
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 30px 20px;
            text-align: center;
            color: white;
            pointer-events: auto;
        }

        .scanning-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70vw;
            height: 70vw;
            max-width: 400px;
            max-height: 400px;
            border: 2px dashed rgba(59, 130, 246, 0.5);
            border-radius: 20px;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
        }

        .scanning-frame::before, .scanning-frame::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            border: 4px solid #3b82f6;
            border-radius: 10px;
        }

        /* Scanning Corners */
        .scan-tl { top: -2px; left: -2px; border-right: none; border-bottom: none; border-bottom-right-radius: 0; }
        .scan-tr { top: -2px; right: -2px; border-left: none; border-bottom: none; border-bottom-left-radius: 0; }
        .scan-bl { bottom: -2px; left: -2px; border-right: none; border-top: none; border-top-right-radius: 0; }
        .scan-br { bottom: -2px; right: -2px; border-left: none; border-top: none; border-top-left-radius: 0; }

        /* Scanning Laser Animation */
        .laser {
            width: 100%;
            height: 2px;
            background: #3b82f6;
            box-shadow: 0 0 10px #3b82f6;
            position: absolute;
            animation: scan 2s infinite linear alternate;
        }

        @keyframes scan {
            0% { top: 0%; }
            100% { top: 100%; }
        }

        #scanning-status {
            font-family: monospace;
            letter-spacing: 2px;
            color: #3b82f6;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Success State */
        .target-found .scanning-frame {
            border-color: #10b981;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.8);
            transition: all 0.5s;
        }
        .target-found .laser { display: none; }
        .target-found .corner { border-color: #10b981; }
        .target-found #scanning-status { color: #10b981; }
    </style>
</head>
<body>

    <!-- AR Scene (A-Frame + MindAR) -->
    <!-- Using a default demo target. The user should replace targetSrc with their compiled .mind file -->
    <a-scene 
        mindar-image="imageTargetSrc: https://cdn.jsdelivr.net/gh/hiukim/mind-ar-js@1.2.5/examples/image-tracking/assets/card-example/targets.mind; autoStart: true; uiScanning: no;" 
        color-space="sRGB" 
        renderer="colorManagement: true, physicallyCorrectLights" 
        vr-mode-ui="enabled: false" 
        device-orientation-permission-ui="enabled: false">
        
        <a-assets>
            <!-- 3D Holographic Card Asset to display when logo is scanned -->
            <img id="holo-profile" src="{{ asset('/images/hero_iluni.png') }}" crossorigin="anonymous">
            <!-- 3D text font -->
            <a-asset-item id="optimerBoldFont" src="https://rawgit.com/mrdoob/three.js/master/examples/fonts/optimer_bold.typeface.json"></a-asset-item>
        </a-assets>

        <a-camera position="0 0 0" look-controls="enabled: false"></a-camera>

        <!-- The Target Marker (index 0 is the first image in the .mind file) -->
        <a-entity mindar-image-target="targetIndex: 0" id="target-entity">
            <!-- What appears when scanned -->
            <!-- A Glowing Plane -->
            <a-plane position="0 0 0" height="1.2" width="1" material="color: #0f172a; opacity: 0.9; transparent: true"></a-plane>
            
            <!-- Avatar Image -->
            <a-image src="#holo-profile" position="0 0.25 0.1" height="0.5" width="0.5"></a-image>
            
            <!-- Text Overlay -->
            <a-text value="STEMAN ALUMNI" color="#3b82f6" position="-0.4 0.55 0.1" scale="0.5 0.5 0.5"></a-text>
            <a-text value="VIRTUAL DATA RETRIEVED" color="#10b981" position="-0.4 -0.1 0.1" scale="0.3 0.3 0.3"></a-text>
            <a-text value="Name: Alumni System\nStatus: Active\nYear: Digital Era" color="#ffffff" position="-0.4 -0.3 0.1" scale="0.3 0.3 0.3"></a-text>
            
            <!-- Decorative border glow -->
            <a-box position="0 0 -0.05" depth="0.01" height="1.25" width="1.05" material="color: #3b82f6; opacity: 0.5; transparent: true"></a-box>
        </a-entity>
    </a-scene>

    <!-- Custom UI Overlay -->
    <div id="ar-ui-overlay">
        <div class="ar-header">
            <a href="/" class="btn btn-dark rounded-pill border border-secondary shadow"><i class="bi bi-arrow-left"></i> Kembali</a>
            <div class="badge bg-primary px-3 py-2 rounded-pill shadow"><i class="bi bi-camera-video"></i> AR Mode Aktif</div>
        </div>

        <div class="scanning-frame" id="scanner-frame">
            <div class="scan-tl corner" style="position:absolute; width:40px; height:40px; border: 4px solid #3b82f6; top:-4px; left:-4px; border-right:none; border-bottom:none;"></div>
            <div class="scan-tr corner" style="position:absolute; width:40px; height:40px; border: 4px solid #3b82f6; top:-4px; right:-4px; border-left:none; border-bottom:none;"></div>
            <div class="scan-bl corner" style="position:absolute; width:40px; height:40px; border: 4px solid #3b82f6; bottom:-4px; left:-4px; border-right:none; border-top:none;"></div>
            <div class="scan-br corner" style="position:absolute; width:40px; height:40px; border: 4px solid #3b82f6; bottom:-4px; right:-4px; border-left:none; border-top:none;"></div>
            <div class="laser"></div>
        </div>

        <div class="ar-footer">
            <h5 class="fw-bold mb-2" id="status-heading">Arahkan Kamera Ke Logo</h5>
            <p class="small opacity-75 mb-3" id="status-text">Cari logo SMK N 2 Ternate atau Buku Tahunan Anda untuk membuka hologram digital.</p>
            <div id="scanning-status"><i class="bi bi-radar"></i> SCANNING...</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const targetEntity = document.getElementById('target-entity');
            const overlay = document.getElementById('ar-ui-overlay');
            const heading = document.getElementById('status-heading');
            const text = document.getElementById('status-text');
            const status = document.getElementById('scanning-status');

            // Listen for Target Found
            targetEntity.addEventListener('targetFound', event => {
                overlay.classList.add('target-found');
                heading.innerText = "TARGET TERDETEKSI!";
                heading.classList.add('text-success');
                text.innerText = "Hologram memuat data alumni dari server...";
                status.innerHTML = '<i class="bi bi-check-circle-fill"></i> DATA RETRIEVED';
            });

            // Listen for Target Lost
            targetEntity.addEventListener('targetLost', event => {
                overlay.classList.remove('target-found');
                heading.innerText = "Arahkan Kamera Ke Logo";
                heading.classList.remove('text-success');
                text.innerText = "Cari logo SMK N 2 Ternate atau Buku Tahunan Anda untuk membuka hologram digital.";
                status.innerHTML = '<i class="bi bi-radar"></i> SCANNING...';
            });
        });
    </script>
</body>
</html>
