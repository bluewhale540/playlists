<?php
require('connect-db.php');

session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: homepage.php");
    exit;
}

$email = "";
$password = "";
$login_error_message = "";
 

if($_SERVER["REQUEST_METHOD"] == "POST"){
 

    if(empty($username_err) && empty($password_err)){
        $query = "select user_id, password from user where email = :email";
        $statement= $db->prepare($query);
        $statement->bindValue(':email', trim($_POST["email"]));
        $statement-> execute();
        $results = $statement->fetch();
            

                if(!empty($results)){                    
                    
                        if(password_verify(trim($_POST["password"]), $results['password'])){

                            session_start();

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $results['user_id'];
                            $_SESSION["username"] = trim($_POST["email"]);                            
                            
                            header("location: homepage.php");
                        } else{
                            
                            $login_error_message = "Incorrect email/password";
                        }
                } else{
                    $login_error_message = "Incorrect email/password";
                }
            } 


    $statement->closeCursor();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Enter your credentials to login.</p>

        <?php 
        if(!empty($login_error_message)){
            echo '<div class="alert alert-danger">' . $login_error_message . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control " value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="index.php">Sign up</a>.</p>
        </form>
    </div>
</body>
</html>