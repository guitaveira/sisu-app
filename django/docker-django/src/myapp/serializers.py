from .models import  Feedback
from rest_framework import  serializers

# Serializers define the API representation.
class FeedbackSerializer(serializers.ModelSerializer):
    class Meta:
        model = Feedback
        fields = '__all__'