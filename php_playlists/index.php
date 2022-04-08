<?php
require('connect-db.php');
$email = "";
$password = "";
$login_error_message = "";

global $db;

if($_SERVER["REQUEST_METHOD"] == "POST"){
  

    if(empty(trim($_POST["email"]))){
        $login_error_message = "This field cannot be blank";
    }
   else{
    global $db;
    $query = "select user_id from user where email = :email";
            $statement= $db->prepare($query);
            $statement->bindValue(':email', trim($_POST["email"]));
            $statement-> execute();
            $results = $statement->fetchAll();
          
            
           if (count($results)==0) { 
            $email = trim($_POST["email"]);
           }
           else{
            $login_error_message= "email already exists";
            echo "<p>That email already exist </p>";

           }
           $statement->closeCursor();
           
    }
    
//TODO validate password lengh etc.
    if(empty($login_error_message)){
        
      $query = "insert into user (email, num_followers, password) values(:email, :num_followers, :password)";
        $statement= $db->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':num_followers', 0);
        $statement->bindValue(':password', password_hash(trim($_POST["password"]), PASSWORD_DEFAULT));
        
        if($statement-> execute()==true){
          header("location: login.php");
        }else{
          echo "ERROR";
        }
        $statement->closeCursor();
       
    }
    
}
?>
<! --- Place holder signup copied from from https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php ---> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <form action="index.php" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($login_error_message)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $login_error_message; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
           
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p> <a href="login.php">Login with Existing Account</a></p>
        </form>
    </div>    
</body>
</html>