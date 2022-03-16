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
    PRIMARY KEY (user_id, playlist_id),
    FOREIGN KEY (playlist_id) REFERENCES playlist(playlist_id)
);

CREATE TABLE created_by (
    user_id int NOT NULL,
    playlist_id int NOT NULL PRIMARY KEY,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (playlist_id) REFERENCES playlist(playlist_id)
);

CREATE TABLE contains (
    playlist_id int NOT NULL,
    song_id INT NOT NULL,
    PRIMARY KEY (playlist_id, song_id),
    FOREIGN KEY (playlist_id) REFERENCES playlist(playlist_id),
    FOREIGN KEY (song_id) REFERENCES song(song_id)
);

CREATE TABLE in_album (
    song_id INT NOT NULL PRIMARY KEY,
    album_id INT NOT NULL,
    FOREIGN KEY (song_id) REFERENCES song(song_id),
    FOREIGN KEY (album_id) REFERENCES album(album_id)
);

CREATE TABLE follows (
    follower INT NOT NULL,
    followed INT NOT NULL,
    PRIMARY KEY (follower, followed),
    FOREIGN KEY (follower) REFERENCES user(user_id),
    FOREIGN KEY (followed) REFERENCES user(user_id)
);

CREATE TABLE comment (
    user_id INT NOT NULL,
    playlist_id INT NOT NULL,
    the_comment VARCHAR(1000) NOT NULL,
    PRIMARY KEY (user_id, playlist_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (playlist_id) REFERENCES playlist(playlist_id)
);