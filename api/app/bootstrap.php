<?php 
// require ('vendor\vlucas\phpdotenv\src\Dotenv.php');
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// $dotenv = new DotEnv(__DIR__);
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// echo $_ENV["SECRET"];
// function base64UrlEncode($text)
// {
//     return str_replace(
//         ['+', '/', '='],
//         ['-', '_', ''],
//         base64_encode($text)
//     );
// }

