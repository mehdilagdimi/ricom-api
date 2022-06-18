<?php

    require 'DicomSlice.php';

    class Study extends Model{
        public $modality_id;
        public $patient_id;
        public $order_id;
        public $serie_id;
        public $sliceModel;
    
        public function __construct(){
            parent::__construct();
            $this->table = 'serie';
            $this->sliceModel = new DicomSlice();
        }

        

        public function getOrders(){
            return $this->getTable();
        }

        public function checkIfStudyExist($orderID) {
            $this->order_id = htmlspecialchars($orderID);
            // $res = $this->getSpecificLimited("order_id", $orderID, "addedat", $limit, $offset);
            $this->db->query('SELECT 1 FROM '. $this->table. ' WHERE order_id=:order_id');

            $this->db->bind(":order_id", $this->order_id);
            $this->db->execute();
            if($this->db->rowCount() > 0){
                return true;
            } else {
                return false;
            }

        }
            
        public function setOrderID($orderID){
            $this->order_id = htmlspecialchars($orderID);
                // $res = $this->getSpecificLimited("order_id", $orderID, "addedat", $limit, $offset);
            if(!$this->checkIfStudyExist($orderID)){
                $this->db->query('INSERT INTO '. $this->table. ' (order_id) VALUES (:order_id)');
                $this->db->bind(":order_id", $this->order_id);
                
                if ($this->db->execute()) {
                    return 1;
                } else {
                    return -1;
                }
            } else {
                return 1;
            }

        }

        public function getSerieID($orderID){
            $this->order_id = htmlspecialchars($orderID);
            $this->db->query('SELECT id FROM '. $this->table. ' WHERE order_id=:order_id');

            $this->db->bind(":order_id", $this->order_id);
            $res = $this->db->single();

            if ($res) {
                return $res;
            } else {
                return false;
            }
        }

        public function getStudyBySerieID($serieID){
            $this->serie_id = htmlspecialchars($serieID);      
            $slices = $this->sliceModel->getSlices($this->serie_id);
            $count = $this->sliceModel->getSlicesCount($this->serie_id)->count;
            return array($slices, $count);
        }

        public function storeStudy($serieID, $serieName){
            $this->serie_id = htmlspecialchars($serieID);
            // echo $serieID;
            // echo $serieName;
            // die();
            // $serieI
            $this->db->query('UPDATE '.$this->table. ' SET name=:name WHERE id =:serieID');
            
            $this->db->bind(':serieID', $this->serie_id);
            $this->db->bind(':name', $serieName);

            if ($this->db->execute()) {
                return 1;
            } else {
                return -1;
            }
         }
         
        public function storeSlice($serieID, $sliceName){
            $this->serie_id = htmlspecialchars($serieID);
            $res = $this->sliceModel->storeSlices($this->serie_id, $sliceName);     
            return $res; 
         }
         
         public function getStudyCount($serieID) {
            $this->serie_id = htmlspecialchars($serieID);

            $count = $this->sliceModel->getSlicesCount($this->serie_id)->count;

            return $count;
         }
        // public function getStudiesCount($col, $val){
                 
        //     if ($res) {
        //         return $res;
        //     } else {
        //         return false;
        //     }
        // }
        
        // public function getseriesByRadID($userID, $limit, $offset){
        //     $this->radiologist_id = htmlspecialchars($userID);
        //     $limit = htmlspecialchars($limit);
        //     $offset = htmlspecialchars($offset);
        //     $this->table = 'physician_orders';
        //     $res = $this->getSpecificLimited("radiologist_id", $this->radiologist_id, "addedat", $limit, $offset);
        //     $count = $this->getOrdersCount()->count;
        //     // die(var_dump($this->getOrdersCount()->count));
        //     $this->table = 'examinationOrder';
        //     return array($res, $count);
        // }

        // public function getOrderByID($orderID){

        //     $this->db->query("SELECT * FROM $this->table WHERE id=:id");
            // $this->db->bind(":id", $orderID);
        //     $record = $this->db->single();
        //     return $record;
        //  }

        // public function updateOrderRadID($orderID, $radID){
        //     $orderID = htmlspecialchars($orderID);
        //     $radID = htmlspecialchars($radID);

        //     $this->db->query("UPDATE $this->table SET radiologist_id = :radID WHERE id=:id");
        //     $this->db->bind(":id", $orderID);
        //     $this->db->bind(":radID", $radID);       
        //     if ($this->db->execute()) {
        //         return 1;
        //     } else {
        //         return -1;
        //     }
        //  }

        // public function updateOrderState($orderID, $status){
        //     $this->orderID = htmlspecialchars($orderID);
        //     $this->status = strtoupper(htmlspecialchars($status));

        //     $this->db->query("UPDATE $this->table SET status = :status WHERE id=:id");
        //     $this->db->bind(":id", $this->orderID);
        //     $this->db->bind(":status", $this->status);       
        //     if ($this->db->execute()) {
        //         return 1;
        //     } else {
        //         return -1;
        //     }
        //  }

         
       
         
    }
