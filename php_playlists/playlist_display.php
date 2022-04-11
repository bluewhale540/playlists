<?php
require('connect-db.php');
session_start();

//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$_SESSION["owns_playlist"]= check_owner($_SESSION["playlist_id"]);
//  echo "owns playlist";
//  echo $_SESSION["owns_playlist"];

$likes_playlist= check_if_likes();
$list_of_songs= get_all_songs($_SESSION["playlist_id"]);//get

$playlist_name= get_playlist_name($_SESSION["playlist_id"]);

function check_if_likes(){
    global $db;
    $query = "select * from likes where playlist_id = :playlist_id and user_id= :user_id";
	
	$statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $_SESSION["playlist_id"]);
    $statement->bindValue(':user_id', $_SESSION["id"]);
    $statement-> execute();

	$results = $statement->fetch();   

	$statement->closeCursor();

    if(empty($results)){
        return 0;
    }
    else {
        return 1;
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Like")
    {
        like_playlist();
    }

    else if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Unlike")
    {
        unlike_playlist();
    }
   
    else if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Delete")
    {
        delete_song($_POST['song_to_delete']);
        $list_of_songs = get_all_songs($_SESSION["playlist_id"]);
    }

}
function like_playlist(){
    global $db;
    $query = "insert into likes values(:user_id,:playlist_id) ";
    $statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $_SESSION["playlist_id"]);
    $statement->bindValue(':user_id', $_SESSION["id"]);
    $statement-> execute();
	$statement->closeCursor();

    $query = "update playlist set num_likes = num_likes+1 where playlist_id= :playlist_id ";
    $statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $_SESSION["playlist_id"]);
    $statement-> execute();
	$statement->closeCursor();
    
    header("location: playlist_display.php");
}
function unlike_playlist(){
    global $db;
    $query = "delete from likes where playlist_id = :playlist_id and user_id= :user_id";
    $statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $_SESSION["playlist_id"]);
    $statement->bindValue(':user_id', $_SESSION["id"]);
    $statement-> execute();
	$statement->closeCursor();

    $query = "update playlist set num_likes = num_likes-1 where playlist_id= :playlist_id ";
    $statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $_SESSION["playlist_id"]);
    $statement-> execute();
	$statement->closeCursor();
    
    header("location: playlist_display.php");
}
function delete_song($song_id){
    global $db;
    $query = "delete from in_album where song_id = :song_id";
    $statement= $db->prepare($query);
    $statement->bindValue(':song_id', $song_id);
    $statement-> execute();
	$statement->closeCursor();

    $query = "delete from contains where song_id = :song_id";
    $statement= $db->prepare($query);
    $statement->bindValue(':song_id', $song_id);
    $statement-> execute();
	$statement->closeCursor();

    $query = "delete from song where song_id = :song_id";
    $statement= $db->prepare($query);
    $statement->bindValue(':song_id', $song_id);
    $statement-> execute();
	$statement->closeCursor();
}
function check_owner($playlist_id){
    global $db;
	$query = "select * from created_by where playlist_id = :playlist_id and user_id= :user_id";
	
	$statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $_SESSION["playlist_id"]);
    $statement->bindValue(':user_id', $_SESSION["id"]);
    $statement-> execute();

	$results = $statement->fetch();   

	$statement->closeCursor();

    if(empty($results)){
        return 0;
    }
    else {
        return 1;
    }
    
}

