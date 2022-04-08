<?php
session_start();
 
//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage placeholder</title>
</head>
<body>
    <h1 class="my-5">Hello, <?php echo ($_SESSION["username"]);?></h1>
    <p>
        <a href="signout.php" class="btn btn-danger ml-3">Sign Out</a>
    </p>
</body>
</html>