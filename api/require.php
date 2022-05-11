<?php 
    //require libs
    require_once 'lib/Core.php';
    require_once 'lib/Controller.php';
    require_once 'lib/Database.php';
    require_once 'lib/Model.php';

    require_once 'config/config.php';

    //instantiate core class
    
    $init = new Core();
    // try {
    //     // Connection::get()->connect();
    //     // $db = new Database();
    //      // $init = new Core();
    //     echo 'A connection to the PostgreSQL database sever has been established successfully.';
    // } catch (\PDOException $e) {
    //     echo $e->getMessage();
    // }
?>