<?php
require_once('Slot.php');

class Appointment extends Model
{
    public $slotId;
    public $apptmntDate;
    private $userRef;
    public $createdAt;
    private $apptmntID;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'appointments';
    }

    public function getApptmnts($userRef)
    {
        $this->userRef = htmlspecialchars(strip_tags($userRef));
        $c = "apptmntDate";

        $data = $this->getTableOrder($this->userRef, $c, 'ASC');
        if ($this->db->rowCount() > 0) {
            return $data;
        } else {
            return false;
        }
    }

    public function getApptmnt($slotId, $apptmntDate)
    {
       
        $this->db->query("SELECT 1 FROM $this->table WHERE slotID=:slotID AND apptmntDate=:apptmntDate");
        $this->db->bind(':slotID', $slotId);
        $this->db->bind(':apptmntDate', $apptmntDate);
        $result = $this->db->single();
        return $result;
    }
 

    public function addAppointment($userRef, $slotId, $apptmntDate)
    {
        // echo $this->table;
        $this->userRef = htmlspecialchars(strip_tags($userRef));
        $this->slotId = htmlspecialchars(strip_tags($slotId));
        $this->apptmntDate = htmlspecialchars(strip_tags($apptmntDate));

        $this->getApptmnt($this->slotId, $this->apptmntDate);
        if($this->db->rowCount() > 0){
            return false;
        }

        $this->db->query("INSERT INTO $this->table (userRef, slotID, apptmntDate) VALUES ('$this->userRef', '$this->slotId', '$this->apptmntDate')");
        if ($this->db->execute()) {
            return 1;
        } else {
            return -1;
        };
    }

    public function deleteAppointment($apptmntID) {
        $this->apptmntID = htmlspecialchars(strip_tags($apptmntID));
        $this->db->query("DELETE FROM $this->table WHERE apptmntID = :apptmntID");
        $this->db->bind(":apptmntID", $this->apptmntID);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        };
    }

}
