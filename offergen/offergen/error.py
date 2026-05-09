class DbError(Exception):
    """general database error"""
    def __init__(self, msg, query=None):
        self.name = 'Database error'
        self.query = query
        Exception.__init__(self, msg)


class DbBadKeyError(Exception):
    """bad key in where clause or bad foreign key"""
    def __init__(self, msg):
        self.code = 400
        self.name = 'Bad request'
        Exception.__init__(self, msg)


class RequestError(Exception):
    """bad key in where clause or bad foreign key"""
    def __init__(self, msg):
        self.code = 400
        self.name = 'Bad request'
        self.msgcode = 4
        Exception.__init__(self, msg)


class HttpError(Exception):
    def __init__(self, code, name, msg):
        self.code = code
        self.name = name
        Exception.__init__(self, msg)


class NotFoundError(HttpError):
    def __init__(self, url):
        HttpError.__init__(self, 404, 'Not Found', 'Page not found: %r' % url)


class LatexError(Exception):
    def __init__(self, rc):
        self.msgcode = 11
        Exception.__init__(self, 'LaTeX returned error code %d.' % rc)


class DviPsError(LatexError):
    def __init__(self, rc):
        self.msgcode = 11
        Exception.__init__(self, 'dvips returned error code %d.' % rc)


class NoOutputError(Exception):
    def __init__(self):
        self.msgcode = 10
        Exception.__init__(self, 'No pages in output.')

class TerminatedError(Exception):
    pass

