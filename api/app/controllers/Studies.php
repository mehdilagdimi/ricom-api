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

    public function setOrderIDStudy($orderID)
    {
        $auth = new Authenticate();
        $this->response = [];
        
        if ($auth->validate_jwt($auth->validate_jwt('radiologist', $this->response, false))) {

            $result = $this->studyModel->setOrderID($orderID);

            if ($result) {
                $this->response += ["msg" => "Linked Order with Study successfully", "data" => $result, "orderID" => $orderID];
                // die(var_dump($this->response));
                echo json_encode($this->response);
                exit;

            } else {

                $this->response += ["msg" => "Failed linking Study with Order", "orderID" => $orderID];
                echo json_encode($this->response);
                header('HTTP/1.1 401 Unauthorized');
                exit;
            };

        } else {
            echo json_encode("Access denied");
        }
        
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

                $this->response += ["msg" => "Failed Fetching Study ID", "orderID" => $orderID];
                echo json_encode($this->response);
                header('HTTP/1.1 401 Unauthorized');
                exit;
            };

        } else {
            echo json_encode("Access denied");
        }
        
    }

    public function storeStudy($serieID)
    {
        $auth = new Authenticate();
        $this->response = [];
        
        if ($auth->validate_jwt('radiologist', $this->response, false)) {
            $data = json_decode(file_get_contents("php://input"));
            // echo json_encode(var_dump($data->studyData->study));
            // die();
            $DIR = 'C:/xampp/htdocs/RICOM api/dicom/';

            $file_chunks = explode(';base64,', $data->studyData->study);
            $file_type = explode('image/', $file_chunks[0]);
            $img_type = $file_type[1];
            $base64Img = base64_decode($file_chunks[1]);

            $filePath = $DIR . uniqid() . '.jpg';
            file_put_contents($filePath, $base64Img);
            die();
            list($result, $count) = $this->studyModel->getStudyByserieID($serieID);
            if ($result) {
                $this->response += ["msg" => "Fetched Study ID successfully", "data" => $result, "recordsTotal" => $count, "serieID" => $serieID];
                // die(var_dump($this->response));
                echo json_encode($this->response);
                exit;

            } else {

                $this->response += ["msg" => "Failed Fetching Study ID", "serieID" => $serieID];
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
