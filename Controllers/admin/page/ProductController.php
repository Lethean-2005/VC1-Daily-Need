<?php
    class products extends BasecustomerController{
        public function index(){
            $this->view('pages/products');
        }
       
        //oral
        public function oral(){
            $this->view('pages/oral_health');
        }

        //beverage
        public function beverage(){
            $this->view('pages/beverages');
        }

        //cooking
        public function cooking(){
            $this->view('pages/cooking');
        }

        //drinking
        public function drinking(){
            $this->view('pages/drinking');
        }

        //feminine
        public function feminine(){
            $this->view('pages/feminine');
        }

        //houeshold
        public function houeshold(){
            $this->view('pages/houeshold');
        }

        //saop
        public function saop(){
            $this->view('pages/saop');
        }

        //snacks
        public function snacks(){
            $this->view('pages/snacks');
        }

        //tissue
        public function tissue(){
            $this->view('pages/tissue');
        }
    }
