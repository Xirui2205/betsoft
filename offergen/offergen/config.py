import os

class Config:
    def __init__(self):
        self.debug = False
        self.iface = '127.0.0.1'
        self.port = 14445
        self.lang = 'cs'
        self.cc = 'cz'
        self.locale = 'cs_CZ.UTF-8'
        self.forms = None

        self.static = 'static'   # static data path
        self.output = 'output'   # output dir
        self.workdir = 'workdir' # work dir

        self.dbhost = None
        self.dbname = None
        self.dbuser = None
        self.dbpass = None
        self.dbminconn = 1
        
        self.admin_dbhost = None
        self.admin_dbname = None
        self.admin_dbuser = None
        self.admin_dbpass = None
        self.admin_dbminconn = 1
        
        self.db_document_table = 'offergen_tiskopis'
        self.db_document_type_table = 'offergen_typ_tiskopisu'



    def load(self, fname):
        execfile(fname, self.__dict__)
        self.static = self.abs_path(self.static)
        self.output = self.abs_path(self.output)
        self.workdir = self.abs_path(self.workdir)


    def abs_path(self, path):
        if not path.startswith('/'):
            path = os.getcwd() + '/' + path
        return path


cfg = Config()

