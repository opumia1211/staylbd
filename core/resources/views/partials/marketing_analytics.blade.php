@php
    $general = $general ?? gs();
@endphp

{{-- Google Analytics 4 --}}
@if($general->ga4_id)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $general->ga4_id }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $general->ga4_id }}');
</script>
@endif

{{-- Facebook Pixel --}}
@if($general->fb_pixel_id)
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f.fbq)f.fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{{ $general->fb_pixel_id }}');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $general->fb_pixel_id }}&ev=PageView&noscript=1"/></noscript>
@endif

{{-- TikTok Pixel (Bonus for Growth) --}}
@if($general->tt_pixel_id)
<script>
!function (w, d, t) {
  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","detach","updateEventID"],ttq.setAndVerifyHooks=function(t,e){ttq.instance=function(s,a){for(var i=ttq.methods,o=0;o<i.length;o++)t(ttq,i[o]);return e(ttq,s,a)}};var e,s;ttq.instance=ttq.setAndVerifyHooks(function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}},function(t,e,s){return t._i=t._i||{},t._i[e]=t._i[e]||[],t._i[e].push(s),t}),ttq.load=function(e,s){var n="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]={status:0,active:!0,integrations:{sku:{},ad:{},user:{},page:{}}},n+="?sdkid="+e+"&lib="+t;var a=d.createElement("script");a.type="text/javascript",a.async=!0,a.src=n;var i=d.getElementsByTagName("script")[0];i.parentNode.insertBefore(a,i)};
  ttq.load('{{ $general->tt_pixel_id }}');
  ttq.page();
}(window, document, 'ttq');
</script>
@endif
