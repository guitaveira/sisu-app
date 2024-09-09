from django.urls import path
from . import views
urlpatterns = [
    path('feedback/<int:id>/view/', views.feedback_view, name='feedback_view'),
]
