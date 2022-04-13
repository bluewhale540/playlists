<?php
require('connect-db.php');

session_start();
//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$playlist_to_add = null;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['btnAction'] == "Create") {
        $timezone = date_default_timezone_set('America/New_York');
        $date = date('Y-m-d H:i:s');

        if($_POST['public_radio'] == "public"){
            addPlaylist($_POST['name'], $date, 1);
        }
        else {
            addPlaylist($_POST['name'], $date, 0);
        }
    }
}

function addPlaylist($name, $date_created, $is_public) {
    global $db;
    
    $query1 = "insert into playlist (name, date_created, num_likes, is_public)
    values(:name, :date_created, 0, :is_public)";
    $query2 = "insert into created_by values(:user_id, (select playlist_id from playlist where name=:name) )";
    $statement1 = $db->prepare($query1);
    $statement2 = $db->prepare($query2);

    $statement1->bindValue(':name', $name);
    $statement1->bindValue(':date_created', $date_created);
    $statement1->bindValue(':is_public', $is_public);
    
    $statement2->bindValue(':user_id', $_SESSION["id"]);
    $statement2->bindValue(':name', $name);
   
    $statement1->execute();
    $statement2->execute();
    $statement1->closeCursor();
    $statement2->closeCursor();
}

?>

<!-- 1. create HTML5 doctype -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Add a Playlist</title>
</head>

<body> <!--everything displayed on screen-->

<!--Navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="homepage.php">Title</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="homepage.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user-library.php">Playlists</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search.php">Search</a>
                </li>
            </ul>

            <a class="btn btn-primary" href="signout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <h1>Create Playlist</h1>
    <form name="mainForm" action="add-playlist.php" method="post">
        <div class="form-group row mb-3 mx-3">
            Playlist Name:
            <input type="text" class="form-control" name="name" required
                    value="<?php if ($playlist_to_add!=null) echo $playlist_to_add['name'] ?>"
            >
        </div>
        <fieldset class="form-group mb-3 mx-3">
            <legend>Make Public?</legend>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="public_radio" id="public" value="public" checked="">
                    Yes - other users will be able to see and like my playlist
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="public_radio" id="private" value="private">
                    No - only I will be able to see this playlist
                </label>
            </div>
        </fieldset>
        <input type="submit" value="Create" name="btnAction" class="btn btn-primary"/>
    </form>
    <br>
    <p><a href="user-library.php">Go to your playlist library!</a></p>
</div>
</body>
</html>