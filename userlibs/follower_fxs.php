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

function followUser($current_user, $target_user){
    global $db;
    $query = $db->prepare('INSERT INTO follows (follower, followed) VALUES(:current_user, :target_user)');
    $query->bindValue(':current_user', $current_user);
    $query->bindValue(':target_user', $target_user);
    $query->execute();
    $query->closeCursor();
}

function unFollowUser($current_user, $target_user){
    global $db;
    $query = $db->prepare('DELETE from follows where follower=:current_user and followed=:target_user');
    $query->bindValue(':current_user', $current_user);
    $query->bindValue(':target_user', $target_user);
    $query->execute();
    $query->closeCursor();
}

function checkFollowing($current_user, $target_user){
    global $db;
    $query = $db->prepare('select * from follows where follower=:current_user and followed=:target_user');
    $query->bindValue(':current_user', $current_user);
    $query->bindValue(':target_user', $target_user);
    $query->execute();
  
    $result = $query->fetch();
    $query->closeCursor();
    if(empty($result)){
        return false;
    } 
    return true;
}
