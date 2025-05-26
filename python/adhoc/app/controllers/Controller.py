from jinja2 import Environment, FileSystemLoader
import os
class Controller:
    def __init__(self,env):
        self.environ=env
        self.data = ""
        self.status = "200 OK"
        self.redirect_url = ""
        self.env = Environment(loader=FileSystemLoader(os.getcwd()+'/views' ))
    def redirectPage(self,url:str):
        self.status = "302 OK"
        self.redirect_url = url
    def notFound(self):
        self.status = "404 Not Found"
        template = self.env.get_template("404.html")
        self.data=template.render()