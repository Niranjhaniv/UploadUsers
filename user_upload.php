<?php

/**
 * Manual for runnning this phpfile
 * @return String help message
 */

 require_once("Database.php");
 require_once("UsersInfo.php");

function helpCommands() {
    $help  = "Manual of uploading csv files and option available \n";
    $help .= "--file [csv file name] – The name of the CSV to be parsed\n";
    $help .= "--create_table – this will cause the MySQL users table to be built\n";
    $help .= "--dry_run – To be used with the --file directive to run the script.\nNote that this option will not alter the database\n";
    $help .= "-u – MySQL username\n";
    $help .= "-p – MySQL password\n";
    $help .= "-h – MySQL host\n";
    $help .= "--help – help you with the manual available \n";

    return $help;
}
 function createTble($dbconfig)
    {
        $Database = new Database($dbconfig);
        $mysqlconnection = $Database->getmySqlConnection();
        if ($mysqlconnection->connect_errno == 0) {
            echo $Database->createUserTable();
        }

    }
function checkAllConditionsCreateTable($arguments){
    $check = array();
    //echo count($arguments);
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
            $dbconfig  = array( "hostname" => $hostname,"uname" => $uname,"passwd" => $passwd);
        } else {
            return "Invalid command";
        }
        return $dbconfig;
}
function checkAllConditionsInsertTable($arguments) {
    $check = array();
    //echo count($arguments);
    if(count($arguments) === 9) {

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
            $dbconfig  = array( "hostname" => $hostname,"uname" => $uname,"passwd" => $passwd);
        } else {
            return "Invalid command";
        }
        return $dbconfig;
}

function parseCsvAndInsert($file,$dbconfig) {
   // $headers = array();
   $affectedRows = 0;
   $insertedOutput = "";
   $error = "";
    if (file_exists($file) && is_readable($file)) {
        $userCsv = array_map("str_getcsv", file($file)); 
        $header = array_shift($userCsv);
        $headerTrimmed = array_map("trim", $header);
        foreach ($userCsv as $i=>$row) {
            $userCsv[$i] = array_combine($headerTrimmed, $row);
        }
        
        $Database = new Database($dbconfig);
        $mysqlconnection = $Database->getmySqlConnection();
       if($mysqlconnection->connect_errno == 0 && $Database->checkUserTable() === false){
             createTble($dbconfig);
        }
        if ($mysqlconnection->connect_errno == 0) {
            if ($userCsv) {
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

              
            } else {
                $insertedOutput = " $file file cannot not be loaded. Please try again.";
            }
            echo $insertedOutput;
        }
    } else {
            throw new Exception($file . ' it is not readable or it is not accepted');
    }         

}

 function dryRunCSV($fileName) {

    if (file_exists($fileName) && is_readable($fileName)) {
        $userCsv = array_map("str_getcsv", file($fileName)); 
        $header = array_shift($userCsv);
        $headerTrimmed = array_map("trim", $header);
        foreach ($userCsv as $i=>$row) {
            $userCsv[$i] = array_combine($headerTrimmed, $row);
        }
        if ($userCsv) {
            $UserInfoVal = array();
            for ($i=0; $i < count($userCsv) ; $i++) { 
                $name = $userCsv[$i]["name"];
                $surname = $userCsv[$i]["surname"];
                $email = $userCsv[$i]["email"];
                $UserInfoVal[$i] = new UserInfo($name, $surname, $email); 
                $UserInfoVal[$i]->displayUserForDryRun();
            }
                
        } else {
            $displayOutput = "Error loading filename";
        }
    } else {
        throw new Exception($fileName . ' it is not readable or it is not accepted');
    }  

}
 function parseCommandLineArguments($arguments) {
    $option = isset($arguments[1]) ? $arguments[1] : "";

    switch ($option) {
        case "--create_table":
            $dbconfig = checkAllConditions($arguments);
            if(is_array($dbconfig)){
                createTble($dbconfig);
            } else {
                echo $dbconfig;
            }
            
        break;
        case "--file":
            $fileName = isset($arguments[2]) ? $arguments[2] : "";
            $dbconfig = checkAllConditionsInsertTable($arguments);
            if(count($arguments) === 4 AND $arguments[1] === "--file" AND $arguments[3] === "--dry_run") {
                dryRunCSV($fileName);
            } else {
                if (is_array($dbconfig)) {
                    parseCsvAndInsert($fileName, $dbconfig);
                } else {
                    echo $dbconfig;
                } 
            }
                      
        break;
        case "--help":
            echo helpCommands();
        break;
        case "--dry_run":
            $filename = isset($arguments[3]) ? $arguments[3] : "";
            echo $filename;
            if(count($arguments) === 4 AND $arguments[1] === "--dry_run" AND $arguments[2] === "--file") {
                dryRunCSV($fileName);
            } else {
                echo "Invalid command";
            }
           
        break;
        case "-u":
            echo "Invalid command, use with --create_table or --file";
        break;
        case "-p":
            echo "Invalid command, use with --create_table or --file";
        break;
        case "-h":
            echo "Invalid command, use with --create_table or --file";
        break;
        default:
        break;
    };

}
parseCommandLineArguments($argv);

?>