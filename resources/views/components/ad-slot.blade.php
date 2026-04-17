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
        background: #f1f5f9;
        position: relative;
    }

    .ad-marquee-mode {
        background: transparent;
        border-radius: 12px;
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
        object-fit: cover;
        aspect-ratio: var(--aspect-ratio);
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Marquee Styles */
    .ad-marquee-wrapper {
        overflow: hidden;
        width: 100%;
        mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
    }

    .ad-marquee-content {
        display: flex;
        width: max-content;
        animation: ad-scroll 15s linear infinite;
    }

    .ad-marquee-content:hover {
        animation-play-state: paused;
    }

    .ad-item {
        flex: 0 0 auto;
        width: 100vw; /* Default take full width of viewport if in container */
        max-width: 100%;
    }

    /* Override width for positions */
    .ad-pos-header .ad-item, 
    .ad-pos-content .ad-item {
        width: 100%; /* In flex container, this will be the base */
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
        .ad-img {
            aspect-ratio: var(--mobile-aspect-ratio);
        }
    }
</style>
@endonce
