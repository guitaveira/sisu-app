from django import get_version
from django.views.generic import TemplateView
from .tasks import show_hello_world
from .models import DemoModel
from django.shortcuts import render, get_object_or_404
from .models import Feedback
from rest_framework import viewsets
from .serializers import FeedbackSerializer

# Create your views here.
class FeedbackViewSet(viewsets.ModelViewSet):
    queryset = Feedback.objects.all()
    serializer_class = FeedbackSerializer


def feedback_view(request, id):
    feedback = get_object_or_404(Feedback, id=id)
    response = render(request, '../templates/feedback_view.html',
                      {'feedback': feedback})
    return response

class ShowHelloWorld(TemplateView):
    template_name = 'hello_world.html'

    def get(self, *args, **kwargs):
        show_hello_world.apply()
        return super().get(*args, **kwargs)

    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        context['demo_content'] = DemoModel.objects.all()
        context['version'] = get_version()
        return context
