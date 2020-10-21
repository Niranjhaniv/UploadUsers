<?php

/**
 * Manual for runnning this phpfile
 * @return String help message
 */

 require_once("Database.php");
 require_once("UsersInfo.php");

function helpCommands() {
    $help  = "Manual of uploading csv files and option available \n";
    $help .= "--file [csv file name] – The name of the CSV to be parsed Eg: --file <csvname>\n";
    $help .= "--create_table – this will cause the MySQL users table to be built\n";
    $help .= "--dry_run – To be used with the --file directive to run the script.\nNote that this option will not alter the database\n";
    $help .= "-u – MySQL username\n --create_table or --file -u <uname> -p <password> -h <hostname> ";
    $help .= "-p – MySQL password\n --create_table or --file -u <uname> -p <password> -h <hostname> ";
    $help .= "-h – MySQL host\n --create_table or --file -u <uname> -p <password> -h <hostname> ";
    $help .= "--help – help you with the manual available \n";

    return $help;
}
/**
 * create the user database table
 * @param array $dbconfig
 */
function createTble($dbconfig)
{
    $Database = new Database($dbconfig);
    $mysqlconnection = $Database->getmySqlConnection();
    if ($mysqlconnection->connect_errno == 0) {
        echo $Database->createUserTable();
    }
}
/**
 * Checks the condition for inserting or creating table in the table
 * @param array $arguments
 * @return array $dbconfig Database config
 */
function checkAllConditions($arguments,$givenOptions) {
    $check = array();
    //echo count($arguments);

        if(array_key_exists("u",$givenOptions)){
            if(isset($givenOptions['u'])){
                $uname = $givenOptions['u'];
            } else {
                return null;
            }
           
        }
        if(array_key_exists("p",$givenOptions)){
            if(isset($givenOptions['p'])){
                $passwd = $givenOptions['p'];
            } else {
                return null;
            }
            
        }
        if(array_key_exists("h",$givenOptions)){

            if(isset($givenOptions['h'])){
                $hostname = $givenOptions['h'];
            } else {
                return null;
            }
           
        }
        $dbconfig  = array( "hostname" => $hostname,"uname" => $uname,"passwd" => $passwd);
        return $dbconfig;
}
/**
 * Prints or inserts the data without inserting in the table
 * @param String $fileName
 * @param array $dbconfig
 * @param Boolean $isDryrun
 */
 function insertOrDisplayCSV($fileName,$dbconfig,$isDryRun) {
    $affectedRows = 0;
    $insertedOutput = "";
    $error = "";
    if (file_exists($fileName) && is_readable($fileName)) {
        $userCsv = array_map("str_getcsv", file($fileName)); 
        $header = array_shift($userCsv);
        $headerTrimmed = array_map("trim", $header);
        foreach ($userCsv as $i=>$row) {
            $userCsv[$i] = array_combine($headerTrimmed, $row);
        }
        if ($userCsv) {
            if($isDryRun){
                $UserInfoVal = array();
                for ($i=0; $i < count($userCsv) ; $i++) { 
                    $name = $userCsv[$i]["name"];
                    $surname = $userCsv[$i]["surname"];
                    $email = $userCsv[$i]["email"];
                    $UserInfoVal[$i] = new UserInfo($name, $surname, $email); 
                    $UserInfoVal[$i]->displayUserForDryRun();
                }
           } else if (is_array($dbconfig)) {
                $Database = new Database($dbconfig);
                $mysqlconnection = $Database->getmySqlConnection();
                if($mysqlconnection->connect_errno == 0 && $Database->checkUserTable() === false){
                        createTble($dbconfig);
                    }
                if ($mysqlconnection->connect_errno == 0) {
                    $UserInfoVal = array();
                    for ($i=0; $i < count($userCsv) ; $i++) { 
                        $name = $userCsv[$i]["name"];
                        $surname = $userCsv[$i]["surname"];
                        $email = $userCsv[$i]["email"];
    
                        $UserInfoVal[$i] = new UserInfo($name, $surname, $email); 
                        $result = $UserInfoVal[$i]->checkUsercsvToInsert($Database);
    
                        if (gettype($result) === "string") {
                            echo $result;
                        } else {
                            if (!empty($result->error)) {
                                $error .=  $result->error."\n";
                            }
    
                            if($result->affected_rows == 1) {
                                $affectedRows++;
                            }
                        }
                    }
                    if (intval($affectedRows) > 0) {
                        $insertedOutput .= $affectedRows . " row";
                        $insertedOutput .= (intval($affectedRows) > 1 ? "s " : " ");
                        $insertedOutput .= "inserted. \n";
                    }
    
                    if (!empty($error)) {
                        $insertedOutput .= $error;
                    }
    
                }
            } else {
                    $insertedOutput = " $fileName file cannot not be loaded. Please try again.";       
            }  
            echo $insertedOutput;
        } else {
            $displayOutput = "Error loading filename";
        }
    } else {
        throw new Exception($fileName . ' it is not readable or it is not accepted');
    }  

}
/**
 * Inputs from command line arguments
 * @param array $arguments - Command line arguments passed
 */
 function parseCommandLineArguments($arguments) {
    $option = isset($arguments[1]) ? $arguments[1] : "";
    $short = "u:p:h:";
    $longopts  = array("file:", "help", "dry_run", "create_table::");
    $givenOptions = getopt($short,$longopts);
    $longOptions = array_keys(getopt("",$longopts));
    switch ($longOptions[0]) {
        case 'create_table':
            if(count($arguments) === 8) {
                $dbconfig = checkAllConditions($arguments,$givenOptions);
                if(is_array($dbconfig)){
                    createTble($dbconfig);
                } else {
                    echo $dbconfig;
                }
            }else {
                echo "Invalid command. Use --help";
            }
            
        break;
        case 'file':
            $isDryRun = false;
            $dbconfig = "";
            $fileName = isset($arguments[2]) ? $arguments[2] : "";
            if(count($arguments) === 4 && array_key_exists("dry_run", $givenOptions)){
                $isDryRun = true;
            } else {
                if(count($arguments) === 9) {
                    $dbconfig = checkAllConditions($arguments,$givenOptions);
                } else {
                    echo "Invalid command. Use --help";
                }
            }
            insertOrDisplayCSV($fileName,$dbconfig, $isDryRun);
        break;
        case 'help':
            echo helpCommands();
        break;
        case 'dry_run':
            $fileName = isset($arguments[3]) ? $arguments[3] : "";
            if(count($arguments) === 4 && array_key_exists("file",$givenOptions)) {
                insertOrDisplayCSV($fileName,null, true);
            } else {
                echo "Invalid command. Use --help";
            }
           
        break;
        default:
        break;
    };

}
parseCommandLineArguments($argv);

?>