<?php
class Report extends Model
{
    public $user_id;
    // public $physician_id;
    // public $radiologist_id;
    public $order_id;
    public $physReport;
    public $radReport;
    public $status;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'report';
    }

    // public function getOrders(){
    //     return $this->getTable();
    // }
    // public function getOrdersLimited($limit, $offset){
    //     $limit = htmlspecialchars($limit);
    //     $offset = htmlspecialchars($offset);
    //     $this->table = 'physician_orders';
    //     $res = $this->getSpecificLimited(null, null, "createdat", $limit, $offset);
    //     $count = $this->getOrdersCount("TRUE" , "TRUE")->count;
    //     $this->table = 'examinationOrder';
    //     return array($res, $count);
    // }

    // public function getOrdersByUserID($userID, $limit, $offset, $role){
    //     $this->user_id = htmlspecialchars($userID);
    //     $limit = htmlspecialchars($limit);
    //     $offset = htmlspecialchars($offset);
    //     // $col = htmlspecialchars($col);
    //     $this->table = 'physician_orders';
    //     if ($role === "PHYSICIAN") {
    //         $res = $this->getSpecificLimited("physician_id", $this->user_id, "addedat", $limit, $offset);
    //         $count = $this->getOrdersCount("TRUE", "TRUE")->count;
    //     } else {
    //         $res = $this->getSpecificLimited("radiologist_id", $this->user_id, "addedat", $limit, $offset);
    //         $count = $this->getOrdersCount("radiologist_id", $this->user_id)->count;   
    //     }
    //     // die(var_dump($this->getOrdersCount()->count));
    //     $this->table = 'examinationOrder';
    //     return array($res, $count);
    // }

    public function getReportsByOrder($order_id){
        $this->order_id = htmlspecialchars($order_id);

        $this->db->query("SELECT * FROM $this->table WHERE order_id = :id");
        $this->db->bind(":id", $this->order_id);
        
        $res = $this->db->single();
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }

    // public function getOrdersCount($col, $val){
    //     // $this->table = 'physician_orders';
    //     $this->db->query("SELECT count(*) FROM $this->table WHERE $col =:val");
    //     //much faster query (estimate)
    //     // $this->db->query("SELECT reltuples AS estimate FROM pg_class WHERE relname = $this->table");
    //     $this->db->bind(":val", $val);
    //     $res = $this->db->single();            
    //     if ($res) {
    //         return $res;
    //     } else {
    //         return false;
    //     }
    // }

    // public function getOrderByID($orderID){

    //     $this->db->query("SELECT * FROM $this->table WHERE id=:id");
    //     $this->db->bind(":id", $orderID);
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
    public function reportExists($order_id)
    {
        // $this->db->query("SELECT 1 FROM " . $this->table . " WHERE order_id =:id)");
        $this->db->query("SELECT 1 FROM " . $this->table . " WHERE order_id =:id");
        $this->db->bind(':id', $order_id);

        $res = $this->db->single();
        // die(var_dump($res));
        return $res;
    }

    public function addReport($order_id, $report, $role)
    {
        $this->order_id = htmlspecialchars($order_id);
        if ($role === "RADIOLOGIST") {
            $this->radReport = htmlspecialchars($report);

            if ($this->reportExists($this->order_id)) {
                $this->db->query('UPDATE ' . $this->table . ' SET reportradiologist =:report WHERE order_id =:id');
            } else {
                $this->db->query('INSERT INTO ' . $this->table . ' (order_id, reportradiologist) VALUES (:id, :report)');
            }

            $this->db->bind(':report', $this->radReport);
        } else {
            $this->physReport = htmlspecialchars($report);
            if ($this->reportExists($this->order_id)) {
                $this->db->query('UPDATE ' . $this->table . ' SET reportphysician =:report WHERE order_id =:id');
            } else {
                $this->db->query('INSERT INTO ' . $this->table . ' (order_id, reportphysician) VALUES (:id, :report)');
            }

            $this->db->bind(':report', $this->physReport);
        }

        $this->db->bind(':id', $this->order_id);

        if ($this->db->execute()) {
            return 1;
        } else {
            return -1;
        }
    }
}
