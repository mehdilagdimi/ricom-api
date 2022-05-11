<?php
    class Model {
        protected $db;
        protected $table = "";
        // private $query;
        
        public function __construct(){
            $this->db = new Database;
        }

        public function getTable(){
            // $query = "SELECT * FROM " . $table; 
            $this->db->query("SELECT * FROM " . $this->table);
            // $this->db->query("SELECT * FROM vols");
            $result = $this->db->resultSet();
            return $result;
        }

        public function getTableOrder($userRef, $c, $orderBy){ 
            $this->db->query("SELECT * FROM " . $this->table . " WHERE userRef=:userRef ORDER BY " . $c . " $orderBy");
            //bind params
            $this->db->bind(":userRef", $userRef);
            $result = $this->db->resultSet();
            return $result;
        }

        public function getSpecific($col, $constraint, $orderBy){
            $this->db->query("SELECT * FROM $this->table WHERE $col = :constrnt ORDER BY :ordrby DESC");
            $this->db->bind(":constrnt",$constraint);
            $this->db->bind(":ordrby", $orderBy);

            $result = $this->db->resultSet();
            return $result;
        }

        public function getSpecificMultiple($constraints){
            $query = "SELECT * FROM $this->table WHERE ";
            $numOfConstraints = count($constraints);

            foreach($constraints as $col => $c ){
                $numOfConstraints--;
                $query .= "$col = '$c'";
                if($numOfConstraints > 0){
                    $query .= " AND ";
                }
            }
            $this->db->query($query);
            $result = $this->db->resultSet();
            return $result;
        }
        

        public function getRecordHighestID($id){
            $this->db->query("SELECT * FROM $this->table WHERE $id = (SELECT max($id) FROM $this->table)");
            $record = $this->db->single();
            $maxID = $record->$id;
            return $maxID;
        }

        public function delete($id, $val){
            $this->db->query("DELETE FROM $this->table WHERE $id = '$val'");
            $this->db->execute();
        }

        // public function addRecord($constraints_arr){
        //     $this->db->query("INSERT INTO ")
        // }
        
        // public function getSpecificRows(){
        //     $this->db->query("SELECT * FROM " . $this->table . " WHERE " . $c_1 . " = ");
        // }
        //get row with multuple constraints
        // public function getRow(...$constraints){
        //     // $len = count($constraints);
        //     $constraints_sql = "";
        //     foreach($constraints as $c){
        //         $constraints_sql +=  
        //     }
        //     $this->db->query("SELECT * FROM " . $this->table . "WHERE " . $constraints[] );
        //     $result = $this->db->resultSet();
        //     return $result;
        // }

    }
?>