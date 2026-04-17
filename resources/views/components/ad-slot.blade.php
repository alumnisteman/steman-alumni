@props([
    'position', 
    'aspectRatio' => 'auto', 
    'mobileAspectRatio' => null, 
    'class' => '',
    'running' => true // Default to true if not specified
])

@php
    $position = strtolower(trim($position));
    $ads = getAds($position);
    
    // Fallback images if no ads exist
    $defaultDesktop = 'https://via.placeholder.com/1280x300?text=Sponsor+Space';
    $defaultMobile = 'https://via.placeholder.com/600x400?text=Sponsor+Space';
    
    $isMarquee = $running && ($position === 'header' || $position === 'content') && $ads->count() > 0;
    
    // Fallback for non-marquee: Only show one random ad (like sidebar)
    if (!$isMarquee && $ads->isNotEmpty()) {
        $ads = collect([$ads->first()]);
    }
@endphp

<div class="ad-container {{ $class }} ad-pos-{{ $position }} {{ $isMarquee ? 'ad-marquee-mode' : '' }}" 
     style="--aspect-ratio: {{ $aspectRatio }}; --mobile-aspect-ratio: {{ $mobileAspectRatio ?? $aspectRatio }};">
    
    @if($ads->isEmpty())
        <div class="ad-link shadow-hover">
            <picture>
                <img src="{{ $defaultDesktop }}" alt="Advertisement" class="ad-img" loading="lazy">
            </picture>
        </div>
    @else
        <div class="{{ $isMarquee ? 'ad-marquee-wrapper' : '' }}">
            <div class="{{ $isMarquee ? 'ad-marquee-content' : 'ad-single-content' }}">
                @foreach($ads as $ad)
                    <a href="{{ route('ads.click', $ad->id) }}" target="_blank" class="ad-item ad-link {{ !$isMarquee ? 'shadow-hover' : '' }}">
                        <picture>
                            <source media="(max-width: 767px)" srcset="{{ $ad->image_mobile ?? $ad->image_desktop }}">
                            <img src="{{ $ad->image_desktop }}" alt="{{ $ad->title }}" class="ad-img" loading="lazy">
                        </picture>
                    </a>
                @endforeach

                {{-- Duplicate items for seamless loop in marquee mode --}}
                @if($isMarquee)
                    @foreach($ads as $ad)
                        <a href="{{ route('ads.click', $ad->id) }}" target="_blank" class="ad-item ad-link" aria-hidden="true">
                            <picture>
                                <source media="(max-width: 767px)" srcset="{{ $ad->image_mobile ?? $ad->image_desktop }}">
                                <img src="{{ $ad->image_desktop }}" alt="{{ $ad->title }}" class="ad-img" loading="lazy">
                            </picture>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    @endif
</div>

@once
<style>
    .ad-container {
        width: 100%;
        margin-bottom: 2rem;
        border-radius: 16px;
        overflow: hidden;
        background: transparent;
        position: relative;
    }

    /* Billboard-Style Banner untuk Header & Content (Sindonews-Style) */
    .ad-pos-header, 
    .ad-pos-content {
        background: #ffffff;
        width: 100%;
        max-width: 1210px; /* Lebar maksimal billboard */
        height: 250px;     /* Tinggi ditambah agar konten terbaca */
        margin: auto;      /* Tengah rata */
        margin-bottom: 2rem;
        display: block;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .ad-pos-header:hover, 
    .ad-pos-content:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .ad-marquee-mode {
        background: #ffffff;
    }

    .ad-link {
        display: block;
        width: 100%;
        height: 100%;
        text-decoration: none;
    }

    .ad-img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: contain; /* ANTI-LONJONG: Menjaga proporsi asli */
        object-position: center;
        background-color: #fcfcfc; /* Mengisi ruang kosong jika rasio berbeda */
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Marquee Styles */
    .ad-marquee-wrapper {
        overflow: hidden;
        width: 100%;
        container-type: inline-size;
        mask-image: linear-gradient(to right, transparent, black 1%, black 99%, transparent);
    }

    .ad-marquee-content {
        display: flex;
        width: max-content;
        animation: ad-scroll 8s linear infinite;
    }

    @media (hover: hover) {
        .ad-marquee-content:hover {
            animation-play-state: paused;
        }
    }

    .ad-marquee-content .ad-item {
        flex: 0 0 auto;
        width: 100cqw; /* Perfect fit to the container wrapper */
        padding-right: 2rem; /* Wider gap for billboard marquee */
        box-sizing: border-box;
    }

    @keyframes ad-scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    /* Normal shadow behavior for non-marquee */
    .ad-single-content .shadow-hover:hover .ad-img {
        transform: scale(1.02);
    }
    
    .shadow-hover {
        transition: box-shadow 0.3s ease;
    }
    .shadow-hover:hover {
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    @media (max-width: 767px) {
        .ad-container {
            margin-bottom: 1.5rem;
        }

        /* Responsive Billboard untuk Mobile (Sindonews-Style) */
        .ad-pos-header, 
        .ad-pos-content {
            width: 100% !important; /* Penuhi lebar layar ponsel */
            height: 150px !important; /* Tinggi ditingkatkan */
            border-radius: 8px;
        }
        
        .ad-img {
            padding: 5px;
        }
    }
</style>
@endonce
