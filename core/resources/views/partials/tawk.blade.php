{{-- Tawk.to: use env() so config cache doesn't keep wrong ID. Set TAWK_PROPERTY_ID in .env as "PropertyID/WidgetID" or 40-char (auto-split). --}}
@php
    $raw = trim((string) config('services.tawk.property_id', ''));
    if ($raw !== '') {
        if (str_contains($raw, '/')) {
            $tawkEmbedId = rtrim($raw, '/');
        } elseif (strlen($raw) === 40) {
            $tawkEmbedId = substr($raw, 0, 24) . '/' . substr($raw, 24);
        } else {
            $tawkEmbedId = $raw . '/default';
        }
    } else {
        $tawkEmbedId = '';
    }
    $tawkValid = $tawkEmbedId !== '' && str_contains($tawkEmbedId, '/');
    // Skip on localhost/127.0.0.1 to avoid CORS (embed.tawk.to does not send Access-Control-Allow-Origin for localhost)
    $httpHost = request()->getHttpHost();
    $isLocalEnv = app()->environment('local');
    $isLocalhost = $isLocalEnv
        || stripos($httpHost, 'localhost') !== false
        || stripos($httpHost, '127.0.0.1') !== false
        || str_contains(request()->url(), 'localhost')
        || str_contains(request()->url(), '127.0.0.1');
    $loadOnLocalhost = filter_var(env('TAWK_ON_LOCALHOST', 'false'), FILTER_VALIDATE_BOOLEAN);
    $skipTawk = $isLocalhost && !$loadOnLocalhost;
    $libraryOnly = feature_enabled('assets.library_only_mode', true);
    $allowChat = feature_enabled('assets.allow_live_chat_embed', false);
@endphp
{{-- Tawk: only load if TAWK_PROPERTY_ID set. Disabled when APP_ENV=local or on localhost (CORS/404). TAWK_ENABLED=false disables globally; TAWK_ON_LOCALHOST=true loads on localhost. --}}
@if(!$libraryOnly && $allowChat && $tawkValid && env('TAWK_ENABLED', true) && !$skipTawk)
<script type="text/javascript">
(function(){
  if (window.__tawkLoaded) return;
  var h = typeof location !== 'undefined' && location.hostname ? location.hostname.toLowerCase() : '';
  if (h === 'localhost' || h === '127.0.0.1' || h.indexOf('localhost') !== -1) return;
  var url = 'https://embed.tawk.to/{{ $tawkEmbedId }}';
  function loadTawk() {
    if (window.__tawkLoaded) return;
    window.__tawkLoaded = true;
    window.Tawk_API = window.Tawk_API || {};
    window.Tawk_LoadStart = new Date();
    try {
      var s = document.createElement('script');
      s.async = true;
      s.src = url;
      s.charset = 'UTF-8';
      /* Do not set crossorigin: Tawk.to does not send Access-Control-Allow-Origin for localhost, which causes CORS block. */
      s.onerror = function() { window.__tawkLoaded = false; };
      (document.body || document.documentElement).appendChild(s);
    } catch (e) { window.__tawkLoaded = false; }
  }
  if (document.readyState === 'complete') {
    setTimeout(loadTawk, 2500);
  } else {
    window.addEventListener('load', function() { setTimeout(loadTawk, 2500); }, { once: true });
  }
})();
</script>
@endif
