from urllib.parse import parse_qs
import sys
sys.path.append('./app')
import cgi
from jinja2 import Environment, FileSystemLoader
from models.Feedback import Feedback
from controllers.Controller import Controller
import os
from html import escape

class FeedbackController(Controller):

    def create(self):
        method = self.environ["REQUEST_METHOD"]
        template = self.env.get_template("create.html")
        feedback = Feedback()
        if method == "POST":
            form = cgi.FieldStorage(fp=self.environ["wsgi.input"], environ=self.environ)
            feedback.nome= form.getvalue("name")
            feedback.email = form.getvalue("email")
            feedback.feedback = escape(form.getvalue("feedback"))
            if feedback.save():
                self.redirectPage('/app/feedback/view?id=' + str(feedback.id))
            else:
                feedback.error='Email deve conter @'

        self.data = template.render(feedback=feedback)

    def view(self, id):
        feedback=Feedback.find(id[0])
        if feedback:
            template = self.env.get_template("view.html")
            self.data = template.render(feedback=feedback)
        else:
            self.notFound()


    def delete(self,id):
        feedback = Feedback.find(id[0])
        if feedback:
            feedback.delete()
            self.redirectPage("/app/feedback/index")
        else:
            self.notFound()

    def index(self):
        feedbacks = Feedback.all()
        template = self.env.get_template("index.html")
        self.data = template.render(feedbacks=feedbacks)
