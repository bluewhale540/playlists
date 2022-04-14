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
