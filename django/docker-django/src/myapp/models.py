from django.db import models
from django.contrib.auth.models import  User
from django.core.exceptions import ValidationError
import dns.resolver
from rest_framework.permissions import IsAuthenticated
from rest_framework_simplejwt.authentication import JWTAuthentication

def checkDns(email):
    domain = email.split('@')[1]
    try:
        dns.resolver.resolve(domain, 'MX')
    except dns.resolver.NXDOMAIN:
        raise ValidationError(f'O domínio {domain} não possui registros MX válidos.')
    except dns.resolver.LifetimeTimeout:
        raise ValidationError("Não foi possível contactar ao DNS")

class Feedback(models.Model):
    nome = models.TextField(blank=True, null=True)
    email = models.EmailField(validators=[checkDns])
    feedback = models.TextField(blank=True, null=True)
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    authentication_classes = [JWTAuthentication]
    permission_classes = [IsAuthenticated]
    class Meta:
        permissions = [
            ("change_only_yours", "Pode mudar apenas seus dados de Feedback"),
        ]
        managed = True
        db_table = 'feedback'


class DemoModel(models.Model):
    title = models.CharField(max_length=255)
    body = models.TextField()
    image = models.ImageField(upload_to="demo_images")

    def __str__(self):
        return self.title
