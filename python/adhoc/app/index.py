from wsgiref import simple_server

def app(environ, start_response):
    data = b"Hello now Web!\n"
    start_response ("200 OK",[
        ("Content-Type", "text/plain"),
        ("Content-Length",str(len(data)))
    ])
    return [data]

if __name__ == '__main__':
    w_s = simple_server.make_server(
        host="",
        port=8000,
        app=app
    )
    w_s.serve_forever()