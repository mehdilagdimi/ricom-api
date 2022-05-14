<?php
    class User extends Model{

        private $email;
        private $role;
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
     
        public function addUser($fName, $lName, $role, $email, $phone, $bDate = null, $passw){ 
            $this->role = htmlspecialchars(strip_tags($role));
            $this->email = htmlspecialchars(strip_tags($email));
            $this->phone = htmlspecialchars(strip_tags($phone));
            $this->fName = htmlspecialchars(strip_tags($fName));
            $this->lName = htmlspecialchars(strip_tags($lName));
            $this->bDate = htmlspecialchars(strip_tags($bDate));
            $this->passw = htmlspecialchars(strip_tags($passw));
            
            //check is user already exists
            if($this->getUser()){
                return false;
            };

            if($this->bDate !== null){
                $this->db->query("INSERT INTO $this->table (fName, lName, [role], email, phone, birthDate, passw) VALUES (:fName, :lName, :[role], :email, :phone, :birthDate, :passw)");
                // --    ON CONFLICT (userRef) DO NOTHING");
                $this->db->bind(':birthDate', $this->bDate);
            } else {
                $this->db->query("INSERT INTO $this->table (fName, lName, [role], email, phone, passw) VALUES (:fName, :lName, :[role], :email, :phone, :passw)");
            }
               
            $this->db->bind(':role', $this->role);
            $this->db->bind(':email', $this->email);
            $this->db->bind(':phone', $this->phone);
            $this->db->bind(':fName', $this->fName);
            $this->db->bind(':lName', $this->lName);
            $this->db->bind(':passw', $this->passw);

            if($this->db->execute()){
                return 1;
            } else {
                return -1;
            }
        }

        public function getUser(){
                $this->db->query("SELECT * FROM $this->table WHERE email=:email");
                $this->db->bind(':email', $this->email);
                $result = $this->db->single();
                return $result;
        }
 
        // public function deleteUser($userID){
        //     $this->db->query("DELETE FROM $this->table WHERE userID='$userID'");
        //     $this->db->execute();
        // }
       
    }
