<?php

function searchPlaylists($query_term) {
    global $db;
    $query_term = '%' . $query_term . '%';
    $query = "select * from playlist where name LIKE :query_term";
    $q = $db->prepare($query);
    $q->bindValue(':query_term', $query_term);
    $q->execute();
    $r = $q->fetchAll();

    $q->closeCursor();
    return $r;
}

function searchUsers($query_term) {
    global $db;
    $query_term = '%' . $query_term . '%';
    $query = "select * from user where email LIKE :query_term";
    $q = $db->prepare($query);
    $q->bindValue(':query_term', $query_term);
    $q->execute();
    $r = $q->fetchAll();

    $q->closeCursor();
    return $r;
}

function searchSongs($query_term) {
    global $db;
    $query_term = '%' . $query_term . '%';
    $query = "select * from song where title LIKE :query_term";
    $q = $db->prepare($query);
    $q->bindValue(':query_term', $query_term);
    $q->execute();
    $r = $q->fetchAll();

    $q->closeCursor();
    return $r;
}