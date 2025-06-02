from urllib.parse import parse_qs
import sys
sys.path.append('./app')
from jinja2 import Environment, FileSystemLoader
from models.Feedback import Feedback
from controllers.Controller import Controller
import os


class FeedbackController(Controller):

    def create(self):
        method = self.environ["REQUEST_METHOD"]
        template = self.env.get_template("create.html")
        feedback = Feedback()
        if method == "POST":
            self.loadForm(feedback)
            if feedback.save():
                self.redirectPage('view', {'id': feedback.id})

        self.data = template.render(feedback=feedback)

    def view(self, id):
        feedback=Feedback.find(id[0])
        if feedback:
            template = self.env.get_template("view.html")
            self.data = template.render(feedback=feedback)
        else:
            self.notFound()

    def update(self,id):
        method = self.environ["REQUEST_METHOD"]
        template = self.env.get_template("create.html")
        feedback = Feedback.find(id[0])
        if feedback:
            if method == "POST":
                self.loadForm(feedback)
                if feedback.save():
                    self.redirectPage('view', {'id': feedback.id})
        else:
            self.notFound()
            return

        self.data = template.render(feedback=feedback)


    def delete(self,id):
        feedback = Feedback.find(id[0])
        if feedback:
            feedback.delete()
            self.session['flash'] = 'Feedback Deletado com sucesso'
            self.redirectPage('index')
        else:
            self.notFound()

    def index(self):
        feedbacks = Feedback.all()
        message=""
        if 'flash' in self.session:
            message=self.session['flash']
            self.session['flash']=""
        template = self.env.get_template("index.html")
        self.data = template.render(feedbacks=feedbacks,message=message)


