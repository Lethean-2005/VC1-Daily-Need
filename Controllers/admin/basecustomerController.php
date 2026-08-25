<?php
    //class for base admin controller
    class BasecustomerController{


        //$view the view file to render
        //$data the data to be passed to the view
        protected function view($view, $data = []){
            extract($data);
            ob_start();
            require __DIR__ . "/../../views/{$view}.php";
            $content = ob_get_clean();
            require __DIR__ . "/../../views/layoutcust.php";
        }


        //redirect to a specific location
        //$url The URL to redirect to
        protected function redirect($url){
            header("Location: $url");
            exit();
        }
    }
?>