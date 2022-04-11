<?php
require('connect-db.php');

session_start();
//check session
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$playlist_to_add = null;

if($_SERVER['REQUEST_METHOD'] == 'POST') //check if post was submitted
{ 
    if(!empty($_POST['btnAction']))
    {
        if($_POST['btnAction'] == "Create")
        {
            if($_POST['make_public'] == "No"){
                addPlaylist($_POST['name'], $_POST['date_created'], 0);
            }
            else if($_POST['make_public'] == "Yes"){
                addPlaylist($_POST['name'], $_POST['date_created'], 1);
            }
        }
    }
}

function addPlaylist($name, $date_created, $is_public)
{
    global $db;
    
    $query1 = "insert into playlist (name, date_created, num_likes, is_public)
    values(:name, :date_created, 0, :is_public)";
    $query2 = "insert into created_by values(:user_id, (select playlist_id from playlist where name=:name) )";
    $statement1 = $db->prepare($query1);
    $statement2 = $db->prepare($query2);

    $statement1->bindValue(':name', $name);
    $statement1->bindValue(':date_created', $date_created);
    $statement1->bindValue(':is_public', $is_public);
    
    $statement2->bindValue(':user_id', $_SESSION["id"]);
    $statement2->bindValue(':name', $name);
   
    $statement1->execute();
    $statement2->execute();
    $statement1->closeCursor();
    $statement2->closeCursor();
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

<div class="container">

    <h1>Create Playlist</h1>  
  
    <form name="mainForm" action="add-playlist.php" method="post">    
        <div class="row mb-3 mx-3">
            Playlist Name:
            <input type="text" class="form-control" name="name" required
                value="<?php if ($playlist_to_add!=null) echo $playlist_to_add['name'] ?>"
            />        
        </div> 
        <div class="row mb-3 mx-3">
            Today's Date (YYYY-MM-DD):
            <input type="text" class="form-control" name="date_created" required
                value="<?php if ($playlist_to_add!=null) echo $playlist_to_add['date_created'] ?>"
            />          
        </div>
        <div class="row mb-3 mx-3">
            Make Public? (Yes/No)
            <input type="text" class="form-control" name="make_public" required
                value="<?php if ($playlist_to_add!=null) echo $playlist_to_add['make_public'] ?>"
            />          
        </div>
        <input type="submit" value="Create" name="btnAction" class="btn btn-dark"/>
    </form> 
    <br>
    <p><a href="user-library.php">Go to your playlist library!</a></p>

</div>

  <!-- CDN for JS bootstrap -->
  <!-- you may also use JS bootstrap to make the page dynamic -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> -->
  
  <!-- for local -->
  <!-- <script src="your-js-file.js"></script> -->  

  
</div>    
</body>
</html>