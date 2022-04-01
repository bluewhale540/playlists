
from django.urls import include, path
from .views import PlaylistsView

urlpatterns = [
    path('', PlaylistsView.as_view(), name='playlists'),

]
