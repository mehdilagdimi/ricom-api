<?php
class User extends Model
{

    public $userID;
    private $email;
    private $role;
    private $fName;
    private $lName;
    private $bDate;
    private $passw;


    public function __construct()
    {
        parent::__construct();
        $this->table = 'users';
    }

    // public function getUsers()
    // {
    //     return $this->getTable();
    // }

    public function getUsers($userID, $limit, $offset, $role)
    {
        $this->user_id = htmlspecialchars($userID);
        $limit = htmlspecialchars($limit);
        $offset = htmlspecialchars($offset);
        // $col = htmlspecialchars($col);
        $res = $this->getSpecificLimited(null, null, "createdat", $limit, $offset);
        $count = $this->getUsersCount()->count;
        forEach($res as $r){
            $r->createdat = date("Y-m-d h:m", strtotime($r->createdat));
            // die(var_dump($res->createdat));
        }
        return array($res, $count);
    }

    public function getUsersCount(){
        // $this->table = 'physician_orders';
        $this->db->query("SELECT count(*) FROM $this->table");
        //much faster query (estimate)
        // $this->db->query("SELECT reltuples AS estimate FROM pg_class WHERE relname = $this->table");
        $res = $this->db->single();            
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }

    public function archiveUser($userID){
        $this->userID = htmlspecialchars($userID);
        $archive = true;

        $this->db->query("UPDATE $this->table SET archive = :archive WHERE id = :userID");

        $this->db->bind(':archive', $archive);
        $this->db->bind(':userID', $this->userID);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function addUser($fName, $lName, $role, $email, $phone, $bDate = null, $passw)
    {
        $this->role = htmlspecialchars(strip_tags($role));
        $this->email = htmlspecialchars(strip_tags($email));
        $this->phone = htmlspecialchars(strip_tags($phone));
        $this->fName = htmlspecialchars(strip_tags($fName));
        $this->lName = htmlspecialchars(strip_tags($lName));
        $this->bDate = htmlspecialchars(strip_tags($bDate));
        $this->passw = htmlspecialchars(strip_tags($passw));

        $archive = false;

        //check is user already exists
        // if ($this->getUser($this->email, $this->passw)) {
        if ($this->getUser($this->email)) {
            return false;
        };

        if ($bDate !== null) {
            $this->table = "patient";
            $this->db->query('INSERT INTO ' . $this->table . ' (fName, lName, "role", email, phone, birthDate, passw, archive) VALUES (:fName, :lName, :rle, :email, :phone, :birthDate, :passw, :archive)');
            $this->db->bind(':birthDate', $this->bDate);
            $this->table = "users";
        } else {
            $this->db->query('INSERT INTO '  . $this->table . ' (fname, lname, "role", email, phone, passw, archive) VALUES (:fName, :lName, :rle, :email, :phone, :passw, :archive)');
        }

        // Reserved PSQL keyword inside a single quoted query in PHP : 
        // $this->db->query('INSERT INTO ' . $this->table . ' (fName, "role") VALUES (:fName, :rle)');

        // // Reserved PSQL keyword inside a single quoted query in PHP :
        //     # In postgreSQL column names are converted to lowercase so case doesn't matter 
        //     # Though reserved word 'role' is  the way it is written (case sensitive)
        //     # PHP var can't be recognised inside single quotes       
        // $this->db->query('INSERT INTO ' . $this->table . ' (fName, "role") VALUES (:fName, :rle)');

        // // Reserved PSQL keyword inside a double quoted query : PHP var is recognised inside quotes
        // $this->db->query("INSERT INTO  $this->table  (fName, roole) VALUES (:fName, :roole)");



        $this->db->bind(':fName', $this->fName);
        $this->db->bind(':lName', $this->lName);
        $this->db->bind(':rle', $this->role);
        $this->db->bind(':email', $this->email);
        $this->db->bind(':phone', $this->phone);

        $this->db->bind(':passw', $this->passw);
        $this->db->bind(':archive', $archive);

        // echo "success bind";
        // die();

        if ($this->db->execute()) {
            return 1;
        } else {
            return -1;
        }
    }

    // public function getUser($email, $passw)
    public function getUser($email)
    {
        // $this->db->query("SELECT * FROM $this->table WHERE email=:email AND passw =:passw");
        $this->db->query("SELECT * FROM $this->table WHERE email=:email");
        $this->db->bind(':email', $email);
        // $this->db->bind(':passw', $passw);
        $result = $this->db->single();
        return $result;
    }

    public function getUsersByRole($role, $attrb)
    {
        if ($role == 'PATIENT') {
            $this->table = 'patient';
        }
        $this->db->query('SELECT '. $attrb . ' FROM '. $this->table. ' WHERE "role"=:rle');
        $this->db->bind(':rle', $role);
        $result = $this->db->resultSet();
        $this->table = 'users';
        if($this->db->rowCount() > 0){
            return $result;
        } else {
            return false;
        }
       
        
    }


    // public function deleteUser($userID){
    //     $this->db->query("DELETE FROM $this->table WHERE userID='$userID'");
    //     $this->db->execute();
    // }

}
