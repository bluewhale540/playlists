<?php

require('connect-db.php');
session_start();

//check session
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
$search_results = null;

if ($_SERVER["REQUEST_METHOD"] == "GET" and !empty($_GET["search"])) {
    $search_results = searchPlaylists($_GET["search"]);
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['btnAction'])) {
        if ($_POST['btnAction'] == "View") {

            header("location: playlist_display.php");
            $_SESSION["playlist_id"] = $_POST["playlist_to_view"];
        }
    }
}
function searchPlaylists($query_term)
{
    global $db;
    $query = "select * from playlist where name LIKE :query_term";
    $q = $db->prepare($query);
    $q->bindValue(':query_term', $query_term);
    $q->execute();
    $r = $q->fetchAll();

    $q->closeCursor();
    return $r;
}


<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Search</title>
</head>

<body>
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
        <h1>Search Playlists</h1>
        <form action="search.php" method="get">
            <input type="text" name="search">
            <input type="submit" value="Search" name="btnAction">
        </form>

    </div>
    <hr />
    <?php if ($search_results == null) : ?>
        <div class="container mt-3">
            <h2>Search for playlists using the form.</h2>
        </div>

    <?php elseif (count($search_results) == 0) : ?>
        <div class="container mt-3">
            <h2>No results found...</h2>
        </div>
    <?php else : ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="18%">Playlist Name</th>
                    <th width="5%"></th>
                    <th width="8%">Date Created</th>
                    <th width="5%">Likes</th>
                    <th width="6%">Privacy</th>
                    <th width="5%"></th>
                </tr>
            </thead>
            <?php foreach ($search_results as $playlist) : ?>
                <tr>
                    <td> <?php echo $playlist['name']; ?> </td>
                    <td>
                        <form action="search.php" method="post">
                            <input type="submit" value="View" name="btnAction" class="btn btn-info" />
                            <input type="hidden" name="playlist_to_view" value="<?php echo $playlist['playlist_id'] ?>" />
                        </form>
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
    <?php endif; ?>
</body>

</html>
