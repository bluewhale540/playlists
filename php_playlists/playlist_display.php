<?php
require('connect-db.php');
session_start();

//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$_SESSION["is_public"]=0;
$_SESSION["owns_playlist"]= check_owner($_SESSION["playlist_id"]);

$playlist_name= get_playlist_info($_SESSION["playlist_id"]);

if($_SESSION["is_public"]==0 && $_SESSION["owns_playlist"]==0) {
    header("location: user-library.php");
    exit;
}

//  echo "owns playlist";
//  echo $_SESSION["owns_playlist"];

$likes_playlist= check_if_likes();
$list_of_songs= get_all_songs($_SESSION["playlist_id"]);//get

function check_if_likes(){
    global $db;
    $query = "select * from likes where playlist_id = :playlist_id and user_id= :user_id";
	
	$statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $_SESSION["playlist_id"]);
    $statement->bindValue(':user_id', $_SESSION["id"]);
    $statement-> execute();

	$results = $statement->fetch();   

	$statement->closeCursor();

    if(empty($results)) {
        return 0;
    }
    else {
        return 1;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Like") {
        like_playlist();
    }
    else if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Unlike") {
        unlike_playlist();
    }
    else if (!empty($_POST['btnAction']) && $_POST['btnAction'] == "Delete") {
        delete_song($_POST['song_to_delete']);
        $list_of_songs = get_all_songs($_SESSION["playlist_id"]);
    }
}

function like_playlist() {
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

function unlike_playlist() {
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

function delete_song($song_id) {
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

function check_owner($playlist_id) {
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

function get_playlist_info($playlist_id) {
    global $db;
	$query = "select * from playlist where playlist_id = :playlist_id";
	
	$statement= $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlist_id);
    $statement-> execute();

	$results = $statement->fetch();   

	$statement->closeCursor();

    $_SESSION["is_public"]= $results['is_public'];
    return $results['name'];
    
}

function get_all_songs($playlist_id) {
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
    
    else {
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
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Playlists</title>
</head>

<body>

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
<table class="table table-hover">
  <thead>
  <tr>
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
</body>
</html>