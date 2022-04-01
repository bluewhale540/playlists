from django.db import models

# Create your models here.


class Song(models.Model):
    class Meta:
        db_table = "song"
    song_id = models.AutoField(primary_key=True)
    title = models.CharField(max_length=200)
    artist = models.CharField(max_length=200)


class Playlist(models.Model):
    playlist_id = models.AutoField(primary_key=True)
    name = models.CharField(max_length=200)
    date_created = models.DateField()
    num_likes = models.IntegerField()
    is_public = models.IntegerField()

    class Meta:

        db_table = 'playlist'


class User(models.Model):
    user_id = models.AutoField(primary_key=True)
    email = models.CharField(max_length=100)
    num_followers = models.IntegerField()
    password = models.CharField(max_length=100)

    class Meta:

        db_table = 'user'


class Follows(models.Model):
    follower = models.OneToOneField(
        'User', models.DO_NOTHING, db_column='follower', primary_key=True, related_name='user')
    followed = models.ForeignKey(
        'User', models.DO_NOTHING, db_column='followed')

    class Meta:

        db_table = 'follows'
        unique_together = (('follower', 'followed'),)


class InAlbum(models.Model):
    song = models.OneToOneField('Song', models.DO_NOTHING, primary_key=True)
    album = models.ForeignKey('Album', models.DO_NOTHING)

    class Meta:

        db_table = 'in_album'


class Likes(models.Model):
    user_id = models.IntegerField(primary_key=True)
    playlist = models.ForeignKey('Playlist', models.DO_NOTHING)

    class Meta:

        db_table = 'likes'
        unique_together = (('user_id', 'playlist'),)


class CreatedBy(models.Model):
    user = models.ForeignKey('User', models.DO_NOTHING)
    playlist = models.OneToOneField(
        'Playlist', models.DO_NOTHING, primary_key=True)

    class Meta:

        db_table = 'created_by'


class Contains(models.Model):
    playlist = models.OneToOneField(
        'Playlist', models.DO_NOTHING, primary_key=True)
    song = models.ForeignKey('Song', models.DO_NOTHING)

    class Meta:

        db_table = 'contains'
        unique_together = (('playlist', 'song'),)


class Comment(models.Model):
    user = models.OneToOneField('User', models.DO_NOTHING, primary_key=True)
    playlist = models.ForeignKey('Playlist', models.DO_NOTHING)
    the_comment = models.CharField(max_length=1000)

    class Meta:

        db_table = 'comment'
        unique_together = (('user', 'playlist'),)


class Categories(models.Model):
    title = models.CharField(primary_key=True, max_length=200)
    artist = models.CharField(max_length=100)
    genre = models.CharField(max_length=100, blank=True, null=True)

    class Meta:

        db_table = 'categories'
        unique_together = (('title', 'artist'),)


class Album(models.Model):
    album_id = models.AutoField(primary_key=True)
    title = models.CharField(max_length=200)
    artist = models.CharField(max_length=100)
    date_released = models.DateField()

    class Meta:

        db_table = 'album'
