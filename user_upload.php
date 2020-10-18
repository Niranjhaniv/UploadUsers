<?php

/**
 * Manual for runnning this phpfile
 * @return String help message
 */

 require_once("Database.php");

function helpCommands() {
    $help  = "Manual of uploading csv files and option available \n";
    $help .= "   --file [csv file name] – The name of the CSV to be parsed\n";
    $help .= "   --create_table – this will cause the MySQL users table to be built\n";
    $help .= "   --dry_run – To be used with the --file directive to run the script.\n\t Note that this option will not alter the database\n";
    $help .= "   -u – MySQL username\n";
    $help .= "   -p – MySQL password\n";
    $help .= "   -h – MySQL host\n";
    $help .= "   --help – help you with the manual available \n";

    return $help;
}
 function createTble($dbconfig)
    {
        $Database = new Database($dbconfig);
        $mysqlconnection = $Database->getmySqlConnection();

    }
function checkAllConditions($arguments){
    $check = array();
    if(count($arguments) === 8) {

        for ($i=2; $i < count($arguments); $i++) { 
            if(strpos($arguments[$i], '-') !== false) {
                $check[$arguments[$i]] = $arguments[$i + 1];
            }
        }
            foreach ($check as $key => $val) {
                if ($key === "-u") {
                    $uname = $val;
                } else if ($key === "-p") {
                    $passwd = $val;
                } else if ($key === "-h") {
                    $hostname = $val;
                }
            }

            $dbconfig  = array(
              "hostname" => $hostname,
              "uname" => $uname,
              "passwd" => $passwd
            );

        } else {
            return "Invalid command";
        }

        return $dbconfig;


}
 function parseCommandLineArguments($arguments) {
    $option = isset($arguments[1]) ? $arguments[1] : "";

    switch ($option) {
        case "--create_table":
            $dbconfig = checkAllConditions($arguments);
            createTble($dbconfig);
        break;
        case "--file":
        break;
        case "--help":
            echo helpCommands();
        break;
        case "--dry_run":
        break;
        case "-u":
        break;
        case "-p":
        break;
        case "-h":
        break;
        default:
        break;
    };

}
parseCommandLineArguments($argv);

?>