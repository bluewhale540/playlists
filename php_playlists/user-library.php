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
            header("location: playlist-display.php");
            $_SESSION["playlist_id"] = $_POST['id'];
            
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
  <meta charset="UTF-8">  
  
  <!-- 2. include meta tag to ensure proper rendering and touch zooming -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- 
  Bootstrap is designed to be responsive to mobile.
  Mobile-first styles are part of the core framework.
   
  width=device-width sets the width of the page to follow the screen-width
  initial-scale=1 sets the initial zoom level when the page is first loaded   
  -->
  
  <meta name="author" content="Shreya Moharir">
  <meta name="description" content="include some description about your page">  
    
  <title>POTD5</title>
  
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

<body> <!--everything displayed on screen-->

<div class="container">
    <h1>My Library</h1>        
    
    <p><a href="add-playlist.php">Create a new playlist!</a></p>

<hr/>

<table class="w3=table w3-bordered w3-card-4" style="width:90%">
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
                    value="<?php echo $playlist['id'] ?>" />
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