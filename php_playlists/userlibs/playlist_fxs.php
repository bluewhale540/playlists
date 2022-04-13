<?php

function getAllPlaylists() {
    global $db;
    $query = "select * from playlist natural join created_by where user_id=:id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $_SESSION["id"]);
    $statement->execute();

    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function getPopular($userId) {
    global $db;
    $query = "SELECT * FROM playlist 
        natural join created_by WHERE user_id=:id
        ORDER BY num_likes DESC
        LIMIT 3";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $userId);
    $statement->execute();

    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function deletePlaylist($playlist_id) {
    global $db;

    $toDelete = ["created_by", "contains", "comment", "likes", "playlist"];
    for ($i = 0; $i < sizeof($toDelete); $i++) {
        $query = "DELETE FROM {$toDelete[$i]} WHERE playlist_id=:playlist_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':playlist_id', $playlist_id);
        $statement->execute();
        $statement->closeCursor();
    }
}