<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET, POST, UPDATE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Credentials, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");


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
        // echo $data->passw; 
        // die();
        if ($data) {
            // echo json_encode($data);
            // exit();
            if($this->login($data)){
                if($this->token){
                    // setCookie($name="token", $value=$this->token, $httponly=true);
                    $cookie = setcookie("jwt",$this->token,0,'/','',false,true);
                    // header("Set-Cookie: samesite-test=1; expires=0; path=/; samesite=Strict");
                    if($cookie){
                        echo json_encode(["response" => "Access allowed", "jwt" => $this->token, "cookie state" => $cookie,"cookies"=>$_COOKIE]);
                    }else {
                        echo json_encode(["response" => "Failed to set cookie"]);
                    }
                }
            } else {
                echo json_encode(["response" => "Invalid credentials"]);
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

    public function testcookie($data){
        if($data){
            echo json_encode(["response" => $_COOKIE, "tst" => $data]);
        }
    }
}
