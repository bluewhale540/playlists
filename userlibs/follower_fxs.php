<?php

function getFollowers($userId) {
    global $db;
    $query = "SELECT * FROM user
        INNER JOIN follows ON user.user_id = follows.follower
        WHERE followed=:id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $userId);
    $statement->execute();

    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function getFollowing($userId) {
    global $db;
    $query = "SELECT * FROM user
        INNER JOIN follows ON user.user_id = follows.followed
        WHERE follower=:id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $userId);
    $statement->execute();

    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}
