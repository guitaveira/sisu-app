from django.db import models
from django.contrib.auth.models import User

class Feedback(models.Model):
    nome = models.CharField(max_length=255,null=False)
    email = models.EmailField(null=False)
    feedback = models.TextField(null=False)
    user= models.ForeignKey(User,null=True,on_delete=models.SET_NULL)
    class Meta:
        permissions=[
            ('change_only_yours','Can  change  only yours feedbacks')
        ]
    def __str__(self):
        return self.nome


class DemoModel(models.Model):
    title = models.CharField(max_length=255)
    body = models.TextField()
    image = models.ImageField(upload_to="demo_images")

    def __str__(self):
        return self.title
