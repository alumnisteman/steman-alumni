@if(isset($activeEventTheme) && $activeEventTheme)
@php
    $theme      = $activeEventTheme;
    $countdown  = $theme->countdownTarget();
@endphp
<div class="event-banner-bar" id="event-theme-banner" role="banner" aria-label="Banner Event">
    @if($theme->banner_icon)
    <i class="{{ $theme->banner_icon }} event-icon" aria-hidden="true"></i>
    @elseif($theme->emoji)
    <span class="event-icon" aria-hidden="true">{{ $theme->emoji }}</span>
    @endif

    <div>
        <strong>{{ $theme->banner_text }}</strong>
        @if($theme->banner_subtext)
        <span class="d-none d-md-inline ms-2 opacity-90 fw-normal" style="font-size:0.82rem;">
            {{ $theme->banner_subtext }}
        </span>
        @endif
    </div>

    @if($countdown)
    <div class="event-countdown">
        <i class="bi bi-hourglass-split"></i>
        <span id="event-countdown-display">{{ $countdown->diffForHumans(['short' => true, 'parts' => 2]) }}</span>
        @if($theme->countdown_label)
        <span class="opacity-75">({{ $theme->countdown_label }})</span>
        @endif
    </div>
    @endif

    <button class="event-banner-close" onclick="closeEventBanner()" title="Tutup" aria-label="Tutup banner">
        <i class="bi bi-x" aria-hidden="true"></i>
    </button>
</div>

<script>
(function () {
    var STORAGE_KEY = 'event_banner_hidden_{{ $theme->css_class }}_{{ now()->format("Ymd") }}';
    if (sessionStorage.getItem(STORAGE_KEY) === '1') {
        var el = document.getElementById('event-theme-banner');
        if (el) el.style.display = 'none';
    }
    window.closeEventBanner = function () {
        var el = document.getElementById('event-theme-banner');
        if (el) {
            el.style.opacity = '0';
            el.style.transition = 'opacity 0.3s';
            setTimeout(function () { el.style.display = 'none'; }, 300);
        }
        try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch(e) {}
    };
})();
</script>
@endif
