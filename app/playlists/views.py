from django.shortcuts import render
from django.views.generic import ListView

from .models import Playlist

# Create your views here.


class PlaylistsView(ListView):
    model = Playlist
    template_name = "playlists/index.html"
    context_object_name = "playlists_list"
