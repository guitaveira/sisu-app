from django.urls import path ,include
from rest_framework   import routers
from .views import feedback_view,  FeedbackViewSet

router = routers.DefaultRouter()
router.register(r'feedbacks', FeedbackViewSet)

urlpatterns = [
    path('feedback/<int:id>/view', feedback_view, name='feedback_view'),
    path('api/', include(router.urls))
]

# urlpatterns += static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
# urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
