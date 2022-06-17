<?php

   
    class DicomSlice extends Model{
        public $modality_id;
        public $patient_id;
        public $order_id;
    
        public function __construct(){
            parent::__construct();
            $this->table = 'dicomslice';
        }

        

        public function getOrders(){
            return $this->getTable();
        }

    
        public function getSlices($serieID){
            $this->serie_id = htmlspecialchars($serieID);
            $res = $this->getSpecific("serie_id", $this->serie_id, "name");

            if ($res) {
                return $res;
            } else {
                return false;
            }
        }

        public function getSlicesCount($serieID){
            $this->db->query("SELECT count(*) FROM $this->table WHERE $serieID =:val");
            $this->db->bind(":val", $serieID);
            $res = $this->db->single();  
            if ($res) {
                return $res;
            } else {
                return false;
            }
        }

        
         
       
         
    }
