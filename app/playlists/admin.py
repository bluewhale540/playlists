from django.contrib import admin

from .models import (Album, Categories, Comment, Contains, CreatedBy, Follows,
                     InAlbum, Likes, Playlist, Song, User)

# Register your models here.
admin.site.register(Song)
admin.site.register(Album)
admin.site.register(User)
admin.site.register(InAlbum)
admin.site.register(Playlist)
admin.site.register(Categories)
admin.site.register(Comment)
admin.site.register(Contains)
admin.site.register(CreatedBy)
admin.site.register(Follows)
admin.site.register(Likes)
