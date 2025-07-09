from django.contrib import admin
from django.urls import reverse
from django.utils.html import format_html

from .models import DemoModel , Feedback
from .forms import FeedbackForm
class FeedbackAdmin(admin.ModelAdmin):
    list_display = ['nome', 'viewlink', 'editlink']
    form=FeedbackForm

    def get_queryset(self, request):
        qs=super().get_queryset(request)
        if not request.user.is_superuser:
            return qs.filter(user=request.user)
        return qs
    def get_form(self, request, obj=None, change=False, **kwargs):
        form = super().get_form(request,obj,**kwargs)
        form._request = request
        return form

    def viewlink(self,obj):
        return format_html('<a href={} >Visualizar</a>',
                           reverse('feedback_view',args=[obj.id]))
    viewlink.short_description="Visualizar"
    def editlink(self,obj):
        return format_html('<a href={} >Editar</a>',
                           reverse('admin:myapp_feedback_change',args=[obj.id]))
    editlink.short_description="Editar"


admin.site.register(Feedback,FeedbackAdmin)

