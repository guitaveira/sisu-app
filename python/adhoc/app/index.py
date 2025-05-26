import importlib
from wsgiref import simple_server
from urllib.parse import parse_qs
from orator import DatabaseManager, Model
import os

def app(environ, start_response):
    path = environ['PATH_INFO']
    param = parse_qs(environ['QUERY_STRING'])

    path_array = path.split('/')
    classname = path_array[2].capitalize() + 'Controller'

    module = importlib.import_module("controllers."+ classname)
    instance= getattr(module, classname)(environ)
    getattr(instance, path_array[3] or 'index')(*param.values())

    start_response(instance.status, [
        ("Content-Type", "text/html"),
        ("location", instance.redirect_url),
        ("Content-Length", str(len(instance.data)))
    ])
    return [instance.data.encode()]

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