<?php
    class  Controller {
        public function model($model){
            //require model file
            require_once '../app/models/' . $model .'.php';
            //instantiate model
            return new $model;
        }
        //load view (check for file)
        public function view($view, $data = []){
            if(file_exists('../app/views/'. $view . '.php')){
                require_once '../app/views/'. $view . '.php';
                
            } else {
                die("View does not exists");
            }
        }

        public function checkSessionLogin(){
            if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true ) {
                return true;
            } else { return false;}
        }

        public function checkSessionAdmin(){
            if(isset($_SESSION['privilege']) && $_SESSION['privilege'] == 'admin'){
                return true;
            } else { return false;}
        }
    }
?>