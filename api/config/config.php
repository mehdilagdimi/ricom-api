<?php
    //Data base params
    define ('DB_HOST', 'localhost');
    define ('DB_USER', 'mehdilagdimi');
    define ('DB_PASS', '1234');
    define ('DB_NAME', 'ricom');
    define('DB_PORT', '5432');

    // echo __FILE__ . "<br>" ;
    // echo dirname(dirname(__FILE__));
    //app root
    define('APPROOT', dirname(dirname(__FILE__)));
    //url root
    define('URLROOT', 'http://localhost/ricom api/api/');
    //css root
    // define ('CSSROOT', dirname(dirname(dirname(__FILE__))) . '\public\css');
    //img src root
    // define ('IMGROOT', dirname(dirname(dirname(__FILE__))) . '\public\img');

    //sitename
    define('SITENAME', 'RICOM');
    
    session_start();
?>