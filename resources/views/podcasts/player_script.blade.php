<!-- Cyberpunk Audio Terminal: Player UI -->
<style>
    #cyberpunk-audio-player-content {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        max-width: 350px;
        width: calc(100% - 2rem);
        position: fixed;
        bottom: 1rem;
        left: 1rem;
        z-index: 10000;
        pointer-events: auto;
        background: rgba(0,0,0,0.95);
        border: 1px solid #0ff;
        clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 15px, 100% 100%, 0 100%);
    }
    
    #cyberpunk-audio-player-content.is-minimized {
        max-width: 120px;
        clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%) !important;
    }
    
    #cyberpunk-audio-player-content.is-minimized #player-title,
    #cyberpunk-audio-player-content.is-minimized #player-progress-container,
    #cyberpunk-audio-player-content.is-minimized #player-video-container {
        display: none !important;
    }
    
    #cyberpunk-audio-player-content .maximize-btn { display: none; }
    #cyberpunk-audio-player-content.is-minimized .maximize-btn { display: flex; }
    #cyberpunk-audio-player-content.is-minimized .minimize-btn { display: none; }
    #cyberpunk-audio-player-content.is-minimized .audio-visualizer { margin-right: 10px; }
    
    @media (max-width: 768px) {
        #cyberpunk-audio-player-content {
            bottom: 80px; /* Above mobile bottom nav */
            left: 50%;
            transform: translateX(-50%);
        }
        #cyberpunk-audio-player-content.is-minimized {
            transform: translateX(-50%);
        }
    }
</style>

<div id="cyberpunk-audio-player-content" class="bento-card py-2 px-4 d-none align-items-center gap-3 border-neon-cyan shadow-lg">
    <div class="audio-visualizer">
        <div class="visualizer-bar"></div>
        <div class="visualizer-bar"></div>
        <div class="visualizer-bar"></div>
        <div class="visualizer-bar"></div>
        <div class="visualizer-bar"></div>
    </div>
    <div class="flex-grow-1 overflow-hidden">
        <p id="player-title" class="text-white extra-small fw-bold mb-0 text-truncate uppercase tracking-widest" style="font-size: 0.65rem;">UPLINK ESTABLISHED...</p>
        <div id="player-video-container" class="d-none mt-1" style="height: 100px;">
            <iframe id="youtube-player" width="100%" height="100%" src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div>
        <div id="player-progress-container" class="w-100 bg-white bg-opacity-10 mt-1" style="height: 2px;">
            <div id="player-progress-bar" class="bg-neon-cyan h-100" style="width: 0%;"></div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button id="player-toggle-btn" class="btn btn-link text-neon-cyan p-0 fs-4 d-flex align-items-center" onclick="toggleAudio()">
            <i id="player-icon" class="bi bi-pause-fill" style="pointer-events: none;"></i>
        </button>
        <button class="btn btn-link text-white-50 p-0 fs-6 d-flex align-items-center minimize-btn" onclick="minimizeAudio()" title="Minimize">
            <i class="bi bi-dash-lg" style="pointer-events: none;"></i>
        </button>
        <button class="btn btn-link text-neon-cyan p-0 fs-6 d-flex align-items-center maximize-btn" onclick="maximizeAudio()" title="Maximize">
            <i class="bi bi-arrows-angle-expand" style="pointer-events: none;"></i>
        </button>
        <button class="btn btn-link text-white-50 p-0 fs-6 d-flex align-items-center" onclick="closeAudio()" title="Close">
            <i class="bi bi-x-lg" style="pointer-events: none;"></i>
        </button>
    </div>
</div>

