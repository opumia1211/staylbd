# Downloads Lucide SVGs (main branch) into New1/lucide-ecommerce/
# Requires: curl.exe (Windows 10+). Run: powershell -ExecutionPolicy Bypass -File .\download_lucide_icons.ps1
$ErrorActionPreference = 'Stop'
$base = 'https://raw.githubusercontent.com/lucide-icons/lucide/main/icons'
$outDir = Join-Path $PSScriptRoot 'lucide-ecommerce'
New-Item -ItemType Directory -Force -Path $outDir | Out-Null

function Download-One {
    param([string]$LucideName, [string]$OutName)
    $url = "$base/$LucideName"
    $dest = Join-Path $outDir $OutName
    $r = & curl.exe -sSL -o $dest -w "%{http_code}" $url
    if ($r -ne '200' -or -not (Test-Path $dest) -or ((Get-Item $dest).Length -lt 20)) {
        if (Test-Path $dest) { Remove-Item $dest -Force }
        return $false
    }
    return $true
}

$pairs = @(
    @{ try = @('search.svg'); out = 'search_icon.svg' },
    @{ try = @('mic.svg'); out = 'voice_search_icon.svg' },
    @{ try = @('scan-search.svg', 'scan-line.svg', 'camera.svg'); out = 'image_search_icon.svg' },
    @{ try = @('house.svg', 'home.svg'); out = 'home_icon.svg' },
    @{ try = @('layout-grid.svg'); out = 'categories_icon.svg' },
    @{ try = @('package.svg'); out = 'products_icon.svg' },
    @{ try = @('smartphone.svg'); out = 'contact_icon.svg' },
    @{ try = @('truck.svg'); out = 'track_order_icon.svg' },
    @{ try = @('languages.svg', 'globe.svg'); out = 'language_icon.svg' },
    @{ try = @('bell.svg'); out = 'notification_icon.svg' },
    @{ try = @('heart.svg'); out = 'wishlist_icon.svg' },
    @{ try = @('arrow-left-right.svg'); out = 'compare_icon.svg' },
    @{ try = @('shopping-cart.svg'); out = 'cart_icon.svg' },
    @{ try = @('zap.svg', 'shopping-bag.svg'); out = 'buy_now_icon.svg' },
    @{ try = @('clipboard-list.svg'); out = 'orders_icon.svg' },
    @{ try = @('circle-user.svg', 'user.svg'); out = 'login_icon.svg' },
    @{ try = @('user-plus.svg'); out = 'register_icon.svg' },
    @{ try = @('wallet.svg', 'banknote.svg'); out = 'transactions_icon.svg' },
    @{ try = @('messages-square.svg', 'message-circle.svg'); out = 'messages_icon.svg' },
    @{ try = @('mail.svg'); out = 'mail_icon.svg' },
    @{ try = @('star.svg'); out = 'review_icon.svg' },
    @{ try = @('badge-check.svg', 'circle-user-round.svg'); out = 'profile_icon.svg' },
    @{ try = @('key-round.svg', 'key.svg'); out = 'change_password_icon.svg' },
    @{ try = @('log-out.svg'); out = 'logout_icon.svg' },
    @{ try = @('eye.svg'); out = 'quick_view_icon.svg' },
    @{ try = @('credit-card.svg'); out = 'policy_payment_icon.svg' },
    @{ try = @('package-search.svg', 'truck.svg'); out = 'policy_shipping_icon.svg' },
    @{ try = @('list-ordered.svg', 'clipboard-list.svg'); out = 'policy_order_icon.svg' },
    @{ try = @('tag.svg'); out = 'section_brand_icon.svg' },
    @{ try = @('chevrons-up.svg', 'arrow-big-up-dash.svg'); out = 'scroll_top_icon.svg' }
)

$ok = 0
$fail = @()
foreach ($p in $pairs) {
    $done = $false
    foreach ($t in $p.try) {
        if (Download-One -LucideName $t -OutName $p.out) {
            Write-Host "OK $($p.out) <= $t"
            $ok++
            $done = $true
            break
        }
    }
    if (-not $done) {
        $fail += $p.out
        Write-Warning "FAILED $($p.out)"
    }
}

Write-Host "`nDownloaded $ok / $($pairs.Count) into $outDir"
if ($fail.Count) {
    Write-Warning "Missing: $($fail -join ', ')"
    exit 1
}
exit 0
