import cgi
from wsgiref import simple_server
from jinja2 import Environment, FileSystemLoader
import os
import psycopg2


def app(environ, start_response):
    path = environ["PATH_INFO"]
    method = environ["REQUEST_METHOD"]
    env = Environment(loader=FileSystemLoader(os.getcwd()))
    template = env.get_template("feedback.html")
    data=""
    if path == "/app":
        data = "Hello, Web!\n"
    if path == "/app/feedback":
        if method == "POST":
            form = cgi.FieldStorage(fp=environ["wsgi.input"], environ=environ)
            feedback = {
                "name": form.getvalue("name"),
                "email": form.getvalue("email"),
                "feedback": form.getvalue("feedback"),
            }
            if "@" in feedback["email"]:
                conn = psycopg2.connect(
                    host="db",
                    dbname= os.environ['DB_DATABSE'],
                    port=5432,
                    user=os.environ['DB_USER'],
                    password=os.environ['DB_PASSWORD']
                )
                cur = conn.cursor()
                sql = f"INSERT INTO feedback(nome, email, feedback) " \
                      f"VALUES ('{feedback['name']}', '{feedback['email']}', '{feedback['feedback']}')"
                cur.execute(sql)
                conn.commit()
                cur.close()
                conn.close()
                data = "Dados salvos com sucesso"
            else:
                feedback['error']='Email deve conter @'
                data = template.render(feedback)
        else:
            feedback = {
                "name": "",
                "email": "",
                "feedback": "",
                "error" : ""
            }
            data = template.render(feedback)

    start_response("200 OK", [
        ("Content-Type", "text/html"),
        ("Content-Length", str(len(data)))
    ])
    return [data.encode()]

if __name__ == '__main__':
    w_s = simple_server.make_server(
        host="",
        port=8000,
        app=app
    )
    w_s.serve_forever()