<?php

function getAllPlaylists($userId, $owner) {
    global $db;
    $query = '';
    if ($owner) {
        $query = 'SELECT *
            FROM playlist
            INNER JOIN created_by cb 
            ON playlist.playlist_id = cb.playlist_id
            WHERE user_id=:id';
    }
    else {
        $query = 'SELECT *
            FROM playlist
            INNER JOIN created_by cb 
            ON playlist.playlist_id = cb.playlist_id
            WHERE user_id=:id AND is_public=TRUE';
    }
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $userId);
    $statement->execute();

    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function getPopular($userId) {
    global $db;
    $query = "SELECT * FROM playlist 
        INNER JOIN created_by 
        ON playlist.playlist_id = created_by.playlist_id
        WHERE user_id=:id
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