<script>
    (function() {
        let currentAudio = null;
        let isYouTube = false;

        window.getYoutubeId = function(url) {
            if (!url) return null;
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : null;
        };

        window.playAudio = function(url, title) {
            console.log("Transmission received:", title, url);
            const audioPlayer = document.getElementById('cyberpunk-audio-player-content');
            const playerTitle = document.getElementById('player-title');
            const playerIcon = document.getElementById('player-icon');
            const progressBar = document.getElementById('player-progress-bar');
            const videoContainer = document.getElementById('player-video-container');
            const youtubePlayer = document.getElementById('youtube-player');
            const toggleBtn = document.getElementById('player-toggle-btn');

            if (!audioPlayer) {
                console.error("Cyberpunk player element not found!");
                return;
            }

            // Reset state
            window.closeAudio();
            window.maximizeAudio(); 
            
            const ytId = window.getYoutubeId(url);
            if (ytId) {
                isYouTube = true;
                if (videoContainer) videoContainer.classList.remove('d-none');
                if (progressBar) progressBar.parentElement.classList.add('d-none');
                if (toggleBtn) toggleBtn.classList.add('d-none');
                if (youtubePlayer) youtubePlayer.src = `https://www.youtube.com/embed/${ytId}?autoplay=1`;
                if (playerTitle) playerTitle.innerText = "VIDEO UPLINK: " + title.toUpperCase();
            } else {
                isYouTube = false;
                if (videoContainer) videoContainer.classList.add('d-none');
                if (progressBar) progressBar.parentElement.classList.remove('d-none');
                if (toggleBtn) toggleBtn.classList.remove('d-none');
                
                currentAudio = new Audio(url);
                currentAudio.play().catch(e => {
                    console.error("Audio play failed:", e);
                });
                
                if (playerTitle) playerTitle.innerText = "TRANS: " + title.toUpperCase();
                if (playerIcon) playerIcon.className = 'bi bi-pause-fill';
                
                currentAudio.ontimeupdate = () => {
                    if (progressBar) {
                        const progress = (currentAudio.currentTime / currentAudio.duration) * 100;
                        progressBar.style.width = progress + '%';
                    }
                };
                
                currentAudio.onended = () => {
                    window.closeAudio();
                };
            }
            
            audioPlayer.classList.remove('d-none');
            audioPlayer.classList.add('d-flex');
            const bars = audioPlayer.querySelectorAll('.visualizer-bar');
            bars.forEach(bar => bar.style.animationPlayState = 'running');
        };

        window.toggleAudio = function() {
            if (isYouTube) return;
            const audioPlayer = document.getElementById('cyberpunk-audio-player-content');
            const playerIcon = document.getElementById('player-icon');
            if (!currentAudio) return;

            const bars = audioPlayer.querySelectorAll('.visualizer-bar');
            if (currentAudio.paused) {
                currentAudio.play();
                if (playerIcon) playerIcon.className = 'bi bi-pause-fill';
                bars.forEach(bar => bar.style.animationPlayState = 'running');
            } else {
                currentAudio.pause();
                if (playerIcon) playerIcon.className = 'bi bi-play-fill';
                bars.forEach(bar => bar.style.animationPlayState = 'paused');
            }
        };

        window.minimizeAudio = function() {
            const audioPlayer = document.getElementById('cyberpunk-audio-player-content');
            if (audioPlayer) audioPlayer.classList.add('is-minimized');
        };

        window.maximizeAudio = function() {
            const audioPlayer = document.getElementById('cyberpunk-audio-player-content');
            if (audioPlayer) audioPlayer.classList.remove('is-minimized');
        };

        window.closeAudio = function() {
            const audioPlayer = document.getElementById('cyberpunk-audio-player-content');
            const videoContainer = document.getElementById('player-video-container');
            const youtubePlayer = document.getElementById('youtube-player');

            if (currentAudio) {
                currentAudio.pause();
                currentAudio = null;
            }
            if (isYouTube) {
                if (youtubePlayer) youtubePlayer.src = "";
                if (videoContainer) videoContainer.classList.add('d-none');
            }
            if (audioPlayer) {
                audioPlayer.classList.add('d-none');
                audioPlayer.classList.remove('d-flex');
                audioPlayer.classList.remove('is-minimized');
            }
        };
    })();
</script>



