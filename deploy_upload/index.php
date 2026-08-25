
<?php
// TEMPORARY debug: reveal any hidden PHP fatal error (blank-screen cause).
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
// Remove these 5 lines once the site works.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
    require("Router/admin_route.php");
}
?>