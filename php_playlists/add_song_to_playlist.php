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
        addSong($_POST['title'], $_POST['artist'], $_POST['album_name'],$_POST['date_released']);
        header("location: playlist_display.php");
    }
}
function addSong($title, $artist, $album_name, $date_released){
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

    $statement->bindValue(':title',$title);
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

    //TODO Genre

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
  
  <!-- 2. include meta tag to ensure proper rendering and touch zooming -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
    
  <title>Add song to playlist</title>
  
  <!-- 3. link bootstrap -->
  <!-- if you choose to use CDN for CSS bootstrap -->  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  
  <!-- you may also use W3's formats -->
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  
  <!-- 
  Use a link tag to link an external resource.
  A rel (relationship) specifies relationship between the current document and the linked resource. 
  -->
  
  <!-- If you choose to use a favicon, specify the destination of the resource in href -->
  <link rel="icon" type="image/png" href="http://www.cs.virginia.edu/~up3f/cs4750/images/db-icon.png" />
  
  <!-- if you choose to download bootstrap and host it locally -->
  <!-- <link rel="stylesheet" href="path-to-your-file/bootstrap.min.css" /> --> 
  
  <!-- include your CSS -->
  <!-- <link rel="stylesheet" href="custom.css" />  -->
       
</head>

<body>
<div class="container">
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
  <input type="submit" value="Add" name="btnAction" class="btn btn-dark" 
        title="insert a friend" />

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