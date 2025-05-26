from urllib.parse import parse_qs
import sys
sys.path.append('./app')
import cgi
from jinja2 import Environment, FileSystemLoader
from models.Feedback import Feedback
import os
from html import escape

class FeedbackController():
    def __init__(self,env):
        self.environ=env
        self.data = ""
        self.status = "200 OK"
        self.redirect_url = ""
        self.env = Environment(loader=FileSystemLoader(os.getcwd()+'/views' ))

    def create(self):
        method = self.environ["REQUEST_METHOD"]
        template = self.env.get_template("create.html")
        feedback = Feedback()
        if method == "POST":
            form = cgi.FieldStorage(fp=self.environ["wsgi.input"], environ=self.environ)
            feedback.nome= form.getvalue("name")
            feedback.email = form.getvalue("email")
            feedback.feedback = escape(form.getvalue("feedback"))
            if "@" in feedback.email:
                feedback.save()
                self.status = "302 Found"
                self.redirect_url= '/app/feedback/view?id=' + str(feedback.id)
            else:
                feedback.error='Email deve conter @'
        self.data = template.render(feedback=feedback)

    def view(self, id):
        feedback=Feedback.find(id[0])
        if feedback:
            template = self.env.get_template("view.html")
        else:
            template = self.env.get_template("404.html")
        self.data = template.render(feedback=feedback)

    def delete(self,id):
        feedback = Feedback.find(id[0])
        if feedback:
            feedback.delete()
            self.status = "302 Found"
            self.redirect_url="/app/feedback/index"
        else:
            template = self.env.get_template("404.html")
            self.data = template.render()

    def index(self):
        feedbacks = Feedback.all()
        template = self.env.get_template("index.html")
        self.data = template.render(feedbacks=feedbacks)
