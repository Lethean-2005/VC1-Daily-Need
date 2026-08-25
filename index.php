<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    require("Router/admin_route.php");
}
?>