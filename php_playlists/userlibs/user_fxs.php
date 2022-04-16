<?php

function getUser($userId) {
    global $db;
    $query = "SELECT * FROM user
        WHERE user_id=:id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $userId);
    $statement->execute();

    $results = $statement->fetch();
    $statement->closeCursor();
    return $results;
}

function getLikedPlaylists($userId, $owner) {
    global $db;
    $query = '';
    if ($owner) {
        $query = 'SELECT *
            FROM playlist
            INNER JOIN likes
            ON playlist.playlist_id = likes.playlist_id
            WHERE user_id=:id';
    }
    else {
        $query = 'SELECT *
            FROM playlist
            INNER JOIN likes 
            ON playlist.playlist_id = likes.playlist_id
            WHERE user_id=:id AND is_public=TRUE';
    }
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $userId);
    $statement->execute();

    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}
