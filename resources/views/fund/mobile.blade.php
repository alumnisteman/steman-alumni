@extends('layouts.app')

@section('content')
<style>
    /* NATIVE APP FEEL OVERRIDES */
    body {
        background-color: #050505;
        overflow: hidden; 
    }
    .app-container {
        height: 100vh;
        height: -webkit-fill-available;
        display: flex;
        flex-direction: column;
        background-color: #050505;
        color: #fff;
        font-family: 'Inter', sans-serif;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .snap-x {
        scroll-snap-type: x mandatory;
    }
    .snap-center {
        scroll-snap-align: center;
    }
    /* Story Animation */
    @keyframes progress-bar {
        from { width: 0%; }
        to { width: 100%; }
    }
    .story-progress-active {
        animation: progress-bar 4s linear forwards;
    }
    .active-scale:active {
        transform: scale(0.95);
    }
    /* Holographic Elements */
    .holo-border {
        position: relative;
    }
    .holo-border::before {
        content: '';
        position: absolute;
        inset: -2px;
        background: linear-gradient(45deg, #06b6d4, #8b5cf6, #3b82f6, #10b981);
        border-radius: 34px; /* matches inner radius + border */
        z-index: -1;
        opacity: 0.5;
        filter: blur(8px);
        animation: holo-spin 10s linear infinite;
    }
    @keyframes holo-spin {
        0% { filter: hue-rotate(0deg) blur(8px); }
        100% { filter: hue-rotate(360deg) blur(8px); }
    }
    /* Shine Effect for Cards */
    .shine-card {
        position: relative;
        overflow: hidden;
    }
    .shine-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: -150%;
        width: 50%;
        height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
        transform: skewX(-25deg);
        animation: shine 6s infinite;
    }
    @keyframes shine {
        0% { left: -150%; }
        20% { left: 200%; }
        100% { left: 200%; }
    }
</style>

<div class="app-container">

    {{-- TOP BAR --}}
    <div class="flex items-center justify-between p-4 bg-gray-900/50 backdrop-blur-xl sticky top-0 z-[100] border-b border-white/5">
        <div class="flex items-center gap-3">
            <a href="/" class="text-white"><i class="bi bi-chevron-left fs-4"></i></a>
            <div>
                <h1 class="text-lg font-black tracking-tighter">ALUMNI FUND</h1>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Live Transparency</span>
                </div>
            </div>
        </div>
        <div class="bg-white/10 p-2 rounded-full">
            <i class="bi bi-shield-check text-cyan"></i>
        </div>
    </div>

    {{-- SCROLLABLE CONTENT AREA --}}
    <div class="flex-grow overflow-y-auto no-scrollbar pb-32">
        
        {{-- STORY ROW --}}
        <div class="px-4 py-6 overflow-x-auto flex gap-4 no-scrollbar">
            {{-- YOUR STORY --}}
            <div class="flex flex-col items-center flex-shrink-0">
                <div class="w-16 h-16 rounded-full p-[2px] bg-gray-800 border border-dashed border-gray-600 flex items-center justify-center relative">
                    <img src="{{ auth()->user() ? auth()->user()->profile_picture_url : 'https://ui-avatars.com/api/?name=User' }}" class="w-full h-full rounded-full object-cover opacity-50">
                    <div class="absolute bottom-0 right-0 bg-cyan text-white rounded-full w-5 h-5 flex items-center justify-center border-2 border-black">
                        <i class="bi bi-plus small"></i>
                    </div>
                </div>
                <span class="text-[10px] mt-2 text-gray-500 font-bold">Cerita Anda</span>
            </div>

            @foreach($stories as $s)
            <div class="flex flex-col items-center flex-shrink-0 relative group cursor-pointer" onclick="openStory({{ $loop->index }})">
                <!-- Glowing Ring -->
                <div class="absolute inset-0 bg-gradient-to-tr from-cyan-400 via-indigo-500 to-purple-500 rounded-full blur-[4px] opacity-70 group-hover:opacity-100 transition-opacity"></div>
                <div class="w-16 h-16 rounded-full p-[2px] bg-gradient-to-tr from-cyan-400 via-indigo-500 to-purple-500 active-scale transition-transform relative z-10">
                    <div class="bg-[#050505] p-[2px] rounded-full w-full h-full">
                        <img src="{{ $s->cover }}" class="w-full h-full rounded-full object-cover border border-white/10">
                    </div>
                </div>
                <span class="text-[10px] mt-2 text-white font-bold tracking-wide drop-shadow-md">{{ Str::limit($s->title, 10) }}</span>
            </div>
            @endforeach
        </div>

        {{-- FUND SLIDER --}}
        <div class="px-4 mb-2">
            <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Program Unggulan</h3>
        </div>
        <div id="fundSlider" class="flex overflow-x-auto snap-x snap-mandatory gap-5 px-4 pb-10 pt-4 no-scrollbar">

            @foreach($funds as $f)
            <div class="min-w-[85%] snap-center bg-[#0a0a0a] border border-white/10 rounded-[32px] p-6 transition-all relative overflow-hidden group holo-border shine-card shadow-2xl">
                {{-- Decorative Glow --}}
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-cyan/20 blur-[80px] rounded-full mix-blend-screen pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-indigo-500/10 blur-[60px] rounded-full mix-blend-screen pointer-events-none"></div>
                
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-6">
                        <div class="bg-white/10 backdrop-blur-md p-3 rounded-2xl shadow-inner border border-white/5">
                            <i class="bi bi-{{ $f->type == 'foundation' ? 'bank' : 'calendar-event' }} text-cyan fs-4 shadow-cyan"></i>
                        </div>
                        <span class="text-[9px] font-black bg-cyan/10 border border-cyan/30 text-cyan px-3 py-1 rounded-full uppercase tracking-widest shadow-[0_0_10px_rgba(6,182,212,0.3)]">
                            {{ $f->type == 'foundation' ? 'DANA ABADI' : 'EVENT' }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-black mb-2 tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">{{ $f->title }}</h2>
                    <p class="text-gray-400 text-xs mb-6 line-clamp-2 leading-relaxed">{{ strip_tags($f->description) }}</p>

                    <div class="mb-2 flex justify-between items-end">
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Terkumpul</span>
                        <span class="text-sm font-black text-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.5)]">{{ number_format($f->progress, 1) }}%</span>
                    </div>
                    <h3 class="text-3xl font-black mb-5 tracking-tighter">
                        Rp {{ number_format($f->current_amount, 0, ',', '.') }}
                    </h3>

                    <div class="w-full bg-gray-800/80 h-2 rounded-full mb-6 overflow-hidden border border-white/5 shadow-inner">
                        <div class="bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-500 h-full rounded-full transition-all duration-1000 relative"
                             style="width: {{ $f->progress }}%">
                             <div class="absolute top-0 right-0 bottom-0 left-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSI+PC9yZWN0Pgo8L3N2Zz4=')] opacity-30"></div>
                        </div>
                    </div>

                    <a href="/donations/campaign/{{ $f->slug }}"
                       class="block text-center bg-white text-black py-4 rounded-2xl font-black text-sm active-scale transition-all hover:bg-gray-200 shadow-[0_0_20px_rgba(255,255,255,0.2)]">
                       DONASI SEKARANG <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            @endforeach

        </div>

        {{-- REALTIME FEED --}}
        <div class="px-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest">Aktivitas Terbaru</h3>
                <span class="text-[10px] font-bold text-green-500 animate-pulse">REALTIME</span>
            </div>

            <div id="feed" class="space-y-3">
                @foreach($donations as $d)
                <div class="bg-gray-900/50 border border-white/5 p-4 rounded-2xl flex items-center justify-between animate-reveal">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-cyan/10 flex items-center justify-center">
                            <i class="bi bi-person text-cyan small"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold mb-0">{{ $d->user ? $d->user->name : ($d->is_anonymous ? 'Anonim' : 'Alumni') }}</p>
                            <p class="text-[10px] text-gray-500">Baru saja donasi</p>
                        </div>
                    </div>
                    <span class="text-xs font-black text-cyan">+ Rp {{ number_format($d->amount, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- STORY VIEWER (FULLSCREEN) --}}
    <div id="storyViewer" class="fixed inset-0 bg-black z-[1000] hidden flex flex-col">
        {{-- Progress Bars --}}
        <div id="bars" class="flex gap-1 p-4 absolute top-0 left-0 right-0 z-[1010]"></div>

        {{-- Header --}}
        <div class="absolute top-8 left-0 right-0 p-4 flex items-center justify-between z-[1010]">
            <div class="flex items-center gap-3">
                <div id="storyUserAvatar" class="w-10 h-10 rounded-full border-2 border-white/20 p-[2px]">
                    <img id="storyUserImg" src="" class="w-full h-full rounded-full object-cover">
                </div>
                <div>
                    <h4 id="storyUserName" class="text-sm font-black text-white mb-0 shadow-sm"></h4>
                    <span class="text-[10px] text-white/60 font-bold">ALUMNI STEMAN TERNATE</span>
                </div>
            </div>
            <button onclick="closeStory()" class="text-white bg-black/20 backdrop-blur-md rounded-full w-8 h-8 flex items-center justify-center">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Content Area --}}
        <div id="storyContent" class="flex-grow flex items-center justify-center relative">
            <div id="storyImageContainer" class="absolute inset-0 z-0">
                <img id="storyBgImage" src="" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/80"></div>
            </div>
            
            <div class="relative z-10 px-8 text-center mt-32">
                <h2 id="storyTitle" class="text-3xl font-black mb-4 tracking-tighter leading-tight drop-shadow-2xl"></h2>
                <p id="storyText" class="text-lg text-white/80 font-medium drop-shadow-md"></p>
                
                <div class="mt-12">
                    <a href="/register" class="bg-white text-black px-8 py-4 rounded-2xl font-black text-sm active-scale inline-block">
                        IKUT BERKONTRIBUSI
                    </a>
                </div>
            </div>

            {{-- Tap Areas --}}
            <div class="absolute inset-0 flex z-20">
                <div class="w-1/3 h-full" onclick="prevStory()"></div>
                <div class="w-2/3 h-full" onclick="nextStory()"></div>
            </div>
        </div>
    </div>

    {{-- BOTTOM NAV --}}
    <div class="fixed bottom-0 left-0 right-0 bg-black/80 backdrop-blur-2xl border-t border-white/5 flex justify-around py-4 pb-8 z-[100] safe-area-bottom">
        <a href="/" class="flex flex-col items-center gap-1 {{ Request::is('/') ? 'text-cyan' : 'text-gray-500' }}">
            <i class="bi bi-house-door-fill fs-5"></i>
            <span class="text-[10px] font-bold uppercase tracking-widest">Home</span>
        </a>
        <a href="/alumni-fund" class="flex flex-col items-center gap-1 text-cyan">
            <i class="bi bi-lightning-charge-fill fs-5"></i>
            <span class="text-[10px] font-bold uppercase tracking-widest">Fund</span>
        </a>
        <a href="/alumni/dashboard" class="flex flex-col items-center gap-1 text-gray-500">
            <i class="bi bi-grid-fill fs-5"></i>
            <span class="text-[10px] font-bold uppercase tracking-widest">App</span>
        </a>
        <a href="/profil" class="flex flex-col items-center gap-1 text-gray-500">
            <i class="bi bi-person-fill fs-5"></i>
            <span class="text-[10px] font-bold uppercase tracking-widest">Me</span>
        </a>
    </div>

</div>

<script>
    // STORY LOGIC
    let current = 0;
    let timer;
    let stories = @json($stories);

    function openStory(index){
        current = index;
        document.getElementById('storyViewer').classList.remove('hidden');
        renderStory();
    }

    function renderStory(){
        if (!stories[current]) return closeStory();
        
        const s = stories[current];
        
        document.getElementById('storyBgImage').src = s.cover;
        document.getElementById('storyUserImg').src = s.cover;
        document.getElementById('storyUserName').innerText = s.title;
        document.getElementById('storyTitle').innerText = s.title;
        document.getElementById('storyText').innerText = s.text;

        renderBars();
        clearTimeout(timer);
        timer = setTimeout(nextStory, 4000);
    }

    function renderBars(){
        let barsContainer = document.getElementById('bars');
        barsContainer.innerHTML = '';
        stories.forEach((_, i) => {
            let bar = document.createElement('div');
            bar.className = `flex-1 h-1 rounded-full overflow-hidden ${i < current ? 'bg-white' : 'bg-white/20'}`;
            if (i === current) {
                let inner = document.createElement('div');
                inner.className = 'h-full bg-white story-progress-active';
                bar.appendChild(inner);
            }
            barsContainer.appendChild(bar);
        });
    }

    function nextStory(){
        if(current < stories.length - 1){
            current++;
            renderStory();
        } else {
            closeStory();
        }
    }

    function prevStory(){
        if(current > 0){
            current--;
            renderStory();
        } else {
            renderStory(); // Restart current story
        }
    }

    function closeStory(){
        document.getElementById('storyViewer').classList.add('hidden');
        clearTimeout(timer);
    }

    // REALTIME FEED REFRESH
    setInterval(async () => {
        try {
            const res = await fetch('/api/donations');
            const data = await res.json();

            let feed = document.getElementById('feed');
            let html = '';
            data.forEach(d => {
                html += `
                    <div class="bg-gray-900/50 border border-white/5 p-4 rounded-2xl flex items-center justify-between animate-reveal">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-cyan/10 flex items-center justify-center">
                                <i class="bi bi-person text-cyan small"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold mb-0">${d.name}</p>
                                <p class="text-[10px] text-gray-500">Baru saja donasi</p>
                            </div>
                        </div>
                        <span class="text-xs font-black text-cyan">+ Rp ${new Intl.NumberFormat('id-ID').format(d.amount)}</span>
                    </div>
                `;
            });
            feed.innerHTML = html;
        } catch (e) {
            console.error("Feed refresh failed", e);
        }
    }, 10000);

    // Haptic visual feedback
    document.querySelectorAll('.active-scale').forEach(el => {
        el.addEventListener('touchstart', () => {
            if (window.navigator.vibrate) window.navigator.vibrate(5);
        });
    });
</script>

@endsection
