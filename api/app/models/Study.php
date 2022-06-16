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

        // public function getOrdersLimited($limit, $offset){
        //     $limit = htmlspecialchars($limit);
        //     $offset = htmlspecialchars($offset);
        //     $this->table = 'physician_orders';
        //     $res = $this->getSpecificLimited(null, null, "addedat", $limit, $offset);
        //     $count = $this->getOrdersCount("TRUE" , "TRUE")->count;
        //     $this->table = 'examinationOrder';
        //     return array($res, $count);
        // }
            
        public function setOrderID($orderID){
            $this->order_id = htmlspecialchars($orderID);
                // $res = $this->getSpecificLimited("order_id", $orderID, "addedat", $limit, $offset);
            $this->db->query('INSERT INTO '. $this->table. ' (order_id) VALUES (:order_id)');

            $this->db->bind(":order_id", $this->orderID);
            
            if ($this->db->execute()) {
                return 1;
            } else {
                return -1;
            }
        }

        public function getStudiesByOrderID($orderID){
            $this->order_id = htmlspecialchars($orderID);
            $this->serie_id = $this->getSpecific("order_id", $orderID, "createdat");
            
                // $res = $this->getSpecificLimited("order_id", $orderID, "addedat", $limit, $offset);
               
            $res = $this->sliceModel->getSlicesBySerieID($this->serie_id);
            $count = $this->sliceModel->getSlicesCount($res->serie_id)->count;
            
            // die(var_dump($this->getOrdersCount()->count));
            return array($res, $count);
        }

        
        // public function getStudiesCount($col, $val){
                 
        //     if ($res) {
        //         return $res;
        //     } else {
        //         return false;
        //     }
        // }
        
        // public function getOrdersByRadID($userID, $limit, $offset){
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

        //  public function addOrder($physician_id, $patient_id, $order, $status){
        //     $this->physician_id = htmlspecialchars($physician_id);
        //     $this->patient_id = htmlspecialchars($patient_id);
        //     $this->order = htmlspecialchars($order);
        //     $this->status = $status;

        //     $this->db->query('INSERT INTO '.$this->table. ' (physician_id, physician_order, status, patient_id) VALUES (:id, :order, :status, :patient_id)');
            
        //     $this->db->bind(':id', $this->physician_id);
        //     $this->db->bind(':patient_id', $this->patient_id);
        //     $this->db->bind(':order', $this->order);
        //     $this->db->bind(':status', $this->status);

        //     if ($this->db->execute()) {
        //         return 1;
        //     } else {
        //         return -1;
        //     }
        //  }
         
       
         
    }
