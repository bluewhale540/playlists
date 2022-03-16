import base64

import requests
import os

client_id = os.environ['client_id']
client_secret = os.environ['client_secret']
client_info = f"{client_id}:{client_secret}".encode()
spotify_base_url = 'https://api.spotify.com/v1/browse/featured-playlists'
req_auth = requests.post('https://accounts.spotify.com/api/token',
                         headers={
                             'Authorization': f'Basic {base64.b64encode(client_info).decode()}'}, data={'grant_type': 'client_credentials'})
access_token = req_auth.json()['access_token']


play_href = "https://api.spotify.com/v1/playlists/37i9dQZF1DWYcDQ1hSjOpY/tracks"

p_songs = requests.get(play_href, headers={
    'Authorization': f'Bearer {access_token}', 'Content-Type': 'application/json'})
p_songs = p_songs.json()
songs = []
albums = []
in_albums = []
#song(song_id (U, NN), title (NN), artist (NN))
# inAlbum(song_id (NN), album_id (NN))
# album(album_id (U, NN), title (NN), artist (NN), year (NN))
song_id = 1
album_id = 1
for song in p_songs['items'][:20]:

    songs.append({
        "song_id": song_id,
        "title": song['track']['name'],
        'artist': song['track']['artists'][0]['name']
    })
    release_date = song['track']['album']['release_date']

    albums.append({
        'album_id': album_id,
        'title': song['track']['album']['name'],
        'artist': song['track']['album']['artists'][0]['name'],
        'year': release_date
    })
    in_albums.append({
        'song_id': song_id,
        'album_id': album_id
    })
    song_id += 1
    album_id += 1

with open('init_data.sql', 'w') as file:
    for song in songs:
        file.write('INSERT INTO song VALUES({},"{}", "{}");\n'.format(
            song['song_id'], song['title'], song['artist']))
    for album in albums:
        file.write(
            'INSERT INTO album VALUES({}, "{}", "{}", "{}");\n'.format(album['album_id'], album['title'], album['artist'], album['year']))
    for in_album in in_albums:
        file.write('INSERT INTO in_album VALUES({}, {});\n'.format(
            in_album['song_id'], in_album['album_id']))
