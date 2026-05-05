<?php
// ============================================================
// ShowPilot — Audio Daemon Status Proxy
// ============================================================
// The browser can't directly fetch http://pi-ip:8090/health from
// the FPP plugin UI because it's a cross-origin request (different
// port = different origin) and browsers block it. This PHP script
// runs server-side on the Pi and proxies the request to 127.0.0.1,
// returning the result to the browser as same-origin JSON.
// ============================================================

header('Content-Type: application/json');
header('Cache-Control: no-store');

// Read port from plugin config, default 8090
$configFile = '/home/fpp/media/config/plugin.showpilot';
$port = 8090;
if (file_exists($configFile)) {
    $lines = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (preg_match('/^audioDaemonPort\s*=\s*"?(\d+)"?/', $line, $m)) {
            $port = (int)$m[1];
            break;
        }
    }
}

$url = "http://127.0.0.1:{$port}/health";

$ctx = stream_context_create([
    'http' => [
        'timeout' => 3,
        'ignore_errors' => true,
    ]
]);

$result = @file_get_contents($url, false, $ctx);

if ($result === false) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'daemon not reachable']);
} else {
    // Pass through the daemon's response directly
    echo $result;
}
