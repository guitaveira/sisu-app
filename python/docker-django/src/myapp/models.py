from django.db import models


class Feedback(models.Model):
    nome = models.CharField(max_length=255,null=False)
    email = models.EmailField(null=False)
    feedback = models.TextField(null=False)
    def __str__(self):
        return self.nome


class DemoModel(models.Model):
    title = models.CharField(max_length=255)
    body = models.TextField()
    image = models.ImageField(upload_to="demo_images")

    def __str__(self):
        return self.title
