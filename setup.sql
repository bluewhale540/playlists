CREATE TABLE categories (
    title VARCHAR(200) NOT NULL,
    artist VARCHAR(100) NOT NULL,
    genre VARCHAR(100),
    PRIMARY KEY (title, artist)
);

CREATE TABLE song (
    song_id int UNIQUE NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    artist VARCHAR(100) NOT NULL
);

CREATE TABLE user (
    user_id int UNIQUE NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    num_followers int NOT NULL,
    password VARCHAR(100) NOT NULL
);

CREATE TABLE playlist (
    playlist_id int UNIQUE NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    creator int NOT NULL,
    date_created date NOT NULL,
    num_likes int NOT NULL,
    is_public bool NOT NULL
);

CREATE TABLE album (
    album_id int UNIQUE NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    artist VARCHAR(100) NOT NULL,
    date_released date NOT NULL
);

CREATE TABLE likes (
    user_id int NOT NULL,
    playlist_id int NOT NULL,
    PRIMARY KEY (user_id, playlist_id)
);

CREATE TABLE created_by (
    user_id int NOT NULL,
    playlist_id int NOT NULL PRIMARY KEY
);

CREATE TABLE contains (
    playlist_id int NOT NULL,
    song_id INT NOT NULL,
    PRIMARY KEY (playlist_id, song_id)
);

CREATE TABLE inAlbum (
    song_id INT NOT NULL PRIMARY KEY,
    album_id INT NOT NULL
);

CREATE TABLE follows (
    follower INT NOT NULL,
    followed INT NOT NULL,
    PRIMARY KEY (follower, followed)
);

CREATE TABLE comment (
    user_id INT NOT NULL,
    playlist_id INT NOT NULL,
    the_comment VARCHAR(1000) NOT NULL,
    PRIMARY KEY (user_id, playlist_id)
);