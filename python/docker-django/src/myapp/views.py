from django import get_version
from django.core.exceptions import PermissionDenied
from django.shortcuts import get_object_or_404, render
from django.views.generic import TemplateView
from django.contrib import admin
from rest_framework import viewsets
from .tasks import show_hello_world
from .models import DemoModel , Feedback
from .serializers import FeedbackSerializer

class FeedbackViewSet(viewsets.ModelViewSet):
    queryset = Feedback.objects.all()
    serializer_class = FeedbackSerializer

def feedback_view(request,id):
    feedback=get_object_or_404(Feedback,id=id)
    if request.user != feedback.user:
        raise PermissionDenied()
    return render(request,'../templates/feedback_view.html',{
        **admin.site.each_context(request),
        'feedback':feedback
    })
# Create your views here.


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
