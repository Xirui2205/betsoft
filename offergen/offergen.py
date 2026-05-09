#!/usr/bin/env python

from BaseHTTPServer import HTTPServer, BaseHTTPRequestHandler
from SocketServer import ThreadingMixIn
from cgi import parse_qs
import simplejson as json

import sys, os, os.path, time, threading, re
import logging
import locale
import contrib.template as template

from offergen.config import cfg
from offergen.processor import Processor
from offergen.strings import StringsLoader
from offergen.error import NotFoundError, NoOutputError, TerminatedError
from offergen import logs, signaltrap, utils


class MyHTTPServer(ThreadingMixIn, HTTPServer):
    pass


class MyHTTPRequestHandler(BaseHTTPRequestHandler):
    def do_GET(self):
        query = self.path.split('?')
        path = query[0]
        if len(query) < 2:
            args = {}
        else:
            args = parse_qs(query[1])
        try:
            self.handleRequest(path, args)
        except (NoOutputError, NotFoundError), e:
            self.sendError(e)
        except Exception, e:
            log.exception("handleRequest() error")
            self.sendError(e)


    def sendContent(self, data, ctype='text/html; charset=UTF-8', addheaders={}):
        self.send_response(200)
        self.send_header('Content-Type', ctype)
        self.send_header('Content-Length', len(data))
        for name in sorted(addheaders.keys()):
            self.send_header(name, addheaders[name])
        self.end_headers()
        self.wfile.write(data)

    def sendFile(self, fname, ctype='text/html; charset=UTF-8', addheaders={}, asAttachement=True):
        try:
            sz = os.path.getsize(fname)
            f = open(fname, 'rb', 4096)
            self.send_response(200)
            self.send_header('Content-Type', ctype)
            if asAttachement : self.send_header('Content-Disposition', 'attachment; filename=' + os.path.basename(fname))
            self.send_header('Content-Length', sz)
            for name in sorted(addheaders.keys()):
                self.send_header(name, addheaders[name])
            self.end_headers()
            while True:
                buff = f.read(4096)
                if 0 == len(buff): break
                self.wfile.write(buff)
            f.close()
            return True
        except os.error:
            return False
        except IOError:
            return False

    def sendError(self, exc):
        code = 500
        name = 'Chyba'
        query = None
        msgcode = 0
        if 'code' in exc.__dict__:  code = exc.code
        if 'name' in exc.__dict__:  name = exc.name
        if 'query' in exc.__dict__:  query = exc.query
        if 'msgcode' in exc.__dict__:  msgcode = exc.msgcode
        msg = type(exc).__name__ + ': ' + str(exc)
        data = {'code' : code, 'name' : name, 'message' : errstrings['err-%s' % msgcode], 'query' : query}
        out = templ_error.generate(cfg=cfg, **data)
        self.send_response(code, name)
        self.send_header('Content-Type', 'text/html; charset=UTF-8')
        self.send_header('Content-Length', len(out))
        self.send_header('X-Error-Message', msg)
        self.send_header('X-Localized-Message', utils.uncarkyhacky(errstrings['err-%s' % msgcode]))
        self.end_headers()
        self.wfile.write(out)


    def handleRequest(self, path, args):
        if path == '/':
            groups = proc.listGroups()
            sports, actions = proc.listActions()
            betTypes = proc.listBetTypes()
            out = templ_index.generate(cfg=cfg, time=time, forms=proc.forms, sports=sports, actions=actions, betTypes=betTypes)
            self.sendContent(out)
            return

        if path == '/favicon.ico' or path == '/static/favicon.ico':
            fname = cfg.static + '/favicon.ico'
            try:
                data = open(fname, 'r').read()
            except IOError:
                raise NotFoundError(self.path)
            self.sendContent(data, 'image/x-icon')
            return

        if path.startswith('/static/'):
            fname = cfg.static + path[7:]
            try:
                data = open(fname, 'r').read()
            except IOError:
                raise NotFoundError(self.path)
            if path.endswith('.js'):
                self.sendContent(data, 'application/javascript')
            else:
                self.sendContent(data, 'text/css')
            return

        if path == '/docrq':
            # load request
            req = proc.newRequest()
            req.load_qs(args)
            # process
            result = proc.processRequest(req, self.client_address)
            if 'exception' in result:
                raise result['exception']
            # result
            name, ext, pagecount = result['output']
            fname = str(name) + '.' + ext
            headers = {
                'X-Document-Id': str(name),
                'X-Document-URL': cfg.outputurl + '/' + fname,
                'X-Document-Pages': str(pagecount),
                'X-Preview-URL': '%s/%s.%s' % (cfg.outputurl, name, 'txt'),
            }
            out = templ_result.generate(cfg=cfg, result=result)
            self.sendContent(json.dumps({'name':fname, 'content':out}), addheaders=headers)
            return

        if path.startswith('/pdf/'):
            docId = path[5:]
            if re.match('^\d+$', docId) is not None:
                fname = cfg.output + '/' + docId + '.pdf'
                if self.sendFile(fname, 'application/pdf'):
                    return

        # if path.startswith('/txt/'):
        #     docId = path[5:]
        #     if re.match('^\d+$', docId) is not None:
        #         fname = cfg.output + '/' + docId + '.txt'
        #         if self.sendFile(fname, ctype='text/plain; charset=utf-8', asAttachement=False):
        #             return

        raise NotFoundError(self.path)


    def log_message(self, format, *args):
        aclog.info('%s ' + format, self.client_address[0], *args)


    def log_error(self, format, *args):
        aclog.error('%s ' + format, self.client_address[0], *args)




configname = 'offergen.conf'
if len(sys.argv) == 2:
    configname = sys.argv[1]

signaltrap.init()

cfg.load(configname)
cfg.load('VERSION')

locale.setlocale(locale.LC_ALL, cfg.locale)

# initialize logs
logs.setup(cfg.log_path, cfg.log_stdout)
log = logging.getLogger('main')
log.setLevel(logging.DEBUG)
aclog = logging.getLogger('activity')

stringsloader = StringsLoader('strings/offergen_strings_%s.xml' % cfg.lang)

proc = Processor(stringsloader)
proc.loadForms(cfg.forms)

templates = template.Loader("template")
templ_index = templates.load("index.html")
templ_result = templates.load("result.html")
templ_error = templates.load("error.html")

errstrings = stringsloader.getStrings('errors')

os.chdir(cfg.workdir)

server = MyHTTPServer((cfg.iface, cfg.port), MyHTTPRequestHandler)

try:
    server.serve_forever()
except (KeyboardInterrupt, TerminatedError):
    log.info('Signal caught')
    if threading.activeCount() > 1:
        log.info('Waiting for threads to finish')
        while threading.activeCount() > 1:
            time.sleep(0.2)
    log.info('Exiting')

logs.finish()

