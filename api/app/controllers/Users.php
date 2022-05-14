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


    public function showUsers()
    {
    }

    public function signup()
    {
        $auth = new Authenticate();
        if ($auth->validate_jwt("admin")) {
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
                    echo json_encode(array("msg" => "User added successfully", "email" => $this->email));
                } else if (!$result) {
                    echo json_encode(array("msg" => "User already exists", "email" => $this->email));
                } else if ($result === -1) {
                    echo json_encode(array("msg" => "Error creating user", "email" => $this->email));
                }
            } else {
                echo json_encode("No data has been received from frontend");
            }
        } else {
            echo json_encode("Access denied");
        }
    }

    public function deleteUser()
    {
    }
}
