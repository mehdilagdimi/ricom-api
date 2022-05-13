<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");


require_once('vendor/autoload.php');
require '../generate_jwt.php';


function hashFunction($algo, $data)
{
    return hash($algo, $data);
}

class Authenticate extends Controller
{
    private $email;
    private $username;
    private $passw;
    public $loggedin;
    public $user_id;
    public $role;
    public $jwt;
    public $token;

    public function __construct()
    {
        $this->userModel = $this->model("User");
    }

    public function index()
    {
        $data = json_decode(file_get_contents("php://input"));
        if ($data) {
            $this->login($data);

            if (isset($_SESSION['loggedIn'])) {
                if ($_SESSION['loggedIn']) {
                }
            } else {
                header("location:" . URLROOT . "users/index");
            }
        } else {
            echo json_encode("Failed to receive login credentials");
        }
    }

    public function login($credentials)
    {
        if (isset($credentials->email)) {
            $this->email = $credentials->email;
        } else {
            $this->username = $credentials->username;
        }
        $this->passw = $credentials->passw;
        $this->passw = hashFunction('sha256', $this->passw);

        $result = $this->userModel->getUser($this->email, $this->passw);
        
        if ($result) {
            $this->user_id = $result->id;
            $this->role = $result->role;
            $this->generate_jwt();
        } else {
            echo json_decode("Invalid credentials");
        }
    }

    public function generate_jwt()
    {
            $this->jwt = new JWTGenerate("localhost/ricom api/api", $this->user_id, $this->role);
            $this->token = $this->jwt->generate();
    }

    public function validate_jwt()
    {
        if ($this->jwt->validate()) {
            echo "success";
            header("location:" . URLROOT . "$this->role/index");
        };
    }
}
