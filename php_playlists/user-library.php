<?php
require('connect-db.php');
require('userlibs/playlist_fxs.php');
require('userlibs/user_fxs.php');

session_start();
//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$modifying = false;
$list_of_playlists = [];
$playlist_to_delete = null;
$displayName = '';

if (isset($_GET['user']) and ($_GET['user'] != $_SESSION['id'])) {
    $uName = getUser($_GET['user'])['email'];
    $displayName = "{$uName}'s";
    $list_of_playlists = getAllPlaylists($_GET['user'], false);
}
else {
    $modifying = true;
    $displayName = 'My';
    $list_of_playlists = getAllPlaylists($_SESSION['id'], true);
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!empty($_POST['btnAction']))
    {
        if($_POST['btnAction'] == "Delete") {
            deletePlaylist($_POST['playlist_to_delete']);
            $list_of_playlists = getAllPlaylists($_SESSION['id']);
        }
        else if($_POST['btnAction'] == "View") {
            header("location: playlist_display.php");
            $_SESSION["playlist_id"] = $_POST['playlist_to_view'];
        }
    }
}

?>

<!-- 1. create HTML5 doctype -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Library</title>
    <link rel="icon" type="image/x-icon" href="style/spot.jpg">
</head>

<body> <!--everything displayed on screen-->

<!--Navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a href="#" class="img-fluid" style="margin-right: 8px"><img src="style/spot.jpg"></a>
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
            <a class="btn btn-info mx-1" href="profile.php">My Profile</a>
            <a class="btn btn-primary" href="signout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <h1><?php echo $displayName?> Library</h1>
    <?php if ($modifying) {
        echo '<p><a href="add-playlist.php">Create a new playlist!</a></p>';
    }
    else {
        echo '<p><a href="user-library.php">Go back to my library</a></p>';
    }
    ?>
<hr/>

<table class="table table-hover">
    <?php if ($modifying) {
        echo '<thead>
            <tr>
                <th width="18%">Playlist Name</th>
                <th width="5%"></th>
                <th width="8%">Date Created</th>
                <th width="5%">Likes</th>
                <th width="6%">Privacy</th>
                <th width="5%"></th>
            </tr>
        </thead>';
    }
    else {
        echo '<thead>
            <tr>
                <th>Playlist Name</th>
                <th></th>
                <th>Date Created</th>
                <th>Likes</th>
            </tr>
        </thead>';
    }
    ?>

    <?php foreach ($list_of_playlists as $playlist): ?>
    <tr>
        <td> <?php echo $playlist['name']; ?>
        </td>
        <td>
            <form action="user-library.php" method="post">
                <input type="submit" value="View" name="btnAction"
                    class="btn btn-info" />
                <input type="hidden" name="playlist_to_view"
                    value="<?php echo $playlist['playlist_id'] ?>" />

            </form>
        </td>
        <td> <?php echo $playlist['date_created']; ?> </td>
        <td> <?php echo $playlist['num_likes']; ?> </td>
        <td>
            <?php
            if ($modifying) {
                if ($playlist['is_public'] == 0) {
                    echo "Private";
                } else {
                    echo "Public";
                }
            }
            ?>
        </td>
        <?php if ($modifying) {
            echo '<td>
            <form action="user-library.php" method="post">
                <input type="submit" value="Delete" name="btnAction"
                    class="btn btn-danger" />
                <input type="hidden" name="playlist_to_delete"
                    value="<?php echo $playlist[`playlist_id`]?>" />
            </form>
        </td>';
        }
        ?>
    </tr>
    <?php endforeach; ?>
</table>
</div>
</body>
</html>