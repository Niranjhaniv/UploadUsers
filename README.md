
Created a PHP script, that is executed from the command line, which accepts a CSV file as input
(see command line directives below) and processes the CSV file. The parsed file data is to be
inserted into a MySQL database. A CSV file is provided as part of this task that contains test
data, the script must be able to process this file appropriately.<br>
The PHP script will correctly handle the following criteria:<br>
  *  CSV file will contain user data and have three columns: name, surname, email
    (see table definition below)<br>
  *  CSV file will have an arbitrary list of users<br>
  *  Script will iterate through the CSV rows and insert each record into a dedicated
    MySQL database into the table “users”<br>
  *  The users database table will need to be created/rebuilt as part of the PHP script.
    This will be defined as a Command Line directive below<br>
  *  Name and surname field should be set to be capitalised e.g. from “john” to “John”
    before being inserted into DB<br>
  * Emails need to be set to be lower case before being inserted into DB<br>
  *  The script should validate the email address before inserting, to make sure that it
    is valid (valid means that it is a legal email format, e.g. “xxxx@asdf@asdf” is not
    a legal format). In case that an email is invalid, no insert should be made to
    database and an error message should be reported to STDOUT.<br>
## Requirements 
 * PHP version is: 7.2.x <br>
 * MySQL database server is already installed and is version 5.7 (higher versions
are fine, as is MariaDB 10.x) – DB user details should be configurable <br>

## How to run 
* php user_upload.php -- help <br>
* php user_upload.php --file [filename] -u [username] -p [password] -h [hostname]<br>
* php user_upload.php --create_table [filename] -u [username] -p [password] -h [hostname]<br>
* php user_upload.php --file [filename] --dry_run<br>
* php user_upload.php  --dry_run --file [filename]<br>
    
 # Logic test
 
 Created a PHP script that is executed form the command line. The script should:<br>
  *  Output the numbers from 1 to 100<br>
  *  Where the number is divisible by three (3) output the word “foo”<br>
  *  Where the number is divisible by five (5) output the word “bar”<br>
  * Where the number is divisible by three (3) and (5) output the word “foobar”<br>
  *  Only be a single PHP file<br>
 
