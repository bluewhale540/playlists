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

<!--Navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a href="#" class="img-fluid" style="margin-right: 8px"><img src="../style/spot.jpg"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="homepage.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="library.php">Playlists</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search.php">Search</a>
                </li>
            </ul>
            <a class="btn btn-info mx-1" href="profile.php">My Profile</a>
            <a class="btn btn-primary" href="signout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-3 mb-4">
    <h1 class="my-5">Hello, <?php echo ($_SESSION["username"]);?></h1>
    <p>
        <a href="library.php" class="btn btn-danger ml-3"> View My Library</a>
    </p>
</div>

<hr/>

<div class="container">
    <h2>Your Feed</h2>
</div>
</body>
</html>