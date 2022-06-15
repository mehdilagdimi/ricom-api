<?php

require 'Authenticate.php';

class Studies extends Controller
{
    public $modality_id;
    public $patient_id;
    

    public function __construct()
    {
        $this->studyModel = $this->model('Study');
    }

    //default method
    public function index()
    {
    }

    public function getStudy($orderID)
    {
        $auth = new Authenticate();
        $this->response = [];
        
        if ($auth->validate_jwt('physician', $this->response, false) || $auth->validate_jwt('radiologist', $this->response, false) || $auth->validate_jwt('headofdepart', $this->response, false)) {

            list($result, $count) = $this->studyModel->getStudyByOrderID($orderID);
            if ($result) {
                $this->response += ["msg" => "Fetched Study ID successfully", "data" => $result, "recordsTotal" => $count, "orderID" => $orderID];
                // die(var_dump($this->response));
                echo json_encode($this->response);
                exit;

            } else {

                $this->response += ["msg" => "Failed Fetching Study ID", "userID" => $orderID];
                echo json_encode($this->response);
                header('HTTP/1.1 401 Unauthorized');
                exit;
            };

        } else {
            echo json_encode("Access denied");
        }
        
    }



    public function deleteOrders()
    {
    }
}
