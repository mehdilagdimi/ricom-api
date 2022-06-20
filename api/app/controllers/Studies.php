<?php

require 'Authenticate.php';

class Studies extends Controller
{
    public $modality_id;
    public $patient_id;
    private $slices;
    

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
        
        if ($auth->validate_jwt('radiologist', $this->response, false) || $auth->validate_jwt('headofdepart', $this->response, false)) {
            
            $result = $this->studyModel->setOrderID($orderID);
            $serieID = $this->studyModel->getSerieID($orderID);
            // echo "test ;" . $serieID->id;
            // die();
            if ($result) {
                $this->response += ["msg" => "Linked Order with Study successfully", "data" => $result, "serieID" => $serieID];
                // die(var_dump($this->response));
                echo json_encode($this->response);
                exit;

            } else {

                $this->response += ["msg" => "Failed linking Study with Order", "serieID" => $serieID];
                echo json_encode($this->response);
                exit;
            };

        } else {
            echo json_encode("Access denied");
        }
        
    }

    public function getStudyID($orderID)
    {
        $auth = new Authenticate();
        $this->response = [];
        
        if ($auth->validate_jwt('physician', $this->response, false) || $auth->validate_jwt('radiologist', $this->response, false) || $auth->validate_jwt('headofdepart', $this->response, false)) {

            $serieID = $this->studyModel->getSerieID($orderID);

            if ($serieID) {
                $this->response += ["msg" => "Fetched Study ID successfully", "serieID" => $serieID];
                // die(var_dump($this->response));
                echo json_encode($this->response);
                exit;

            } else {

                $this->response += ["msg" => "Failed Fetching Study ID", "orderID" => $orderID];
                echo json_encode($this->response);
                // header('HTTP/1.1 401 Unauthorized');
                exit;
            };

        } else {
            echo json_encode("Access denied");
        }
        
    }

    public function getStudy($serieID)
    {
        $auth = new Authenticate();
        $this->response = [];
        
        if ($auth->validate_jwt('physician', $this->response, false) || $auth->validate_jwt('radiologist', $this->response, false) || $auth->validate_jwt('headofdepart', $this->response, false)) {

            list($result, $count) = $this->studyModel->getStudyBySerieID($serieID);
            if ($result) {
                $this->response += ["msg" => "Fetched Study successfully", "data" => $result, "recordsTotal" => $count, "serieID" => $serieID];
                // die(var_dump($this->response));
                echo json_encode($this->response);
                exit;

            } else {

                $this->response += ["msg" => "Failed Fetching Study", "serieID" => $serieID];
                echo json_encode($this->response);
                // header('HTTP/1.1 401 Unauthorized');
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
            $this->slices = $data->studyData->study;
            $serieName = 'serie' . $serieID;

            $DIR = 'C:/xampp/htdocs/RICOM api/dicom/' . $serieName . "/";
            
            if(!file_exists($DIR)){
                mkdir($DIR);
            }

            foreach($this->slices as $key => $slice){
                $file_chunks = explode(';base64,', $slice);
                $file_type = explode('image/', $file_chunks[0]);
                $img_type = $file_type[1];
                $base64Img = base64_decode($file_chunks[1]);
    
                // $filePath = $DIR . uniqid() . '.jpg';
                $sliceName = $key . "." . $img_type;
                $filePath = $DIR . $sliceName;
                
                file_put_contents($filePath, $base64Img);

                $result = $this->studyModel->storeSlice($serieID, $sliceName);

                if (!$result) {
                    $this->response += ["msg" => "Failed Storing Slice : $key ", "data" => $slice];
                    echo json_encode($this->response);
                    exit;
                }
            }
            // echo $s
            $result = $this->studyModel->storeStudy($serieID, $serieName);
            // echo $result;
            // die();
            $count = $this->studyModel->getStudyCount($serieID);
            // die();
            if ($result) {
                $this->response += ["msg" => "Stored Study successfully", "recordsTotal" => $count, "serieID" => $serieID];
                // die(var_dump($this->response));
                echo json_encode($this->response);
                exit;

            } else {

                $this->response += ["msg" => "Failed Storing Study", "serieID" => $serieID];
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
