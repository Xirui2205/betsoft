import time
from offergen.utils import reformatDate


class Option:
    def __init__(self, name):
        self.name = name

    def evaluate(self, req, rows):
        return str(req.__dict__[self.name.lower()])


class Column:
    def __init__(self, name, type='str'):
        self.name = name
        self.type = type

    def evaluate(self, req, rows):
        val = rows[0][self.name]
        if self.type == 'date':
            val = reformatDate(val)
        if val is None:
            val = ''
        return val


class Eval:
    def __init__(self, code):
        self.code = code

    def evaluate(self, req, rows):
        return str(eval(self.code))


class Join:
    def __init__(self, sep, fields):
        self.sep = sep
        self.fields = fields

    def evaluate(self, req, rows):
        arr = []
        for x in self.fields:
            val = x.evaluate(req,rows)
            if val:
                arr += [val]
        return self.sep.join(arr)


class Form:
    def __init__(self):
        self.name = None
        self.template_tex = None
        self.template_preview = None
        self.args = []
        self.query = None
        self.fields = {}


    def load(self, fname):
        self.__dict__['col'] = Column
        self.__dict__['eval'] = Eval
        self.__dict__['opt'] = Option
        self.__dict__['join'] = Join
        execfile(fname, self.__dict__)

