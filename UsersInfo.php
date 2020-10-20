
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
     * Helps to display the userdetails when user inputs --dry_run
     */
    public function displayUserForDryRun()
    {
        $this->name = ucwords(strtolower($this->name));
        $this->surname = ucfirst(strtolower($this->surname));
        $this->email = strtolower($this->email);
        $email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        $this->name = trim($this->name);
        $this->surname = trim($this->surname);
        $this->email = trim($this->email);
        $mask = "| %-10s| %-10s| %-25s|\n";
        printf($mask, $this->name, $this->surname, $this->email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
        $email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        $this->name = trim($this->name);
        $this->surname = trim($this->surname);
        $this->email = trim($this->email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $Database->insertIntoUSerTable($this);
        } else {
            return " $email address format is invalid \n";
        }
    }
}