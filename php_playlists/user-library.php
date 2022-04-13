<?php
require('connect-db.php');

session_start();
//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$list_of_playlists = getAllPlaylists();
$playlist_to_delete = null;

if($_SERVER['REQUEST_METHOD'] == 'POST') //check if post was submitted
{ 
    if(!empty($_POST['btnAction']))
    {
        if($_POST['btnAction'] == "Delete")
        {
            deletePlaylist($_POST['playlist_to_delete']);
            $list_of_playlists = getAllPlaylists();
        }
        else if($_POST['btnAction'] == "View")
        {
            header("location: playlist_display.php");
            $_SESSION["playlist_id"] = $_POST['playlist_to_view'];
            
        }
    }
}

function getAllPlaylists()
{
    global $db;
    $query = "select * from playlist natural join created_by where user_id=:id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $_SESSION["id"]);
    $statement->execute();

    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function deletePlaylist($playlist_id)
{
    global $db;

    $query1 = "delete from created_by where playlist_id=:playlist_id";
    $query2 = "delete from contains where playlist_id=:playlist_id";
    $query3 = "delete from comment where playlist_id=:playlist_id";
    $query4 = "delete from likes where playlist_id=:playlist_id";
    $query5 = "delete from playlist created_by where playlist_id=:playlist_id";

    $statement1 = $db->prepare($query1);
    $statement2 = $db->prepare($query2);
    $statement3 = $db->prepare($query3);
    $statement4 = $db->prepare($query4);
    $statement5 = $db->prepare($query5);

    $statement1->bindValue(':playlist_id', $playlist_id);
    $statement2->bindValue(':playlist_id', $playlist_id);
    $statement3->bindValue(':playlist_id', $playlist_id);
    $statement4->bindValue(':playlist_id', $playlist_id);
    $statement5->bindValue(':playlist_id', $playlist_id);

    $statement1->execute();
    $statement2->execute();
    $statement3->execute();
    $statement4->execute();
    $statement5->execute();

    $statement1->closeCursor();
    $statement2->closeCursor();
    $statement3->closeCursor();
    $statement4->closeCursor();
    $statement5->closeCursor();
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
    <title>Homepage</title>
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
    <h1>My Library</h1>        
    
    <p><a href="add-playlist.php">Create a new playlist!</a></p>

<hr/>

<table class="table table-hover" style="width:90%">
    <thead>
    <tr style="background-color:#B0B0B0">
        <th width="18%">Playlist Name</th>
        <th width="5%"></th>
        <th width="8%">Date Created</th>
        <th width="5%">Likes</th>
        <th width="6%">Privacy</th>
        <th width="5%"></th>
    </tr>
    </thead>
    <?php foreach ($list_of_playlists as $playlist): ?>
    <tr>
        <td> <?php echo $playlist['name']; ?> </td>
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
            if($playlist['is_public'] == 0)
            {
                echo "Private";
            }
            else{
                echo "Public";
            }  
            ?>
        </td>
        <td>
            <form action="user-library.php" method="post">
                <input type="submit" value="Delete" name="btnAction"
                    class="btn btn-danger" />
                <input type="hidden" name="playlist_to_delete"
                    value="<?php echo $playlist['playlist_id'] ?>" />
            </form>
        </td>
    </tr>
    <?php endforeach; ?>

</table>
</div>

  <!-- CDN for JS bootstrap -->
  <!-- you may also use JS bootstrap to make the page dynamic -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> -->
  
  <!-- for local -->
  <!-- <script src="your-js-file.js"></script> -->  

  
</div>    
</body>
</html>