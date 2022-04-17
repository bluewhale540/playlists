<?php

function delete_song($song_id) {
    global $db;
    $query = "delete from in_album where song_id = :song_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':song_id', $song_id);
    $statement->execute();
    $statement->closeCursor();

    $query = "delete from contains where song_id = :song_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':song_id', $song_id);
    $statement->execute();
    $statement->closeCursor();

    $query = "delete from song where song_id = :song_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':song_id', $song_id);
    $statement->execute();
    $statement->closeCursor();
}


function get_all_songs($playlist_id) {
    global $db;
    $query = "select * from contains where playlist_id = :playlist_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':playlist_id', $playlist_id);
    $statement->execute();

    $results = $statement->fetchAll();

    $statement->closeCursor();
    $song_list = [];

    foreach ($results as $song_id) {
        //get name and artists
        $query = "select * from song where song_id = :song_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':song_id', $song_id['song_id']);
        $statement->execute();

        $songinfo = $statement->fetch();

        $statement->closeCursor();

        //get album that song is in

        $query = "select album_id from in_album where song_id = :song_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':song_id', $song_id['song_id']);
        $statement->execute();

        $album_id = $statement->fetch();

        $statement->closeCursor();

        //get info from album

        $query = "select * from album where album_id = :album_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':album_id', $album_id['album_id']);
        $statement->execute();

        $albuminfo = $statement->fetch();

        $statement->closeCursor();
        //get genre
        $query = "select * from categories where title = :title and artist= :artist";
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $songinfo['title']);
        $statement->bindValue(':artist', $songinfo['artist']);
        $statement->execute();

        $genreinfo = $statement->fetch();

        $statement->closeCursor();
        //combine info together

        $song = [];
        $song['song_id'] = $song_id['song_id'];
        $song['name'] = $songinfo['title'];
        $song['artist'] = $songinfo['artist'];
        $song['album'] = $albuminfo['title'];
        $song['year'] = $albuminfo['date_released'];

        if (!empty($genreinfo)) {
            $song['genre'] = $genreinfo['genre'];
        } else {
            $song['genre'] = "N/A";
        }
        array_push($song_list, $song);
    }
    return $song_list;
}
