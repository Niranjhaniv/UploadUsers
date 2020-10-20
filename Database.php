<?php
class Database
{
    private $hostname;
    private $uname;
    private $passwd;
    private $dbname;
    private $mysqlconnection;

    /**
     * Sets the sql connection with the database.
     * @param array $dbconfig
     * return sqlconnection
     */

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
    /**
     * Get the sql connection with the database.
     */
    public function getmySqlConnection(){
        return $this->mysqlconnection;
    }
    /**
     * Creates the user table.
     */
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
    /**
     * check if the user table exists.
     */
    public function checkUserTable()
    {
        $checkUserTable = "SELECT * FROM users";
        return $this->mysqlconnection->query($checkUserTable);
    }
    /**
     * Inserts the user data into table.
     * @param array $userDetails
     * @return Object $query result 
     */
    public function insertIntoUSerTable($userDetails){

        $insertSql = "INSERT INTO users (name, surname, email) VALUES (?,?,?)";
        $query = $this->mysqlconnection->prepare($insertSql);
        if ($query){
            $query->bind_param('sss',$userDetails->name,$userDetails->surname,$userDetails->email);
            $query->execute();
            return $query;
        } else {
            return "Error while inserting the record: " .$this->mysqlconnection->error."\n";
        }

    }

}