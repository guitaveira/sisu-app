from jinja2 import Environment, FileSystemLoader
import os
import cgi
from html import escape
from urllib.parse import urlencode

class Controller:
    def __init__(self,env):
        self.environ=env
        self.data = ""
        self.status = "200 OK"
        self.redirect_url = ""
        self.session = env['session']
        self.nome=(self.__class__.__name__).lower()[:-len("controller")]
        self.env = Environment(loader=FileSystemLoader(os.getcwd()+f'/views/{self.nome}'))

    def form2dict(self,form):
        dict ={}
        for  key in form:
            dict[key]= escape(form.getvalue(key))
        return dict
    def loadForm(self,model):
        form = cgi.FieldStorage(fp=self.environ["wsgi.input"], environ=self.environ)
        model.fill(self.form2dict(form))

    def redirectPage(self,path:str,params=None):
        self.status = "302 OK"
        self.redirect_url = f'/app/{self.nome}/{path}'
        if params:
            self.redirect_url += f'?{urlencode(params)}'

    def notFound(self):
        self.status = "404 Not Found"
        template = Environment(loader=FileSystemLoader(os.getcwd()+f'/views/public')).get_template("404.html")
        self.data=template.render()