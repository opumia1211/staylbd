<?php

/**
 * POST admin login and save Playwright storage state (cookies).
 * Usage: php scripts/admin-browser-session.php [username] [password]
 */
$base = getenv('ADMIN_BASE') ?: 'http://localhost/sajaladminopu';
$user = $argv[1] ?? getenv('ADMIN_USER') ?: 'admin';
$pass = $argv[2] ?? getenv('ADMIN_PASS') ?: 'admin123';

$cookieFile = sys_get_temp_dir() . '/stayl_admin_cookies.txt';
@unlink($cookieFile);

$ch = curl_init(rtrim($base, '/') . '/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
    CURLOPT_FOLLOWLOCATION => true,
]);
$html = curl_exec($ch);
if ($html === false) {
    fwrite(STDERR, 'Cannot reach admin: ' . curl_error($ch) . "\n");
    exit(1);
}

preg_match('/name="_token" value="([^"]+)"/', $html, $tokenMatch);
$token = $tokenMatch[1] ?? '';
preg_match('/name="admin_login_captcha"[^>]*>.*?<span[^>]*>([A-Za-z0-9]+)</', $html, $capMatch);
$captcha = $capMatch[1] ?? '';

$post = http_build_query(array_filter([
    '_token' => $token,
    'username' => $user,
    'password' => $pass,
    'policy_confirm' => 'on',
    'admin_login_captcha' => $captcha ?: null,
]));

curl_setopt_array($ch, [
    CURLOPT_URL => rtrim($base, '/') . '/',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $post,
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
]);
curl_exec($ch);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

$cookies = [];
if (is_readable($cookieFile)) {
    foreach (file($cookieFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line[0] === '#') continue;
        $p = explode("\t", $line);
        if (count($p) >= 7) {
            $cookies[] = [
                'name' => $p[5],
                'value' => $p[6],
                'domain' => $p[0],
                'path' => $p[2],
                'httpOnly' => strtolower($p[1]) === 'true' || $p[1] === 'TRUE',
                'sameSite' => 'Lax',
            ];
        }
    }
}

$dir = __DIR__ . '/admin-layout-output';
if (!is_dir($dir)) mkdir($dir, 0777, true);
file_put_contents($dir . '/session.json', json_encode(['cookies' => $cookies, 'origins' => []], JSON_PRETTY_PRINT));

echo "Login URL: {$finalUrl}\n";
echo 'Cookies: ' . count($cookies) . "\n";

if (!str_contains($finalUrl, '2fa') && count($cookies) > 0) {
    exit(0);
}
fwrite(STDERR, "Login may need 2FA or wrong credentials. Final: {$finalUrl}\n");
exit(1);
