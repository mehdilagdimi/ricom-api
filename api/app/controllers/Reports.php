<?php

require 'Authenticate.php';

class Reports extends Controller
{
    public $serie_id;
    private $order_id;
    public $report;
    // public $radReport;

    public function __construct()
    {
        $this->reportModel = $this->model('Report');
    }

    //default method
    public function index()
    {
    }

    public function getReports($order_id)
    {
        $auth = new Authenticate();
        $this->response = [];
        //
        if ($auth->validate_jwt('radiologist', $this->response, false) || $auth->validate_jwt('physician', $this->response, false) || $auth->validate_jwt('headofdepart', $this->response, false)) {
            $this->order_id = $order_id;
            $this->report = $this->reportModel->getReportsByOrder($this->order_id);
            if ($this->report) {
                $this->response += ["msg" => "Reports fetched successfully", "data" => $this->report];
                echo json_encode($this->response);
            } else {
                $this->response += ["msg" => "Error fetching reports", "order" => $this->order_id];
                echo json_encode($this->response);
            }
        }
    }

    // public function getOrders($userID, $currentPage, $limit, $role)
    // {
    //     $offset = intval($currentPage) * intval($limit);
    //     $auth = new Authenticate();
    //     $this->response = [];

    //     //get orders for physician
    //     if ($auth->validate_jwt('physician', $this->response, false) || $auth->validate_jwt('radiologist', $this->response, false)) {
    //         // $data = json_decode(file_get_contents("php://input"));
    //         // if($role === "PHYSICIAN"){
    //             list($result, $count) = $this->orderModel->getOrdersByUserID($userID, $limit, $offset, $role);
    //         // } else {
    //         //     list($result, $count) = $this->orderModel->getOrdersByUserID($userID, $limit, $offset, "radiologist_id");
    //         // }


    //         if ($result) {
    //             $this->response += ["msg" => "Fetched Orders successfully", "data" => $result, "recordsTotal" => $count, "userID" => $userID];
    //             // die(var_dump($this->response));
    //             echo json_encode($this->response);
    //             exit;
    //         } else {
    //             $this->response += ["msg" => "Failed Fetching Orders successfully", "userID" => $userID];
    //             echo json_encode($this->response);
    //             header('HTTP/1.1 401 Unauthorized');
    //             exit;
    //         };
    //     } 
    //     //get orders for head of department
    //     else if ($auth->validate_jwt('headofdepart', $this->response, false)) {
    //         // die(var_dump("test"));
    //         list($result, $count) = $this->orderModel->getOrdersLimited($limit, $offset);

    //         if ($result) {
    //             $this->response += ["msg" => "Fetched Orders successfully", "data" => $result, "recordsTotal" => $count, "userID" => $userID];
    //             // die(var_dump($this->response));
    //             echo json_encode($this->response);
    //             exit;
    //         } else {
    //             $this->response += ["msg" => "Failed Fetching Orders successfully", "userID" => $userID];
    //             echo json_encode($this->response);
    //             header('HTTP/1.1 401 Unauthorized');
    //             exit;
    //         };
    //     }

    // }

    public function storeReport($role)
    {
        $auth = new Authenticate();
        $this->response = [];
        //Storing report only by physi and radiologist
        if ($auth->validate_jwt('radiologist', $this->response, false) || $auth->validate_jwt('physician', $this->response, false)) {
            $data = json_decode(file_get_contents("php://input"));
            // var_dump($data);
            // die();
            if ($data) {
                $this->order_id = $data->orderID;
                $this->report = $data->report;
                $role = strtoupper($role);
                $result = $this->reportModel->addReport($this->order_id, $this->report, $role);

                if ($result === 1) {
                    $this->response += ["msg" => "Report added successfully", "order" => $this->order_id];
                    echo json_encode($this->response);
                } else if ($result === -1) {
                    $this->response += ["msg" => "Error adding report", "order" => $this->order_id];
                    echo json_encode($this->response);
                }
            } else {
                echo json_encode("No data has been received from client");
            }
        } else {
            echo json_encode("Access denied");
        }
    }

    // public function assignRadiologist()
    // {
    //     // echo "hello";
    //     $auth = new Authenticate();
    //     $this->response = [];
    //     //only by head of department
    //     if ($auth->validate_jwt('headofdepart', $this->response, false)) {
    //         $data = json_decode(file_get_contents("php://input"));
    //         // var_dump($data);
    //         // die();
    //         if ($data) {
    //             $this->radiologist_id = $data->radID;
    //             $this->order_id= $data->orderID;

    //             $result = $this->orderModel->updateOrderRadID($this->order_id, $this->radiologist_id);
    //             if ($result === 1) {

    //                 $this->response += ["msg" => "Assigned Radiologist successfully", "order_id" => $this->order_id];
    //                 echo json_encode($this->response);
    //             } else if ($result === -1) {
    //                 $this->response += ["msg" => "Error assigning radiologist", "order_id" => $this->order_id];
    //                 echo json_encode($this->response);
    //             }

    //         } else {
    //             echo json_encode("No data has been received from client");
    //         }
    //     } else {
    //         echo json_encode("Access denied");
    //     }
    // }


    // public function deleteOrders()
    // {
    // }
}
