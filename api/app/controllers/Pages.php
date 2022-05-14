<?php
    class Pages extends Controller{
        public function __construct(){
            $this->userModel = $this->model('User');
        }

        public function index(){ //you can get parsed params in every controller's methods after using call_user_func_array
 
            if(isset($_SESSION['loggedIn'])){
                if($_SESSION['loggedIn']){
                 
                        // header("location:" . URLROOT . "flights/index");
                }
            } else {
                // $this->login();
                $this->signup();
            }
        }
        


        public function signup(){
            //sign in
            $this->view('pages/signup');
        }

        public function login(){
            $this->view('pages/login');
            // header("location:" . URLROOT . "logins");
        }
        
        public function contact(){
            // echo 'contact page';
            $this->view('pages/contact');
        }

    }
?>