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

function addComment($playlistId, $userId, $comment) {
    global $db;
    $query = 'INSERT INTO comment VALUES (:userId, :playlistId, :comment)';
    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $userId);
    $statement->bindValue(':playlistId', $playlistId);
    $statement->bindValue(':comment', $comment);
    $statement->execute();
    $statement->closeCursor();
}

function getComments($playlist_id) {
    global $db;
    $query = 'select email, the_comment FROM comment 
        INNER JOIN playlist ON comment.playlist_id = playlist.playlist_id
        INNER JOIN user u on comment.user_id = u.user_id
        WHERE comment.playlist_id = :playlist_id';

    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlist_id);
    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();
    return $results;
}

function check_if_likes($playlistId, $userId): int {
    global $db;
    $query = "select * from likes where playlist_id = :playlist_id and user_id= :user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlistId);
    $statement->bindValue(':user_id', $userId);
    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    if (empty($results)) {
        return 0;
    } else {
        return 1;
    }
}

function like_playlist($playlistId, $userId) {
    global $db;
    $query = "insert into likes values(:user_id,:playlist_id) ";
    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlistId);
    $statement->bindValue(':user_id', $userId);
    $statement->execute();
    $statement->closeCursor();

    $query = "update playlist set num_likes = num_likes+1 where playlist_id= :playlist_id ";
    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlistId);
    $statement->execute();
    $statement->closeCursor();

    header("location: playlist_display.php?playlist=".$playlistId);
}

function unlike_playlist($playlistId, $userId) {
    global $db;
    $query = "delete from likes where playlist_id = :playlist_id and user_id= :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlistId);
    $statement->bindValue(':user_id', $userId);
    $statement->execute();
    $statement->closeCursor();

    $query = "update playlist set num_likes = num_likes-1 where playlist_id= :playlist_id ";
    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlistId);
    $statement->execute();
    $statement->closeCursor();

    header("location: playlist_display.php?playlist=".$playlistId);
}

function check_owner($playlistId, $userId): int
{
    global $db;
    $query = "select * from created_by where playlist_id = :playlist_id and user_id= :user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlistId);
    $statement->bindValue(':user_id', $userId);
    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    if (empty($results)) {
        return 0;
    } else {
        return 1;
    }
}

function get_playlist_name($playlistId) {
    global $db;
    $query = "select name from playlist where playlist_id = :playlist_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlistId);
    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    return $results[0];
}

function is_public($playlistId) {
    global $db;
    $query = "select is_public from playlist where playlist_id = :playlist_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlistId);
    $statement->execute();

    $results = $statement->fetch();

    $statement->closeCursor();

    return $results[0];
}
