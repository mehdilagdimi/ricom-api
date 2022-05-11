<?php
declare(strict_types=1);
require 'bootstrap.php';

require 'vendor/autoload.php';

use Firebase\JWT\JWT;
if($loggedIn = true){echo "true";};

// get the local secret key
$secret = strval(getenv('SECRET'));

$issuedAt   = new DateTimeImmutable();
$expire     = $issuedAt->modify('+6 minutes')->getTimestamp();      // Add 60 seconds
$serverName = "localhost/ricom api/api";
$username   = "username";   

// Create the token header
// $header = json_encode([
//     'typ' => 'JWT',
//     'alg' => 'HS512'
// ]);
// echo "secret" . $secret;

// Create the token payload
$payload = [
    'user_id' => 1,
    'role' => 'admin',
    'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
    'iss'  => $serverName,                       // Issuer
    'nbf'  => $issuedAt->getTimestamp(),         // Not before
    'exp'  => $expire,                           // Expire
    'userName' => $username,       
];

echo JWT::encode($payload, $secret, 'HS512');
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