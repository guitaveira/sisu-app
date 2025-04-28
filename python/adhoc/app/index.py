import cgi
from wsgiref import simple_server

def app(environ, start_response):
    data=""
    if environ['PATH_INFO']== "/app":
        data = "Hello now Web!"
    if environ['PATH_INFO']== "/app/feedback":
        if environ['REQUEST_METHOD']== "POST":
            form = cgi.FieldStorage(fp=environ["wsgi.input"],environ=environ)
            data = form.getvalue('name') + " " + form.getvalue('email') + " " + form.getvalue('feedback')
    start_response ("200 OK",[
        ("Content-Type", "text/plain"),
        ("Content-Length",str(len(data)))
    ])
    return [data.encode('utf-8')]

if __name__ == '__main__':
    w_s = simple_server.make_server(
        host="",
        port=8000,
        app=app
    )
    w_s.serve_forever()