@props([
    'src',
    'alt' => '',
    'w' => null,
    'h' => null,
    'f' => 'webp',
    'loading' => 'lazy',
    'class' => '',
    'isLcp' => false
])

@php
    $finalSrc = $src;
    // Auto-optimize if it's a local path
    if ($src && !str_starts_with($src, 'http')) {
        $finalSrc = thumbnail($src, $w, $h, $f);
    }
    
    // Force eager loading and high priority for LCP
    $finalLoading = $isLcp ? 'eager' : $loading;
    $fetchPriority = $isLcp ? 'high' : 'auto';
@endphp

<img 
    src="{{ $finalSrc }}" 
    alt="{{ $alt }}"
    @if($w) width="{{ $w }}" @endif
    @if($h) height="{{ $h }}" @endif
    loading="{{ $finalLoading }}"
    @if($isLcp) fetchpriority="{{ $fetchPriority }}" @endif
    decoding="async"
    class="{{ $class }}"
    {{ $attributes }}
>
