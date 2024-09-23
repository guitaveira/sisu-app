import logging

from django import get_version
from django.views.generic import TemplateView
from rest_framework.settings import api_settings

from .tasks import show_hello_world
from .models import DemoModel
from django.shortcuts import render, get_object_or_404
from .models import Feedback
from rest_framework import viewsets
from .serializers import FeedbackSerializer
from .permissions import *
from rest_framework import permissions

# Create your views here.
class FeedbackViewSet(viewsets.ModelViewSet):
    queryset = Feedback.objects.all()
    serializer_class = FeedbackSerializer

    action_permissions = {
        'create': ['myapp.feedback_create',IsUserFeedbackOwner],
        'retrieve': ['myapp.feedback_retrieve'],
        'update': ['myapp.feedback.update_feedback', IsUserFeedbackOwner],  # Verifica a permissão do Django e se é o dono
        'partial_update': ['myapp.feedback_partial_update', IsUserFeedbackOwner],
        'list': ['myapp.feedback_list'],
        'destroy': ['myapp.feedback_delete'],
    }

    def get_permissions(self):
        #logging.getLogger('myapp').error(f"Valor da variável:, {self.action} ")
        permission_classes = self.action_permissions.get(self.action,[])
        permission_classes += api_settings.DEFAULT_PERMISSION_CLASSES

        # Adiciona permissões customizadas e padrão
        permission_list = []
        for permission in permission_classes:
            if isinstance(permission, str):
                # Se for uma permissão do Django, checa via `has_perm()`
                if not self.request.user.has_perm(permission):
                    self.permission_denied(self.request, message=f"You do not have permission to {self.action}.")
            else:
                # Se for uma classe de permissão customizada, instancia ela
                permission_list.append(permission())

        return permission_list


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
