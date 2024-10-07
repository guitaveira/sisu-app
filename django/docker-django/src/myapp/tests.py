from django.test import TestCase, Client
from django.contrib.auth.models import User
from .models import Feedback
# Create your tests here.
class FeedBackTestCase(TestCase):

    @classmethod
    def setUpTestData(cls):
        us=User.objects.create_superuser("superadmin",password="superadmin")
    def test_can_view_feedback_form(self):
        c = self.client
        self.assertTrue(c.login(username="superadmin",password="superadmin"))
        response=c.get("/admin/myapp/feedback/add/")
        #print(response)
        self.assertEqual(response.status_code,200)
    def test_can_add_feedback_form(self):
        c = self.client
        self.assertTrue(c.login(username="superadmin",password="superadmin"))
        response=c.post("/admin/myapp/feedback/add/", {
            "nome" : "teste",
            "email" :"teste@gmail.com",
            "feedback": "Nada a delarar",
            "_save" : "Save",
            "user" : "1"
            }
        )
        #print(response.content.decode())
        self.assertEqual(response.status_code,302)
        query= Feedback.objects.filter(nome="teste",email="teste@gmail.com",feedback="Nada a delarar")
        self.assertEqual(query.count(),1)
    def test_error_email_feedback_form(self):
        c = self.client
        self.assertTrue(c.login(username="superadmin",password="superadmin"))
        fakedomain = "ifgnao.com"
        response=c.post("/admin/myapp/feedback/add/", {
            "nome" : "teste",
            "email" :"teste@"+fakedomain,
            "feedback": "Nada a delarar",
            "_save" : "Save"
        })
        self.assertEqual(response.status_code,200)
        query= Feedback.objects.filter(nome="teste",email="teste@"+fakedomain,
                                       feedback="Nada a delarar")
        self.assertEqual(query.count(),0)
        data= response.content.decode('utf-8')
        needle = f'O domínio {fakedomain} não possui registros MX válidos.'
        position=data.find(needle)
        if position==-1:
            needle2 = "Não foi possível contactar ao DNS"
            position=data.find(needle2)
        self.assertNotEquals(position,-1)