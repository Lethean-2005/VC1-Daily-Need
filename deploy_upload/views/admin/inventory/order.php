<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 if (isset($_SESSION['user_id'])) : ?>
    <h1>Welcome to Order management</h1>

<?php 
else: 
    $this->redirect("/login"); 
endif;   
?>