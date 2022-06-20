<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

require 'Authenticate.php';


class Users extends Controller
{
    private $userRef;
    private $fName;
    private $lName;
    private $bDate = null;
    private $passw;


    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    //call showUsers because index is set up as default method so to make showUsers as default when requesting from Users class
    public function index($params = [])
    {
        $this->signup($params);
    }

    public function getUsers($userID, $currentPage, $limit, $role)
    {
        $offset = intval($currentPage) * intval($limit);
        // die(var_dump($role));
        $auth = new Authenticate();
        $this->response = [];

        //get users for admin
        if ($auth->validate_jwt('admin', $this->response, false)) {

            list($result, $count) = $this->userModel->getUsers($userID, $limit, $offset, $role);

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

    public function signup()
    {
        $auth = new Authenticate();
        $this->response = [];
        //Creating new user is done only by admin
        if ($auth->validate_jwt('admin', $this->response, false)) {
            $data = json_decode(file_get_contents("php://input"));

            if ($data) {
                $this->fName = strtoupper($data->fName);
                $this->lName = strtoupper($data->lName);
                $this->role = strtoupper($data->role);
                $this->email = $data->email;
                $this->phone = $data->phone;
                $this->passw = $data->passw;
                if (isset($data->birthDate)) {
                    $this->bDate = $data->birthDate;
                }

                // $this->passw = hashFunction('sha256', $_POST['passw']);
                $this->passw = hashFunction('sha256', $this->passw);

                $result = $this->userModel->addUser($this->fName, $this->lName, $this->role, $this->email, $this->phone, $this->bDate,  $this->passw);

                if ($result === 1) {
                    $this->response += array("msg" => "User added successfully", "email" => $this->email);
                    echo json_encode($this->response);

                } else if (!$result) {
                    $this->response += array("msg" => "User already exists", "email" => $this->email);
                    echo json_encode($this->response);
                } else if ($result === -1) {
                    echo json_encode(array("msg" => "Error creating user", "email" => $this->email));
                }
            } else {
                echo json_encode("No data has been received from client");
            }
        } else {
            echo json_encode("Access denied");
        }
    }

    public function getPatientById()
    {
        $role = 'PATIENT';
        $attrb = 'id_p';
        $data = $this->userModel->getUsersByRole($role, $attrb);

        if ($data) {
            echo json_encode(['response' => 'Records found', "patients_id" => $data]);
        } else {
            echo json_encode(["response" => "No record was found"]);
        }
    }

    public function getUser($userID)
    {
        $data = $this->userModel->getUserById($userID);
        $data->createdat = date("Y-m-d h:m", strtotime($data->createdat));
        // echo var_dump($data);
        // die();
        if ($data) {
            echo json_encode(['response' => 'User found', "user" => $data]);
        } else {
            echo json_encode(["response" => "User not found", "user_id" => $userID]);
        }
    }

    public function archiveUser($userID)
    {
        // $role = 'PATIENT';
        // $attrb = 'id_p';
        $res = $this->userModel->archiveUser($userID);

        if ($res) {
            echo json_encode(['response' => 'User archived successfully', "orderID" => $userID]);
        } else {
            echo json_encode(["response" => "Failed to archive user"]);
        }
    }

    public function getRadiologists()
    {
        $role = 'RADIOLOGIST';
        $attrb = 'id, fname, lname';
        $data = $this->userModel->getUsersByRole($role, $attrb);

        if ($data) {
            echo json_encode(['response' => 'Records found', "radiologists" => $data]);
        } else {
            echo json_encode(["response" => "No record was found"]);
        }
    }
    // public function login()
    // {
    //     $data = json_decode(file_get_contents("php://input"));
    //     if($data){
    //         $this->email = $data->email;
    //         $this->passw = $data->passw;
    //         $this->passw = hashFunction('sha256', $this->passw);

    //         $user = $this->userModel->getUser($this->email, $this->passw);

    //         //validate jwtoken
    //         $auth = new Authenticate();
    //         if ($auth->validate_jwt($user->role)) {
    //             if($user->role !== "admin"){
    //                 die("success");
    //                 header("location:" . URLROOT . ucfirst($user->role) . 's');
    //             }
    //         }
    //     } else {
    //         echo "No data was sent";
    //         exit();
    //     }

    // }


    public function deleteUser()
    {
    }
}
