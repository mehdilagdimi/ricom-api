<?php
    Class Slots extends Controller{
        public function __construct(){
            $this->slotModel = $this->model('Slot');
        }

    //default method
        public function index(){
      
        }
        
        public function getSlot($slotID){
            htmlspecialchars(strip_tags($slotID));
            $this->slotModel->getSlot($slotID);
        }

        public function getSlots(){

        }
         

        public function deleteSlots(){
  
         }
    }

?>