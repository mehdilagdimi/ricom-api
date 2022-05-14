<?php

declare(strict_types=1);
require 'bootstrap.php';

require 'vendor/autoload.php';

use Firebase\JWT\JWT;

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
        $this->secret = strval(getenv('SECRET'));
        $this->issuedAt   = new DateTimeImmutable();
        $this->expire     = $this->issuedAt->modify('+6 minutes')->getTimestamp();      // Add 60 seconds
        $this->serverName = $serverName;
        $this->role = $role;
    }

    // Create the token payload and generate jwt
    public function generate()
    {
        $this->payload = [
            'user_id' => $this->user_id,
            'role' => $this->role,
            'iat'  => $this->issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $this->serverName,                       // Issuer
            'nbf'  => $this->issuedAt->getTimestamp(),         // Not before
            'exp'  => $this->expire,                           // Expire
        ];

        return JWT::encode($this->payload, $this->secret, 'HS512');
    }

    private static function getToken(){
        $bearerToken = "null";
        if (isset($_SERVER['AUTHORIZATION'])) {
            echo "1";
            $bearerToken = $_SERVER['AUTHORIZATION'];
        }elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            echo "2";
            $bearerToken = $_SERVER["HTTP_AUTHORIZATION"];
        }elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            echo "3";
           $bearerToken = $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];
        }
        // echo $bearerToken;
        // die();
       return $bearerToken;  
    }

    public static function validate($role)
    {
        $bearerToken = JWTGenerate::getToken();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' ||$_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'UPDATE') {
            if (!preg_match('/Bearer\s(\S+)/', $bearerToken, $matches)) {
                header('HTTP/1.0 400 Bad Request');
                echo 'Token not found in request';
                exit;
            }

            $jwt = $matches[1];
            if (!$jwt) {
                // No token was able to be extracted from the authorization header
                header('HTTP/1.0 400 Bad Request');
                exit;
            }

            $secret = strval(getenv('SECRET'));
            $token = JWT::decode($jwt, $secret, ['HS512']);
            $now = new DateTimeImmutable();
            $serverName = "localhost/ricom api/api";

            if (
                $token->role !== $role ||
                $token->iss !== $serverName ||
                $token->nbf > $now->getTimestamp() ||
                $token->exp < $now->getTimestamp()
            ) {
                header('HTTP/1.1 401 Unauthorized');
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