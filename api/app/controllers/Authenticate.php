<?php
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json");
// header("Access-Control-Allow-Methods: GET, POST, UPDATE");
// header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");


// require_once '../vendor/autoload.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) .'/generate_jwt.php';

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
            if($this->login($data)){
                if($this->token){
                    echo json_encode($this->token);
                }
            } else {
                echo json_encode("Invalid credentials");
            }
            
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
            $this->role = $result->role;
            $this->generate_jwt();
            return true;
        } else {
            return false;
        }
    }

    public function generate_jwt()
    {
            $this->jwt = new JWTGenerate("localhost/ricom api/api", $this->role);
            $this->token = $this->jwt->generate();
    }

    public function validate_jwt($role)
    {
        if (JWTGenerate::validate($role)) {
            echo "success";
            return true;
            // header("location:" . URLROOT . "$this->role/index");
        };
    }
}
