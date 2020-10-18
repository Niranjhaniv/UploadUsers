<?php
class Database
{
    private $hostname;
    private $uname;
    private $passwd;
    private $dbname;
    private $mysqlconnection;

    public function __construct($dbconfig) {
        
        $this->hostname = $dbconfig["hostname"];
        $this->username = $dbconfig["uname"];
        $this->password = $dbconfig["passwd"];
        $this->dbname = "php_task";
        try {
            $this->mysqlconnection = new mysqli($this->hostname, $this->username,$this->password,$this->dbname);
            return $this->mysqlconnection;
        } catch(Exception $error) {
            return $error;
        }
    }

    public function getmySqlConnection(){
        return $this->mysqlconnection;
    }

    public function createUserTable()
    {
        if ($this->checkUserTable() === false) {
            $createUSerTable  = "CREATE TABLE IF NOT EXISTS users (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(50) NOT NULL,surname VARCHAR(50) NOT NULL,email VARCHAR(100) NOT NULL,UNIQUE KEY UNIQUE_EMAIL (email))";
            if ($this->mysqlconnection->query($createUSerTable) === true) {
                return "Created User table Successfully! \n";
            } else {
                return "Error while creating the user table " . $this->mysqlconnection->error;
            }
        } else {
            return "Table already exists! \n";
        }
    }

    public function checkUserTable()
    {
        $checkUserTable = "SELECT * FROM users";
        return $this->mysqlconnection->query($checkUserTable);
    }

    public function insertIntoUSerTable($userDetails){

        $insertSql = "INSERT INTO users (name, surname, email) VALUES (?,?,?)";
        $query = $this->mysqlconnection->prepare($insertSql);
        if ($query){
            $query->bind_param('sss',$user->name,$user->surname,$user->email);
            $query->execute();
            return $query;
        } else {
            return "Error while inserting the record: " .$this->mysqlconnection->error."\n";
        }

    }

}