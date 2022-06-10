<?php
    class Order extends Model{
        public $physician_id;
        public $order;
        public $status;

        public function __construct(){
            parent::__construct();
            $this->table = 'examinationOrder';
        }

        

        public function getOrders(){
            return $this->getTable();
        }
        public function getOrdersLimited($limit, $offset){
            $limit = htmlspecialchars($limit);
            $offset = htmlspecialchars($offset);
            $this->table = 'physician_orders';
            $res = $this->getSpecificLimited(null, null, "createdat", $limit, $offset);
            $count = $this->getOrdersCount()->count;
            $this->table = 'examinationOrder';
            return array($res, $count);
        }
    
        public function getOrdersByUserID($userID, $limit, $offset){
            $this->physician_id = htmlspecialchars($userID);
            $limit = htmlspecialchars($limit);
            $offset = htmlspecialchars($offset);
            $this->table = 'physician_orders';
            $res = $this->getSpecificLimited("physician_id", $this->physician_id, "addedat", $limit, $offset);
            $count = $this->getOrdersCount()->count;
            // die(var_dump($this->getOrdersCount()->count));
            $this->table = 'examinationOrder';
            return array($res, $count);
        }
        
        public function getOrdersCount(){
            // $this->table = 'physician_orders';
            $this->db->query("SELECT count(*) FROM $this->table");
            //much faster query (estimate)
            // $this->db->query("SELECT reltuples AS estimate FROM pg_class WHERE relname = $this->table");

            $res = $this->db->single();            
            if ($res) {
                return $res;
            } else {
                return false;
            }
        }
        
        public function getOrderByID($orderID){

            $this->db->query("SELECT * FROM $this->table WHERE id=:id");
            $this->db->bind(":id", $orderID);
            $record = $this->db->single();
            return $record;
         }

        public function updateOrderRadID($orderID, $radID){
            $orderID = htmlspecialchars($orderID);
            $radID = htmlspecialchars($radID);

            $this->db->query("UPDATE $this->table SET radiologist_id = :radID WHERE id=:id");
            $this->db->bind(":id", $orderID);
            $this->db->bind(":radID", $radID);       
            if ($this->db->execute()) {
                return 1;
            } else {
                return -1;
            }
         }

         public function addOrder($physician_id, $patient_id, $order, $status){
            $this->physician_id = htmlspecialchars($physician_id);
            $this->patient_id = htmlspecialchars($patient_id);
            $this->order = htmlspecialchars($order);
            $this->status = $status;

            $this->db->query('INSERT INTO '.$this->table. ' (physician_id, physician_order, status, patient_id) VALUES (:id, :order, :status, :patient_id)');
            
            $this->db->bind(':id', $this->physician_id);
            $this->db->bind(':patient_id', $this->patient_id);
            $this->db->bind(':order', $this->order);
            $this->db->bind(':status', $this->status);

            if ($this->db->execute()) {
                return 1;
            } else {
                return -1;
            }
         }
         
       
         
    }
