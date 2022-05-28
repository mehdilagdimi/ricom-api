<?php

declare(strict_types=1);
require 'bootstrap.php';

require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

// $jwt = new JWTGenerate("localhost/ricom api/api", "admin");
// echo $jwt->generate();

// get the local secret key

class JWTGenerate
{
    private $secret;
    private $payload;
    public $serverName;
    public $issuedAt;
    public $expire;

    public function __construct($serverName = "localhost/ricom api/api", $role)
    {
        $this->secret = $_ENV["SECRET"];
        $this->issuedAt   = new DateTimeImmutable();
        $this->expire     = $this->issuedAt->modify('+60 minutes')->getTimestamp();      // Add 60 mins
        $this->serverName = $serverName;
        $this->role = $role;
    }

    // Create the token payload and generate jwt
    public function generate()
    {
        $this->payload = [
            'role' => $this->role,
            'iat'  => $this->issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $this->serverName,                       // Issuer
            // 'nbf'  => $this->issuedAt->modify('+20 minutes')->getTimestamp(),         // Not before
            'nbf'  => $this->issuedAt->getTimestamp(),         // Not before
            'exp'  => $this->expire,                           // Expire
        ];
        // echo $_ENV["SECRET"];
        // exit();
        // JWT::$leeway += 60;
        return JWT::encode($this->payload, $this->secret, 'HS512');
    }

    private static function getToken()
    {
        // $bearerToken = null;
        // if (isset($_SERVER['AUTHORIZATION'])) {
        //     // echo "1";
        //     $bearerToken = $_SERVER['AUTHORIZATION'];
        // } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        //     // echo "2";
        //     $bearerToken = $_SERVER["HTTP_AUTHORIZATION"];
        // } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        //     // echo "3";
        //     $bearerToken = $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];
        // }
        // // echo $bearerToken;
        // // die();
        // return $bearerToken;
        if(isset($_COOKIE["jwt"])){
            if($_COOKIE["jwt"] === "" || $_COOKIE["jwt"] === null || $_COOKIE["jwt"] === 0 || $_COOKIE["jwt"] === false){
                // echo $_COOKIE["jwt"];
                echo 'Login to authenticate';
                exit;
            } else {
                // echo $_COOKIE["jwt"];
                return $_COOKIE["jwt"];
            }
            
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Token not found in request';
            exit;
        }  
    }

    public static function validate($role)
    {
        if(!$tokenInCookie = JWTGenerate::getToken()){
            echo "Token not found";
            exit();
        };
        // echo "<br> TOKEN " . $token;
        // die();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'UPDATE') {
            // if (!preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            //     header('HTTP/1.0 400 Bad Request');
            //     echo 'Token not found in request';
            //     exit;
            // }
            
            // $jwt = $matches[1];
            $jwt = $tokenInCookie;
            if (!$jwt) {
                // No token was able to be extracted from the authorization header
                header('HTTP/1.0 400 Bad Request');
                // return false;
                exit;
            }
            // echo $jwt;
            // die();
            $secret = $_ENV['SECRET'];

            try {
                $token = JWT::decode($jwt, new Key($secret, 'HS512'));
                //  echo strval($token->role);
                // die();
            } catch (ExpiredException $e) {
                echo "Expired Token ";
                echo "Please sign-in";
                // return false;
                exit;
            }
            // echo $token;
            // die();
            $now = new DateTimeImmutable();
            $serverName = "localhost/ricom api/api";

            if (
                $token->role !== $role ||
                $token->iss !== $serverName ||
                $token->nbf > $now->getTimestamp() ||
                $token->exp < $now->getTimestamp()
            ) {
                header('HTTP/1.1 401 Unauthorized');
                // return false;
                exit;
            } else {
                return true;
            }
        }
    }
    // Create the token header
    // $header = json_encode([
    //     'typ' => 'JWT',
    //     'alg' => 'HS512'
    // ]);
    // echo "secret" . $secret;


}
// // Encode Header
// $base64UrlHeader = base64UrlEncode($header);

// // Encode Payload
// $base64UrlPayload = base64UrlEncode($payload);

// // Create Signature Hash
// $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);

// // Encode Signature to Base64Url String
// $base64UrlSignature = base64UrlEncode($signature);

// // Create JWT
// $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

// echo "Your token:\n" . $jwt . "\n";