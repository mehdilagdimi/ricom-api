<?php
    class Slot extends Model{
        public $slotID;
        public $startTime;

        public function __construct(){
            parent::__construct();
            $this->table = 'slotsOrder';
        }

        

        public function getSlots(){
            return $this->getTable();
        }

        public function getSlot($slotID){
            $this->slotID =  $slotID;

            $this->db->query("SELECT startTime FROM $this->table WHERE slotID=:slotID");
            $this->db->bind(":slotID", $this->slotID);

            $record = $this->db->single();
            // echo json_encode($record->starttime);
            // return;
            return $record->starttime;
         }
         
        public function getSlotID($startTime){
            $this->startTime = htmlspecialchars(strip_tags($startTime));

            $this->db->query("SELECT slotID FROM $this->table WHERE startTime=:startTime");
            $this->db->bind(":startTime", $this->startTime);

            $record = $this->db->single();
            return $record->slotid;
         }

        
        public function deleteSlot($SlotID){
            $this->db->query("DELETE FROM $this->table WHERE SlotID='$SlotID'");
            $this->db->execute();
         }
        
         
    }
    
?>