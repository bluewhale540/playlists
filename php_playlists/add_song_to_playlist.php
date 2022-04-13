<?php
require('connect-db.php');
session_start();
 
//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Add")
    {
        addSong($_POST['title'], $_POST['artist'], $_POST['album_name'],$_POST['date_released'], $_POST['genre']);
        header("location: playlist_display.php");
    }
}
function addSong($title, $artist, $album_name, $date_released, $genre){
    global $db;
    //add song to database

    $query = "insert into song (title,artist) values(:title, :artist)";
    $statement= $db->prepare($query);

    $statement->bindValue(':title',$title);
    $statement->bindValue(':artist',$artist);
    $statement-> execute();
	$statement->closeCursor();

    //check if album exists, if it does not then create it 
    $query = "select * from album where title= :title and artist=:artist and date_released= :date_released";
    $statement= $db->prepare($query);

    $statement->bindValue(':title',$album_name);
    $statement->bindValue(':artist',$artist);
    $statement->bindValue(':date_released',$date_released);
    $statement-> execute();
    $album = $statement->fetchAll();   
	$statement->closeCursor();

    if(empty($album)){ //create album
    $query = "insert into album (title,artist, date_released) values(:title, :artist, :date_released)";
    $statement= $db->prepare($query);

    $statement->bindValue(':title',$album_name);
    $statement->bindValue(':artist',$artist);
    $statement->bindValue(':date_released',$date_released);
    $statement-> execute();
	$statement->closeCursor();
    }

    //call add_song_to_albumn prodecure
    $query = 'CALL add_song_to_album(:song_title, :song_artist,:album_title, :album_artist)';
    $statement= $db->prepare($query);

    $statement->bindValue(':song_title',$title);
    $statement->bindValue(':song_artist',$artist);
    $statement->bindValue(':album_title',$album_name);
    $statement->bindValue(':album_artist',$artist);
    $statement-> execute();
	$statement->closeCursor();

    // Genre
    $query = "insert into categories values(:title, :artist, :genre)";
    $statement= $db->prepare($query);

    $statement->bindValue(':title',$title);
    $statement->bindValue(':artist',$artist);
    $statement->bindValue(':genre',$genre);
    $statement-> execute();
	$statement->closeCursor();


    //add song to playlist

    $query = "insert into contains (playlist_id,song_id) values(:playlist_id, (select song_id from song where title= :title and artist= :artist) )";
    $statement= $db->prepare($query);

    $statement->bindValue(':title',$title);
    $statement->bindValue(':artist',$artist);
    $statement->bindValue(':playlist_id',$_SESSION["playlist_id"]);
    $statement-> execute();
	$statement->closeCursor();


}
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add song to playlist</title>
    <link rel="stylesheet" type="text/css" href="style/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Homepage</title>
</head>

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

<body>
<div class="container mt-3">
  <h1 >Add Song to Playlist</h1>  

  <form name="mainForm" action="add_song_to_playlist.php" method="post">   
  <div class="row mb-3 mx-3">
    Title:
    <input type="text" class="form-control" name="title" required/>        
  </div>  
  <div class="row mb-3 mx-3">
    Artist:
    <input type="text" class="form-control" name="artist" required/>        
  </div>  
  <div class="row mb-3 mx-3">
    Album Name:
    <input type="text" class="form-control" name="album_name" required />        
  </div>  
  <div class="row mb-3 mx-3">
    Date Released:
    <input type="text" class="form-control" name="date_released" required />        
  </div>  
  <div class="row mb-3 mx-3">
    Genre:
    <input type="text" class="form-control" name="genre" required />        
  </div>  
  <input type="submit" value="Add" name="btnAction" class="btn btn-dark" 
        title="insert a song" />
    

</form>    

<hr/>


<!-- </div>   -->


  <!-- CDN for JS bootstrap -->
  <!-- you may also use JS bootstrap to make the page dynamic -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> -->
  
  <!-- for local -->
  <!-- <script src="your-js-file.js"></script> -->  
  
</div>    
</body>
</html>