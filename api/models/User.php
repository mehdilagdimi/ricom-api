<?php
    class User extends Model{

        private $userRef;
        private $fName;
        private $lName;
        private $bDate;
        private $passw;


        public function __construct(){
            parent::__construct();
            $this->table = 'users';
        }

        public function getUsers(){
            return $this->getTable();
        }
     
        public function addUser($userRef, $fName, $lName, $birthDate, $passw){ 
            

            // $this->db->query("SELECT * FROM $this->table WHERE userRef = :userRef)");
          
            // $this->db->bind(':userRef', $userRef);
            // if($this->db->execute()){
            //     $this->db->query("INSERT INTO $this->table (userRef, fName, lName, birthDate, passw) VALUES (:userRef, :fName, :lName, :birthDate, :passw);
            //     ");
            // }    
            // else {
            //     echo json_encode("Error creating user");
            //     return false;
            // }
            $this->userRef = htmlspecialchars(strip_tags($userRef));
            $this->fName = htmlspecialchars(strip_tags($fName));
            $this->lName = htmlspecialchars(strip_tags($lName));
            $this->bDate = htmlspecialchars(strip_tags($birthDate));
            $this->passw = htmlspecialchars(strip_tags($passw));
            
            //check is user already exists
            $this->getUser($this->userRef, $this->fName, $this->lName, $this->bDate, $this->passw );
            if($this->db->rowCount() > 0) {
                // echo json_encode(array("msg" => "User already exists", "userRef" => $this->userRef));
                // return false;
                return false;
            }

            $this->db->query("INSERT INTO $this->table (userRef, fName, lName, birthDate, passw) VALUES (:userRef, :fName, :lName, :birthDate, :passw)
               ON CONFLICT (userRef) DO NOTHING
                ");
                //  UPDATE 
                //  SET userRef = :userRef
                //  WHERE NOT EXISTS (SELECT 1 FROM $this->table WHERE userRef = :Ref)
            // IF NOT FOUND THEN 
            //     RAISE 'Can't add this user', :userRef;
            // END IF;

            $this->db->bind(':userRef', $this->userRef);
            // $this->db->bind(':Ref', $userRef);
            $this->db->bind(':fName', $this->fName);
            $this->db->bind(':lName', $this->lName);
            $this->db->bind(':birthDate', $this->bDate);
            $this->db->bind(':passw', $this->passw);
            if($this->db->execute()){
                // echo json_encode(array("msg" => "User added successfully", "userRef" => $this->userRef));
                return 1;
            } else {
                // echo json_encode($this->db->error);
                // echo json_encode(array("msg" => "Error creating user", "userRef" => $this->userRef));
                return -1;
            }
        }

        public function getUser(){

                $this->db->query("SELECT * FROM $this->table WHERE userRef=:userRef");
                $this->db->bind(':userRef', $this->userRef);
                $result = $this->db->resultSet();
                return $result;
        }
 
        // public function deleteUser($userID){
        //     $this->db->query("DELETE FROM $this->table WHERE userID='$userID'");
        //     $this->db->execute();
        // }
       
    }
?> 
