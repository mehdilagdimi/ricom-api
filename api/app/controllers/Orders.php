<?php
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json");
// header('Access-Control-Allow-Credentials: true');
// header("Access-Control-Allow-Methods: GET, POST, UPDATE");
// header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Credentials, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

require 'Authenticate.php';

class Orders extends Controller
{
    public $physician_id;
    public $patient_id;
    public $radiologist;
    public $status;
    private $order;
    public $response;

    public function __construct()
    {
        $this->orderModel = $this->model('Order');
    }

    //default method
    public function index()
    {
    }

    public function getOrder($order)
    {
        // htmlspecialchars(strip_tags($order));
        // $this->orderModel->getOrder($order);
    }

    public function getOrders($userID, $currentPage, $limit)
    {
        $offset = intval($currentPage) * intval($limit);
        $auth = new Authenticate();
        $this->response = [];
        
        //get orders for physician
        if ($auth->validate_jwt('physician', $this->response, false)) {
            // $data = json_decode(file_get_contents("php://input"));
            list($result, $count) = $this->orderModel->getOrdersByUserID($userID, $limit, $offset);
           

            if ($result) {
                $this->response += ["msg" => "Fetched Orders successfully", "data" => $result, "recordsTotal" => $count, "userID" => $userID];
                // die(var_dump($this->response));
                echo json_encode($this->response);
                exit;
            } else {
                $this->response += ["msg" => "Failed Fetching Orders successfully", "userID" => $userID];
                echo json_encode($this->response);
                header('HTTP/1.1 401 Unauthorized');
                exit;
            };
        } 
        //get orders for head of department
        else if ($auth->validate_jwt('headofdepart', $this->response, false)) {
            // die(var_dump("test"));
            list($result, $count) = $this->orderModel->getOrdersLimited($limit, $offset);
           
            if ($result) {
                $this->response += ["msg" => "Fetched Orders successfully", "data" => $result, "recordsTotal" => $count, "userID" => $userID];
                // die(var_dump($this->response));
                echo json_encode($this->response);
                exit;
            } else {
                $this->response += ["msg" => "Failed Fetching Orders successfully", "userID" => $userID];
                echo json_encode($this->response);
                header('HTTP/1.1 401 Unauthorized');
                exit;
            };
        }
        
    }

    public function storeOrder()
    {
        $auth = new Authenticate();
        $this->response = [];
        //Creating new order only physicians
        if ($auth->validate_jwt('physician', $this->response, false)) {
            // $this->response['response'] = "Successfully authenticated";
            // $this->response['role'] = "physician";
            $data = json_decode(file_get_contents("php://input"));
            // var_dump($data);
            // die();
            if ($data) {
                $this->physician_id = $data->userID;
                // die(var_dump($this->physician_id));
                $this->patient_id = $data->patientID;
                $this->order = $data->order;
                $this->status = strtoupper("pending");

                $result = $this->orderModel->addOrder($this->physician_id, $this->patient_id, $this->order, $this->status);
                if ($result === 1) {
                    // $this->response['msg'] = "Order added successfully";
                    // $this->response['order'] = $this->order;
                    $this->response += ["msg" => "Order added successfully", "order" => $this->order];
                    echo json_encode($this->response);
                } else if ($result === -1) {
                    // array_push($this->response, array("msg" => "Error creating order", "order" => $this->order));
                    $this->response += ["msg" => "Error creating order", "order" => $this->order];
                    echo json_encode($this->response);
                }
                // else if (!$result) {
                //     echo json_encode(array("msg" => "Order already exists", "email" => $this->email));
                // }
            } else {
                echo json_encode("No data has been received from client");
            }
        } else {
            echo json_encode("Access denied");
        }
    }


    public function deleteOrders()
    {
    }
}
