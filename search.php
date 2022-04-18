<?php

require('connect_db.php');
require('userlibs/search_fxs.php');
require('userlibs/follower_fxs.php');
session_start();

//check session
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
$playlist_results = null;
$user_results = null;
$song_results = null;

if ($_SERVER["REQUEST_METHOD"] == "GET" and !empty($_GET["search"])) {
    $playlist_results = searchPlaylists($_GET["search"]);
    $user_results = searchUsers($_GET["search"]);
}

?>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../style/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Search</title>
    <link rel="icon" type="image/x-icon" href="/style/spot.jpg">
</head>

<body>
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
        <h1>Search</h1>
        <form class="input-group" action="search.php" method="get" style="width:50%">
            <input type="text" class="form-control" name="search" required  aria-describedby="searchButton">
            <button type="submit" value="Search" name="btnAction" class="btn btn-primary">Search</button>
        </form>

    </div>
    <hr />
    <?php if ($playlist_results === null and $user_results === null) { ?>
        <div class="container mt-3">
            <h2>Search using the form.</h2>
        </div>
    <?php }
    else {
        if (count($playlist_results) == 0) { ?>
        <div class="container mt-3">
            <h2>No playlists found...</h2>
        </div>
        <?php }
        else { ?>
        <div class="container mt-3">
            <h2>Playlists</h2>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="18%">Playlist Name</th>
                    <th width="5%"></th>
                    <th width="8%">Date Created</th>
                    <th width="5%">Likes</th>
                    <th width="6%">Privacy</th>
                </tr>
            </thead>
            <?php foreach ($playlist_results as $playlist) : ?>
                <tr>
                    <td> <?php echo $playlist['name']; ?> </td>
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
                </tr>
            <?php endforeach; ?>
        </table>
        </div>
    <?php }
    if (count($user_results) == 0) : ?>
        <div class="container mt-3">
            <h2>No users found...</h2>
        </div>
    <?php else : ?>
        <div class="container mt-3">
            <h2>Users</h2>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th width="18%">User Name</th>
                    <th width="5%"></th>
                    <th width="19%">Followers</th>
                </tr>
                </thead>
                <?php foreach ($user_results as $user) : ?>
                    <tr>
                        <td> <?php echo $user['email']; ?> </td>
                        <td>
                            <a href="<?php echo "profile.php?user={$user['user_id']}"?>"
                               class="btn btn-info">View</a>
                        </td>
                        <td> <?php echo getFollowerCount($user['user_id']); ?> </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif;
    }
    ?>
</body>

</html>
