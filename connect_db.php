<?php
// Remember to start the database server (or GCP SQL instance) before trying to connect to it
// to get instance connection name, go to GCP SQL overview page
////////////////////////////////////////////
if (getenv('IS_APPENGINE') == 'true') {
    $username = getenv('mysql_user');
    $password = getenv('mysql_password');
    $dbName = 'project';
    $connectionName = getenv('mysql_conn');
    $socketDir = '/cloudsql';

    $dsn = sprintf(
        'mysql:dbname=%s;unix_socket=%s/%s',
        $dbName,
        $socketDir,
        $connectionName
    );
}
elseif (getenv('DEV') != 'true') {
    $username = getenv('mysql_user');
    $password = getenv('mysql_password');
    $dbName = 'project';

    $dsn = "mysql:host=34.150.221.90;dbname=$dbName";

}
else {
    $username = 'user';
    $password = 'password';
    $connectionName = 'localhost:3306';
    $dbName = 'project';
    $dsn = "mysql:host=$connectionName;dbname=$dbName";
}

// To connect from a local PHP to GCP SQL instance, need to add authorized network
// to allow your machine to connect to the SQL instance. 
// 1. Get IP of the computer that will connect to the SQL instance
//    (use http://ipv4.whatismyv6.com/ to find the IP address)
// 2. On the cloud SQL connections page, add authorized networks, enter the IP address
////////////////////////////////////////////

/** S22, PHP (on GCP, local XAMPP, or CS server) connect to MySQL (on local XAMPP) **/
// $username = 'your-username';
// $password = 'your-password';
// $host = 'localhost:3306';
// $dbname = 'your-database';
// $dsn = "mysql:host=$host;dbname=$dbname";  
////////////////////////////////////////////


/** S22, PHP (on GCP, local XAMPP, or CS server) connect to MySQL (on CS server) **/
// $username = 'ksl3fs'; 
// $password = 'testing898';
// $host = 'mysql01.cs.virginia.edu';
// $dbname = 'ksl3fs';
// $dsn = "mysql:host=$host;dbname=$dbname";     

////////////////////////////////////////////

// DSN (Data Source Name) specifies the host computer for the MySQL datbase 
// and the name of the database. If the MySQL datbase is running on the same server
// as PHP, use the localhost keyword to specify the host computer

// To connect to a MySQL database, need three arguments: 
// - specify a DSN, username, and password

// Create an instance of PDO (PHP Data Objects) which connects to a MySQL database
// (PDO defines an interface for accessing databases)
// Syntax: 
//    new PDO(dsn, username, password);


/** connect to the database **/
try {
   $db = new PDO($dsn, $username, $password);

   // dispaly a message to let us know that we are connected to the database 
   //echo "<p>You are connected to the database --- dsn=$dsn, user=$username, pwd=$password </p>";
} catch (PDOException $e)     // handle a PDO exception (errors thrown by the PDO library)
{
   // Call a method from any object, use the object's name followed by -> and then method's name
   // All exception objects provide a getMessage() method that returns the error message 
   $error_message = $e->getMessage();
   echo "<p>An error occurred while connecting to the database: $error_message </p>";
} catch (Exception $e)       // handle any type of exception
{
   $error_message = $e->getMessage();
   echo "<p>Error message: $error_message </p>";
}
