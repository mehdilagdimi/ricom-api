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
        
        public function getSlot($slotID){
            // $this->slotID =  $slotID;

            // $this->db->query("SELECT startTime FROM $this->table WHERE slotID=:slotID");
            // $this->db->bind(":slotID", $this->slotID);

            // $record = $this->db->single();
            // // echo json_encode($record->starttime);
            // // return;
            // return $record->starttime;
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
         
        public function getSlotID($startTime){
            // $this->startTime = htmlspecialchars(strip_tags($startTime));

            // $this->db->query("SELECT slotID FROM $this->table WHERE startTime=:startTime");
            // $this->db->bind(":startTime", $this->startTime);

            // $record = $this->db->single();
            // return $record->slotid;
         }

        
        public function deleteSlot($SlotID){
            // $this->db->query("DELETE FROM $this->table WHERE SlotID='$SlotID'");
            // $this->db->execute();
         }
        
         
    }
    
?>