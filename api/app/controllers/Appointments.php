<?php
     header("Access-Control-Allow-Origin: *");
     header("Content-Type: application/json");
     header("Access-Control-Allow-Methods: GET, POST, DELETE");
     header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

    require_once "Slots.php";
    class Appointments extends Controller{
        private $userRef;
        public $slot;
        public $slotid;
        public $startTime;
        public $apptmntDate;
        private $apptmntID;

        public function __construct (){
            $this->apptmntModel = $this->model('Appointment');
            $this->slotModel = $this->model('Slot');
        }

        public  $userAppointments = [];

        //default
        public function index($id){
            $this->display($id);
        }
           

        public function display($id = null){
            // $data = json_decode(file_get_contents("php://input"));

            // echo json_encode($id);
            // return; 
            $this->userRef = $id;
            $apptmnts = $this->apptmntModel->getApptmnts($this->userRef);

            if($apptmnts){
                foreach($apptmnts as $apptmnt){
                    $apptmnt->starttime = $this->slotModel->getSlot($apptmnt->slotid);
                    // echo json_encode($apptmnt->starttime);
                }
                echo json_encode($apptmnts);
            }
            else {
                // echo json_encode($id);
                // echo json_encode($apptmnts);
                echo json_encode(array("msg" => "User has not made any appoinment"));
            }

        }

        public function getReserved($date = null){
            if($date){
                $this->apptmntDate = htmlspecialchars(strip_tags($date));
                $apptmnts = $this->apptmntModel->getSpecific("apptmntdate", $this->apptmntDate, $this->apptmntDate);

                if($apptmnts){
                    $timeArr = [];

                    foreach($apptmnts as $apptmnt){
                        $time = $this->slotModel->getSlot($apptmnt->slotid);
                        array_push($timeArr, $time);
                    }
                    echo json_encode($timeArr);
                    // foreach($timeArr as $slotid){
                    //     $this->slotModel->getSlot($slotid);
                    // }
                }
                return;
            }
        }


        public function makeAppointment(){

        $data = json_decode(file_get_contents("php://input"));
        // echo json_encode($data);
        // return; 

        if($data) {
            $this->userRef = $data->userRef;
            $this->slot = $data->startTime;
            $this->apptmntDate = $data->apptmntDate;

            $this->slotid = $this->slotModel->getSlotID($this->slot);

            $result = $this->apptmntModel->addAppointment($this->userRef,  $this->slotid, $this->apptmntDate);
            if($result === 1){
                echo json_encode(array("msg" => "Appointment added successfully", "userRef" => $this->userRef));
            } else if(!$result) {
                echo json_encode(array("msg" => "Appointment already exists", "userRef" => $this->userRef));
            } else if($result === -1) {
                echo json_encode(array("msg"=>"Error creating appointment", "userRef" => $this->userRef));
            }

        } else {
            echo json_encode("No data has been received from frontend");
        };            
            
        }

        public function deleteAppointment() {
            $data = json_decode(file_get_contents("php://input"));
            if($data){
                $this->apptmntID = $data->apptmntid;
                
                    if($this->apptmntModel->deleteAppointment($this->apptmntID)){
                        echo json_encode("Appointment successfully deleted");
                    } else {
                        echo json_encode("Failed to delete appointment/Invalid id");
                    }
            
            }
           

        }
    }
?>