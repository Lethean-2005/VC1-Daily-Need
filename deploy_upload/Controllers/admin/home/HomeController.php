<?php
    class HomeController extends BaseAdminController{
        public function index(){
            $this->view('admin/home/homeView');
        }

        //somepage
        public function somepage(){
            $this->view('admin/inventory/some_page');
        }
    }
    
?>