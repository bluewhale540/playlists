<?php
require('connect_db.php');
require('userlibs/playlist_fxs.php');
require('userlibs/user_fxs.php');

session_start();
//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$modifying = false;
$userPlaylists = [];
$playlistToDelete = null;
$likedPlaylists = [];
$playlistToUnlike = null;
$displayName = '';

if (isset($_GET['user']) and ($_GET['user'] != $_SESSION['id'])) {
    $uName = getUser($_GET['user'])['email'];
    $displayName = "{$uName}'s";
    $userPlaylists = getAllPlaylists($_GET['user'], false);
    $likedPlaylists = getLikedPlaylists($_GET['user'], false);
}
else {
    $modifying = true;
    $displayName = 'My';
    $userPlaylists = getAllPlaylists($_SESSION['id'], true);
    $likedPlaylists = getLikedPlaylists($_SESSION['id'], true);
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!empty($_POST['btnAction'])) {
        if($_POST['btnAction'] == "Delete") {
            deletePlaylist($_POST['playlist_to_delete']);
            $userPlaylists = getAllPlaylists($_SESSION['id'], true);
            $likedPlaylists = getLikedPlaylists($_SESSION['id'], true);
        }
        else if ($_POST['btnAction'] == 'Unlike') {
            unlike_playlist($_POST['playlist_to_unlike'], $_SESSION['id']);
            $userPlaylists = getAllPlaylists($_SESSION['id'], true);
            $likedPlaylists = getLikedPlaylists($_SESSION['id'], true);
        }
    }
}

?>

<!-- 1. create HTML5 doctype -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../style/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Library</title>
    <link rel="icon" type="image/x-icon" href="/style/spot.jpg">
</head>

<body> <!--everything displayed on screen-->

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

<div class="container mt-3">
    <h1><?php echo $displayName?> Library</h1>
    <?php if ($modifying) {
        echo '<p><a href="add_playlist.php">Create a new playlist!</a></p>';
    }
    else {
        echo '<p><a href="library.php">Go back to my library</a></p>';
    }
    ?>
</div>

<hr/>

<div class="container mb-4">
    <h2>My Playlists</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <th width="18%">Playlist Name</th>
            <th width="5%"></th>
            <th width="8%">Date Created</th>
            <th width="5%">Likes</th>
            <th width="6%">Privacy</th>
            <?php if ($modifying) {
                echo '<th width="5%"></th>';
            }
            ?>
        </tr>
        </thead>
    <?php foreach ($userPlaylists as $playlist): ?>
    <tr>
        <td> <?php echo $playlist['name']; ?>
        </td>
        <td>
            <a href="<?php echo "playlist_display.php?playlist={$playlist['playlist_id']}"?>"
               class="btn btn-info">View</a>
        </td>
        <td> <?php echo $playlist['date_created']; ?> </td>
        <td> <?php echo $playlist['num_likes']; ?> </td>
        <td>
            <?php
                if ($playlist['is_public'] == 0) {
                    echo "Private";
                } else {
                    echo "Public";
                }
            ?>
        </td>
        <?php if ($modifying) {
            ?>
            <td>
            <form action="library.php" method="post">
                <input type="submit" value="Delete" name="btnAction"
                    class="btn btn-danger" />
                <input type="hidden" name="playlist_to_delete"
                    value="<?php echo $playlist['playlist_id']?>" />
            </form>
        </td>
        <?php
        }
        ?>
    </tr>
    <?php endforeach; ?>
</table>
</div>

<hr/>

<div class="container">
    <h2>Liked Playlists</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <th width="18%">Playlist Name</th>
            <th width="5%"></th>
            <th width="8%">Date Created</th>
            <th width="5%">Likes</th>
            <th width="6%">Privacy</th>
            <?php if ($modifying) {
                echo '<th width="5%"></th>';
            }
            ?>
        </tr>
        </thead>

        <?php foreach ($likedPlaylists as $playlist): ?>
            <tr>
                <td> <?php echo $playlist['name']; ?>
                </td>
                <td>
                    <a href="<?php echo "playlist_display.php?playlist={$playlist['playlist_id']}"?>"
                       class="btn btn-info">View</a>
                </td>
                <td> <?php echo $playlist['date_created']; ?> </td>
                <td> <?php echo $playlist['num_likes']; ?> </td>
                <td>
                    <?php
                        if ($playlist['is_public'] == 0) {
                            echo "Private";
                        } else {
                            echo "Public";
                        }
                    ?>
                </td>
                <?php if ($modifying) {
                    ?>
                    <td>
                        <form action="library.php" method="post">
                            <input type="submit" value="Unlike" name="btnAction"
                                   class="btn btn-secondary" />
                            <input type="hidden" name="playlist_to_unlike"
                                   value="<?php echo $playlist['playlist_id']?>" />
                        </form>
                    </td>
                    <?php
                }
                ?>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>