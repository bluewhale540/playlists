<?php

switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/':                   // URL (without file name) to a default screen
    case '/login.php':
        require 'login.php';
        break;
    case '/signup.php':     // if you plan to also allow a URL with the file name
        require 'signup.php';
        break;
    case '/homepage.php':
        require 'homepage.php';
        break;
    case '/library.php':
        require 'library.php';
        break;
    case 'add_playlist.php':
        require 'add_playlist.php';
        break;
    case '/search.php':
        require 'search.php';
        break;
    case '/profile.php':
        require 'profile.php';
        break;
    case '/signout.php':
        require 'signout.php';
        break;
    case '/playlist_display.php':
        require 'playlist_display.php';
        break;
    case '/add_song_to_playlist.php':
        require 'add_song_to_playlist.php';
        break;
    default:
        http_response_code(405);
        exit('Not Found');
}


