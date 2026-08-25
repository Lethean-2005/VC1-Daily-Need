<?php
    //class for base admin controller
    class BaseAdminController{


        //$view the view file to render
        //$data the data to be passed to the view
        protected function view($view, $data = []){
            $this->requireAdmin();
            extract($data);
            ob_start();
            require __DIR__ . "/../../views/{$view}.php";
            $content = ob_get_clean();
            require __DIR__ . "/../../views/layoutAdmin.php";
        }

        //Block access unless an authenticated admin session exists
        protected function requireAdmin(){
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
                $this->redirect('/admin-login');
            }
        }


        //redirect to a specific location
        //$url The URL to redirect to
        protected function redirect($url){
            header("Location: $url");
            exit();
        }
    }
?>