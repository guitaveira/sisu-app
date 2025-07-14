from django.db import models
from django.contrib.auth.models import User

class Feedback(models.Model):
    nome = models.CharField(max_length=255,null=False)
    email = models.EmailField(null=False)
    feedback = models.TextField(null=False)
    user= models.ForeignKey(User,null=True,on_delete=models.SET_NULL)
    class Meta:
        permissions=[
            ('change_only_yours','Can  change  only yours feedbacks'),
            ('feedback_list', "Listar  feedback API"),
            ('feedback_retrieve', "Reuperar 1 registro  feedback API"),
            ('feedback_update', "atualizar 1 registro  feedback API"),
            ('feedback_partialupdate', "atualizar parcialmente  feedback API"),
            ('feedback_create', "Adicionar feedback API"),
            ('feedback_delete', "Deleter  feedback API"),
        ]
    def __str__(self):
        return self.nome


class DemoModel(models.Model):
    title = models.CharField(max_length=255)
    body = models.TextField()
    image = models.ImageField(upload_to="demo_images")

    def __str__(self):
        return self.title
