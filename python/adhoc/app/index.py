import cgi
from distutils import log
from wsgiref import simple_server
from urllib.parse import parse_qs
from jinja2 import Environment, FileSystemLoader
from orator import DatabaseManager, Model
import os

class Feedback(Model):
    __table__ = 'feedback'
    __timestamps__ = False

def app(environ, start_response):
    path = environ["PATH_INFO"]
    method = environ["REQUEST_METHOD"]
    query = environ["QUERY_STRING"]
    env = Environment(loader=FileSystemLoader(os.getcwd()+'/views' ))
    data=""
    status = "200 OK"
    redirect_url = ""
    if path == "/app/feedback/view":
        params=parse_qs(query)
        feedback=Feedback.find(params['id'][0])
        if feedback:
            template = env.get_template("view.html")
            data = template.render(feedback=feedback)
    if path == "/app/feedback/create":
        template = env.get_template("create.html")
        if method == "POST":
            form = cgi.FieldStorage(fp=environ["wsgi.input"], environ=environ)
            feedback = Feedback()
            feedback.nome= form.getvalue("name")
            feedback.email = form.getvalue("email")
            feedback.feedback = form.getvalue("feedback")
            if "@" in feedback.email:
                feedback.save()
                status = "302 Found"
                redirect_url= '/app/feedback/view?id=' + str(feedback.id)
            else:
                feedback.error='Email deve conter @'
                data = template.render(feedback=feedback)
        else:
            feedback = {
                "name": "",
                "email": "",
                "feedback": "",
                "error" : ""
            }
            data = template.render(feedback=feedback)

    start_response(status, [
        ("Content-Type", "text/html"),
        ("location",redirect_url),
        ("Content-Length", str(len(data)))
    ])
    return [data.encode()]

if __name__ == '__main__':
    config = {
        'pgsql': {
            'driver': 'pgsql',
            'host': 'db',
            'database': os.environ['DB_DATABSE'],
            'user': os.environ['DB_USER'],
            'password': os.environ['DB_PASSWORD'],
            'prefix': ''
        }
    }

    db = DatabaseManager(config)
    Model.set_connection_resolver(db)
    w_s = simple_server.make_server(
        host="",
        port=8000,
        app=app
    )
    w_s.serve_forever()