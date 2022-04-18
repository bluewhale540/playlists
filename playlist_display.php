<?php
require('connect_db.php');
require('userlibs/playlist_fxs.php');
require('userlibs/song_fxs.php');
require('userlibs/follower_fxs.php');
session_start();

//check session
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$owner = false;
$public = false;

$comments = [];
$likes_playlist = false;
$list_of_songs = [];
$playlist_name = '';
$playlist_owner = null;
$is_following = null;
if (isset($_GET['playlist'])) {
    $owner = check_owner($_GET['playlist'], $_SESSION['id']);
    $public = is_public($_GET['playlist']);
    $playlist_owner = get_owner($_GET['playlist']);
    $is_following = checkFollowing($_SESSION['id'], $playlist_owner['user_id']);
   
    if (!$owner and !$public) {
        echo "You don't have access to this playlist";
        exit;
    }

    $likes_playlist = check_if_likes($_GET['playlist'], $_SESSION['id']);
    $list_of_songs = get_all_songs($_GET["playlist"]); //get
    $playlist_name = get_playlist_name($_GET["playlist"]);
    $comments = getComments($_GET['playlist']);
}
else {
    echo 'no playlist given';
    exit;
}

$numComments = sizeof($comments);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['btnAction'] == "Like 👍") {
        like_playlist($_GET['playlist'], $_SESSION['id']);
        $likes_playlist = check_if_likes($_GET['playlist'], $_SESSION['id']);
    }
    else if ($_POST['btnAction'] == "Unlike 👎") {
        unlike_playlist($_GET['playlist'], $_SESSION['id']);
        $likes_playlist = check_if_likes($_GET['playlist'], $_SESSION['id']);
    }
    else if ($_POST['btnAction'] == "Delete") {
        delete_song($_POST['song_to_delete']);
        $list_of_songs = get_all_songs($_GET["playlist"]);
    }
    else if ($_POST['btnAction'] == 'Comment') {
        addComment($_GET['playlist'], $_SESSION['id'], $_POST['commentText']);
        $comments = getComments($_GET['playlist']);
    } else if ($_POST['btnAction'] == 'Follow'){
        followUser($_SESSION['id'], $playlist_owner['user_id']);
        $is_following = checkFollowing($_SESSION['id'], $playlist_owner['user_id']);
    } else if ($_POST['btnAction'] == 'Un-Follow'){
        unFollowUser($_SESSION['id'], $playlist_owner['user_id']);
        $is_following = checkFollowing($_SESSION['id'], $playlist_owner['user_id']);
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../style/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Playlists</title>
    <link rel="icon" type="image/x-icon" href="/style/spot.jpg">
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

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-10">
                <h1><?php echo $playlist_name ?></h1>
            </div>
            <div class="col-sm-1 mx-4">
                <?php if ($owner == 0 || 1) { //GET RID OF the '|| 1' TO ALLOW ANYONE INCLUDING OWNER TO LIKE PLAYLIST
                    if ($likes_playlist) {
                        echo "<form method='post' action='#'> 
                    <input type='submit' value='Unlike 👎' name='btnAction' class='btn btn-secondary' title='unlike the playlist' />
                    </form>";
                    } else {
                        echo "<form method='post' action='#'>
                    <input type='submit' value='Like 👍' name='btnAction' class='btn btn-success btn' title='like the playlist' />
                    </form>";
                    }
                }
                ?>
            </div>
        </div>
        <p>Made by: <?php echo $playlist_owner['email']?><p>
            <?php if ($is_following):?>
                <form method="post" action="#">
                <input type="submit" value="Un-Follow" name="btnAction"  class='btn btn-secondary' title='stop following this user'/>

            </form>
            <?php else :?>
               <form method="post" action="#">
                <input type="submit" value="Follow" name="btnAction"  class='btn btn-secondary' title='follow this user'/>

            </form>
            <?php endif;?>
        <hr />
        <div class="row">
            <div class="col-md-auto">
                <h2>Playlist Songs</h2>
            </div>
            <div class="col mt-1">
                <?php if ($owner) {?>
                    <a href="add_song_to_playlist.php?playlist=<?php echo $_GET['playlist']?>" class='btn btn-info btn-sm'>Add +</a>
                <?php }?>
            </div>
        </div>

        <!-- <div class="row justify-content-center">   -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="25%">Name</th>
                    <th width="25%">Artist</th>
                    <th width="20%">Album</th>
                    <th width="12%">Year</th>
                    <th width="12%">Genre</th>
                    <?php if ($owner) {
                        echo '<th width="12%">Delete</th>';
                    }
                    ?>
                </tr>
            </thead>
            <?php foreach ($list_of_songs as $song) :  ?>
                <tr>
                    <td><?php echo $song['name']; ?></td>
                    <td><?php echo $song['artist']; ?></td>
                    <td><?php echo $song['album']; ?></td>
                    <td><?php echo $song['year']; ?></td>
                    <td><?php echo $song['genre']; ?></td>
                    <?php if ($owner) {?>
                        <td>
                            <form action="" method="post">
                                <input type="submit" value="Delete" name="btnAction" class="btn btn-danger" title="Permanently delete the record" />
                                <input type="hidden" name="song_to_delete" value="<?php echo $song['song_id'] ?>" />
                            </form>
                        </td>
                    <?php }?>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="row">
            <div class="col">
                <h2>Comments (<?php echo $numComments?>)</h2>
            </div>
        </div>
        Add a comment:
        <form class="input-group mb-2" method="post">
            <input type="text" class="form-control" name="commentText"
                   required aria-describedby="button-addon2"
            >
            <button class="btn btn-primary" name="btnAction" value="Comment" type="submit" id="button-addon2">Submit</button>
        </form>
        <?php foreach ($comments as $comment) : ?>
        <div class="card text-white border-primary mb-2" style="max-width: 100rem;">
            <div class="card-header">
                <span class="left"><?php echo $comment['email']?></span>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo $comment['the_comment']?></p>
            </div>
        </div>
        <?php endforeach; ?>
</body>
</html>