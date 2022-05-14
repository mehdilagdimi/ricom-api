<?php
//core app class
    class Core{
        protected $currentController = 'Pages'; //also Pages is default controller
        protected $currentMethod = 'index'; //also index is default method
        protected $params = [];

        public function __construct(){
            // print_r($this->getUrl());
            $url = $this->getUrl();
            //look for first value in controllers folder, ucwords function will capitalize first letter
            if(isset($url[0])){
                if(file_exists('../app/controllers/' . ucwords($url[0]) . '.php')){
                    // echo ucwords($url[0]);
                    //set a new controller
                    $this->currentController = ucwords($url[0]);
                    unset($url[0]);
                }    
            }
          
            //require the controller
            require_once '../app/controllers/' . $this->currentController . '.php';
            $this->currentController = new $this->currentController; 
            //check fpr secprd ûrl param
            if(isset($url[1])){
                if(method_exists($this->currentController, $url[1])){
                    $this->currentMethod = $url[1];
                    unset($url[1]);
                }
            }
            //get params
            $this->params = $url ? array_values($url): [];

            //array of params
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        }
        public function getUrl(){
            if(isset($_GET['url'])){
                $url = rtrim($_GET['url'], '/');

                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);  
                return $url;
            }
        }
    }
?>