function get_playlist_name($playlist_id){
    global $db;
	$query = "select name from playlist where playlist_id = :playlist_id";
	
	$statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlist_id);
    $statement-> execute();

	$results = $statement->fetch();   

	$statement->closeCursor();

    return $results['name'];
    
}
function get_all_songs($playlist_id)
{
	global $db;
	$query = "select * from contains where playlist_id = :playlist_id";
	
	$statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlist_id);
    $statement-> execute();

	$results = $statement->fetchAll();   

	$statement->closeCursor();
    $song_list=[];

    foreach ($results as $song_id) {
        //get name and artists 
        $query = "select * from song where song_id = :song_id";
        $statement= $db->prepare($query);
        $statement->bindValue(':song_id', $song_id['song_id']);
        $statement-> execute();
    
        $songinfo = $statement->fetch();   
    
        $statement->closeCursor();

        //get album that song is in

        $query = "select album_id from in_album where song_id = :song_id";
        $statement= $db->prepare($query);
        $statement->bindValue(':song_id', $song_id['song_id']);
        $statement-> execute();
    
        $album_id= $statement->fetch();   
    
        $statement->closeCursor();
        
        //get info from album 

        $query = "select * from album where album_id = :album_id";
        $statement= $db->prepare($query);
        $statement->bindValue(':album_id', $album_id['album_id']);
        $statement-> execute();
    
        $albuminfo= $statement->fetch();   
    
        $statement->closeCursor();
        //get genre
        $query = "select * from categories where title = :title and artist= :artist";
        $statement= $db->prepare($query);
        $statement->bindValue(':title', $songinfo['title']);
        $statement->bindValue(':artist', $songinfo['artist']);
        $statement-> execute();
    
        $genreinfo= $statement->fetch();   
    
        $statement->closeCursor();
        //combine info together

        $song;
        $song['song_id'] = $song_id['song_id'];
        $song['name'] = $songinfo['title'];
        $song['artist'] = $songinfo['artist'];
        $song['album'] = $songinfo['title'];
        $song['year'] = $albuminfo['date_released'];
       
        

  
        if (!empty($genreinfo)){
        $song['genre'] = $genreinfo['genre'];
        }
    
    else{
        $song['genre']= "N/A";
    }

        array_push($song_list, $song);

      }

	return $song_list;

}


?>



<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">  
  
  <!-- 2. include meta tag to ensure proper rendering and touch zooming -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Playlist Display</title>
  
  <!-- 3. link bootstrap -->
  <!-- if you choose to use CDN for CSS bootstrap -->  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  
  <!-- you may also use W3's formats -->
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  

  <link rel="icon" type="image/png" href="http://www.cs.virginia.edu/~up3f/cs4750/images/db-icon.png" />
  
       
</head>

<body>
<div class="container">
  <h1 ><?php echo $playlist_name?></h1>  
  <p><a href="user-library.php">Go to my playlist library!</a></p>
  <p><a href="add_song_to_playlist.php">Add song to playlist!</a></p>

<hr/>
<h2 >Playlist Songs</h2>
<?php if($_SESSION["owns_playlist"]==0|| 1){ //GET RID OF the '|| 1' TO ALLOW ANYONE INCLUDING OWNER TO LIKE PLAYLIST
    if($likes_playlist){
        echo "<form method='post' action='playlist_display.php'> <input type='submit' value='Unlike' name='btnAction' class='btn btn-secondary' title='unlike the playlist' /></form>";
    }
    else{
        echo "<form method='post' action='playlist_display.php'>
        <input type='submit' value='Like' name='btnAction' class='btn btn-success' title='like the playlist' />
        </form>";
    }
    
}?>



<!-- <div class="row justify-content-center">   -->
<table class="w3-table w3-bordered w3-card-4" style="width:90%">
  <thead>
  <tr style="background-color:#B0B0B0">
    <th width="25%">Name</th>        
    <th width="25%">Artist</th>        
    <th width="20%">Album</th> 
    <th width="12%">Year</th>
    <th width="12%">Genre</th> 
    <th width="12%">Delete</th> 
  </tr>
  </thead>
  <?php foreach ($list_of_songs as $song):  ?>
  <tr>
    <td><?php echo $song['name']; ?></td>
    <td><?php echo $song['artist']; ?></td>
    <td><?php echo $song['album']; ?></td>
    <td><?php echo $song['year']; ?></td>
    <td><?php echo $song['genre']; ?></td>
    <td>
      <form action="" method="post">
        <input type="submit" value="Delete" name="btnAction" class="btn btn-danger" title="Permanently delete the record" />      
        <input type="hidden" name="song_to_delete" value="<?php echo $song['song_id']?>" />
      </form>
    </td>
  </tr>
  <?php endforeach; ?>

  
  </table>

  <?php 
  
    echo "comment section here! ";

  ?>
<!-- </div>   -->


  <!-- CDN for JS bootstrap -->
  <!-- you may also use JS bootstrap to make the page dynamic -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> -->
  
  <!-- for local -->
  <!-- <script src="your-js-file.js"></script> -->  
  
</div>    
</body>
</html>