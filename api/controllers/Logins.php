<?php

        class Logins extends Controller{
            
            public function __construct(){
                $this->userModel = $this->model('User');
            }

            public function index(){
                $this->verifyLogin();
            }
            
            public function verifyLogin(){
  
                if(isset($_POST['email'])){
                    if(!empty($checkAdmin = $this->adminModel->getAdmin($_POST['email'], $_POST['passw']))){ 
                        // echo 'inside admin test';
                        $user = 'admin'; 
                        $userID = $checkAdmin[0]->adminID;
                        // $header = 'dashboard/index';
                       $this->login($user, $userID);

                    } elseif (!empty($checkUser = $this->userModel->getUser($_POST['email'], $_POST['passw']))){
                        // echo 'inside user test';
                        $user = 'user';
                        // echo $checkUser[0]->userID;
                        $userID = $checkUser[0]->userID; 
                        $this->login($user, $userID);

                    } else {
                        $_SESSION['loggedIn'] = false;
                        echo "Invalid login";
                        $this->view('pages/login', "User not found");
                    }
                }
                else {
                    // echo 'Enter an email to log in';
                    $this->view('pages/login');
                }
             }

             public function login($user, $userID){
    
                        $_SESSION['privilege'] = $user;
                        $_SESSION["$user"] = $_POST['email'];
                        $_SESSION['loggedIn'] = true;  
                        $_SESSION['userID'] = $userID;
                        header("location:" . URLROOT . "flights/index");
            
             }
             
             public function logout(){
                // session_start();
                session_unset();
                session_destroy();
                if(isset($_COOKIE)){
                    // unset($_COOKIE);
                    // var_dump($_COOKIE);
                // $_COOKIE = empty($_COOKIE);

                    foreach($_COOKIE as $key => $val){
                        unset($_COOKIE[$key]);
                        echo $key;
                        setcookie($key, null, -1, '/');
                    }
                }

                header("location:" . URLROOT . "logins"); 
             }

        }
?>