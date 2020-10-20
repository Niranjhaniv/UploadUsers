
<?php
class UserInfo {

    public $name;
    public $surname;
    public $email;

    public function __construct($name, $surname, $email) 
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
    }
    /**
     * Check the email is valid and also its domain
     */
    public function checkEmail($email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return false;
        }
         $host =  explode("@", $email);
         $hosts = end($host);
    
        if(!checkdnsrr($hosts, "MX") && !checkdnsrr($hosts, "A")){
              return false;
        }

        return true;
    }
    /**
     * Helps to display the userdetails when user inputs --dry_run
     */
    public function displayUserForDryRun()
    {
        $this->name = ucwords(strtolower($this->name));
        $this->surname = ucfirst(strtolower($this->surname));
        $this->email = strtolower($this->email);
        $email = $this->email;
        $this->name = trim($this->name);
        $this->surname = trim($this->surname);
        $this->email = trim($this->email);
        $mask = "| %-10s| %-10s| %-25s|\n";
        printf($mask, $this->name, $this->surname, $this->email);
        if (!$this->checkEmail($email)){
            echo " $email address format is invalid \n";
        } 
    }

    /**
     * check the user csv and inserts them into db
     * @param object $Database
     * @return object result
     
     */
    public function checkUsercsvToInsert($Database)
    {
        $this->name = ucwords(strtolower($this->name));
        $this->surname = ucfirst(strtolower($this->surname));
        $this->email = strtolower($this->email);
        $email = $this->email;
        $this->name = trim($this->name);
        $this->surname = trim($this->surname);
        $this->email = trim($this->email);
        if ($this->checkEmail($email)){
            return $Database->insertIntoUSerTable($this);
        } else {
            return " $email address format is invalid \n";
        }
    }
}