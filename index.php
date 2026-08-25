<?php
// When running under PHP's built-in server with an explicit router script
// (php -S host:port index.php, used by Railway/Docker), every request —
// including real static files like images, CSS, and fonts — gets routed
// through this script unless we explicitly hand real files back to the
// server. Apache/.htaccess deployments (InfinityFree) never hit this branch.
if (php_sapi_name() === 'cli-server') {
    $path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $fullPath = __DIR__ . $path;
    if ($path !== '/' && is_file($fullPath)) {
        return false;
    }
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
    require("Router/admin_route.php");
}
?>