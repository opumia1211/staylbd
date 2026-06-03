@php
    $feDv = $social->data_values ?? null;
    $feDv = is_array($feDv) ? (object) $feDv : ($feDv ?? (object) []);
    $sUrl = trim((string) ($feDv->url ?? ''));
    $sUrl = $sUrl !== '' ? $sUrl : '#';
    $customIcon = trim((string) ($feDv->custom_icon ?? ''));
    if ($customIcon === '0' || strtolower($customIcon) === 'null') {
        $customIcon = '';
    }
    $customIconRel = $customIcon !== '' && preg_match('#^[a-zA-Z0-9._-]+$#', $customIcon)
        ? 'assets/images/frontend/social_icon/' . $customIcon
        : '';
    $customIconAbs = $customIconRel !== '' && function_exists('public_path')
        ? public_path($customIconRel)
        : '';
    $useCustomImg = $customIconAbs !== '' && is_file($customIconAbs);
    $inlineCustom = trim((string) ($feDv->custom_icon_svg ?? ''));
    $iconStored = trim((string) ($feDv->icon ?? ''));
    $iconClassAttr = '';
    if ($iconStored !== '') {
        if (preg_match('/<i\b[^>]*\bclass\s*=\s*(["\'])([^"\']*)\1/i', $iconStored, $im)) {
            $iconClassAttr = trim(preg_replace('/\s+/', ' ', html_entity_decode($im[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        } elseif (preg_match('/<i\b[^>]*\bclass\s*=\s*([^\s>]+)/i', $iconStored, $im)) {
            $iconClassAttr = trim(preg_replace('/\s+/', ' ', html_entity_decode($im[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        } else {
            $iconClassAttr = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($iconStored, ENT_QUOTES | ENT_HTML5, 'UTF-8'))));
        }
    }
    $iconClassSafe = ($iconClassAttr !== '' && preg_match('#^[a-zA-Z0-9 _\-.]+$#u', $iconClassAttr)) ? $iconClassAttr : '';
    $iconRawLower = strtolower($iconClassSafe);
    $useLibraryIcon = $iconClassSafe !== '' && (
        preg_match('/\b(fab|far|fas|fa-brands|fa-solid|fa-regular|lab|lar|las|lal)\b/', $iconRawLower)
        || str_contains($iconRawLower, 'fa-')
        || str_contains($iconRawLower, 'la-')
    );
    $socialLabel = trim((string) ($feDv->title ?? ''));
    $socialIconName = match (true) {
        $iconRawLower === '' => 'link',
        str_contains($iconRawLower, 'facebook') => 'facebook',
        str_contains($iconRawLower, 'instagram') => 'instagram',
        str_contains($iconRawLower, 'youtube') => 'youtube',
        str_contains($iconRawLower, 'linkedin') => 'linkedin',
        str_contains($iconRawLower, 'whatsapp') => 'whatsapp',
        str_contains($iconRawLower, 'telegram') => 'telegram',
        str_contains($iconRawLower, 'pinterest') => 'pinterest',
        str_contains($iconRawLower, 'tiktok') => 'tiktok',
        str_contains($iconRawLower, 'github') => 'github',
        str_contains($iconRawLower, 'discord') => 'discord',
        str_contains($iconRawLower, 'reddit') => 'reddit',
        str_contains($iconRawLower, 'spotify') => 'spotify',
        str_contains($iconRawLower, 'snapchat'), str_contains($iconRawLower, 'threads') => 'link',
        str_contains($iconRawLower, 'twitter'), str_contains($iconRawLower, 'x-twitter'), str_contains($iconRawLower, 'x.com') => 'x-twitter',
        str_contains($iconRawLower, 'envelope') => 'envelope',
        default => 'link',
    };
@endphp
<a href="{{ $sUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLabel !== '' ? $socialLabel : __('Social link') }}" class="footer-social-link">
    @if($inlineCustom !== '')
        <span class="footer-social-inline">{!! $inlineCustom !!}</span>
    @elseif($useCustomImg)
        <img src="{{ getImage($customIconRel, '96x96') }}" alt="" width="22" height="22" class="footer-social-image" loading="lazy" decoding="async">
    @elseif($useLibraryIcon)
        <i class="{{ $iconClassSafe }} footer-social-font-icon" aria-hidden="true"></i>
    @elseif($iconStored !== '' && str_contains($iconStored, '<svg'))
        <span class="footer-social-inline">{!! $iconStored !!}</span>
    @else
        @include($activeTemplate . 'partials.icon', ['name' => $socialIconName, 'sizePx' => 22])
    @endif
</a>
