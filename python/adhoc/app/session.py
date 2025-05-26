import hashlib
import os
from http import cookies

SESSIONS = {  }
def generate_session_id():
    return hashlib.sha256(os.urandom(64)).hexdigest()

def get_session(environ):
    cookie_dict=cookies.SimpleCookie(environ.get('HTTP_COOKIE',''))
    if 'session_id' in  cookie_dict and cookie_dict['session_id'].value in  SESSIONS:
        session_id = cookie_dict['session_id'].value
    else:
        session_id = generate_session_id()
        SESSIONS[session_id] = {}

    return session_id, SESSIONS[session_id]