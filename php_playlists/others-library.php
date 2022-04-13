<?php
require('connect-db.php');

session_start();
//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$user_id = null;
$username = null;

//For testing:
//$user_id = 25;
//$username = "Shreya";
$list_of_playlists = getTheirPlaylists();

if($_SERVER['REQUEST_METHOD'] == 'POST') //check if post was submitted
{ 
    if(!empty($_POST['btnAction']))
    {
        if($_POST['btnAction'] == "View")
        {
            header("location: playlist-display.php");
            $_SESSION["playlist_id"] = $_POST['id'];
        }
    }
}

function setID($id, $email){
    $user_id = $id;
    $username = $email;
}

function getTheirPlaylists()
{
    global $db;
    global $user_id;
    $query = "select * from playlist natural join created_by where user_id=:id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $user_id);
    $statement->execute();

    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
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
    <h1><?php echo $username;?>'s Library</h1>    
    <p><a href="user-library.php">Go back to my library</a></p>    

<hr/>

<table class="w3=table w3-bordered w3-card-4" style="width:90%">
    <thead>
    <tr style="background-color:#B0B0B0">
        <th width="18%">Playlist Name</th>
        <th width="5%"></th>
        <th width="8%">Date Created</th>
        <th width="5%">Likes</th>
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