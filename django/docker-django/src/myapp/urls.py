from django.urls import path , include
from . import views
from rest_framework import routers
from .views import FeedbackViewSet

router = routers.DefaultRouter()
router.register(r'feedbacks', FeedbackViewSet)

urlpatterns = [
    path('feedback/<int:id>/view/', views.feedback_view, name='feedback_view'),
    path('api/', include(router.urls)),
]
