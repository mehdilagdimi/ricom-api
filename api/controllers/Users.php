<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

    function hashFunction  ($algo, $data) {
        return hash($algo, $data);
    }

    class Users extends Controller{
        private $userRef;
        private $fName;
        private $lName;
        private $bDate;
        private $passw;


        public function __construct(){
            $this->userModel = $this->model('User');
        }

        //call showUsers because index is set up as default method so to make showUsers as default when requesting from Users class
        public function index($params = []){
           $this->signup($params);
        }


        public function showUsers(){
          
        }

        public function signup($params = []){
     
                $data = json_decode(file_get_contents("php://input"));

                if($data) {
                    $this->fName = strtoupper($data->fName);
                    $this->lName = strtoupper($data->lName);
                    $this->bDate = $data->birthDate;
                    $this->passw = $data->passw;
    
                    // $this->passw = hashFunction('sha256', $_POST['passw']);
                    $this->passw = hashFunction('sha256', $this->passw);

                     //create user reference
                    $strToHash = "$this->fName" . "$this->lName" . "$this->bDate";       
                    $this->userRef = hashFunction('md5', $strToHash);
                    // echo $this->userRef; 
                    $result = $this->userModel->addUser($this->userRef, $this->fName, $this->lName, $this->bDate,  $this->passw);
                    if($result === 1){
                        echo json_encode(array("msg" => "User added successfully", "userRef" => $this->userRef));
                    } else if(!$result) {
                        echo json_encode(array("msg" => "User already exists", "userRef" => $this->userRef));
                    } else if ($result === -1){
                        echo json_encode(array("msg"=>"Error creating user", "userRef" => $this->userRef));
                    }

                } else {
                    echo json_encode("No data has been received from frontend");
                }
               
         }

         public function deleteUser(){
           
         }

        //  public function test($id){
        //     echo json_encode($id);
        //     return;
        //  }
  
    }
