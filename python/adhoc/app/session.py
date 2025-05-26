import hashlib
import os

SESSIONS = {  }
def generate_session_id():
    return hashlib.sha256(os.urandom(64)).hexdigest()

def get_session(environ):
    cookies=environ.get('HTTP_COOKIE','')
    cookie_dict= dict(cookies.strip().split('=',1)
                      for cookie in cookies.split(';') if '=' in cookie )


    if 'session_id' in  cookie_dict:
        session= SESSIONS[cookie_dict['session_id']]
    else:
        session_id = generate_session_id()
        session ={}
        SESSIONS[session_id] = session

    return session_id, session