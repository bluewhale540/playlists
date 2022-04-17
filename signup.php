<?php
require('connect_db.php');
$email = "";
$password = "";
$login_error_message = "";

global $db;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"]))) {
        $login_error_message = "This field cannot be blank";
    } else {
        global $db;
        $query = "select user_id from user where email = :email";
        $statement = $db->prepare($query);
        $statement->bindValue(':email', trim($_POST["email"]));
        $statement->execute();
        $results = $statement->fetchAll();

        if (count($results) == 0) {
            $email = trim($_POST["email"]);
        } else {
            $login_error_message = "email already exists";
            echo "<p>That email already exists </p>";
        }
        $statement->closeCursor();
    }

    //TODO validate password lengh etc.
    if(empty($login_error_message)) {
        $query = "insert into user (email, num_followers, password) values(:email, :num_followers, :password)";
        $statement= $db->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':num_followers', 0);
        $statement->bindValue(':password', password_hash(trim($_POST["password"]), PASSWORD_DEFAULT));
        
        if($statement-> execute()==true) {
            header("location: login.php");
        }
        else {
            echo "ERROR";
        }
        $statement->closeCursor();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../style/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Homepage</title>
    <link rel="icon" type="image/x-icon" href="../style/spot.jpg">
</head>
<body>
    <div class="container w-25 mt-5">
        <h2>Sign Up</h2>
        <form action="signup.php" method="post">
            <div class="form-group mb-2">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($login_error_message)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $login_error_message; ?></span>
            </div>    
            <div class="form-group mb-2">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group mb-1">
                <input type="submit" href="signup.php" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p> <a href="login.php">Login with Existing Account</a></p>
        </form>
    </div>    
</body>
</html>