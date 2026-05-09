from threading import Thread, Event
from time import time
import sys, os, logging
#import hotshot, hotshot.stats

from format.format_tex import FormatTex
from format.format_txt import FormatTxt

from report import report_generators

from error import LatexError, DviPsError, NoOutputError

from offergen.config import cfg


class Worker(Thread):
    def __init__(self, req, docid, db, strings):
        Thread.__init__(self)
        self.req = req
        self.docid = docid
        self.db = db
        self.strings = strings

        self.prevt = time()
        self.tlist = []

        self.result = None

        self.log = logging.getLogger('main')


    def tpoint(self, name):
        t = time()
        tlen = t - self.prevt
        self.prevt = t
        self.tlist.append( (name, tlen) )

    def run(self):
        #prof = hotshot.Profile("/tmp/offergen.prof")
        #prof.start()
        try:
            result = {}
            req = self.req
            docid = self.docid

            # query database
            rows = self.db.queryDocument(req, tpoint=self.tpoint)
            if len(rows) == 0 and req.documenttype in ('betsoffer', 'changes', 'results', 'aliases'):
                raise NoOutputError()

            # TXT + TeX
            if req.documenttype == 'storedreport':
                source = rows[0]['source']
            else:
                gen = report_generators[req.documenttype]

                # txt preview
                # fmt = FormatTxt()
                # gen.format(req, rows, fmt, self.strings)
                # self.tpoint('txt format')
                # f = open('%s/%s.txt' % (cfg.output, docid), 'w')
                # data = []
                # for line in fmt.out:
                #     try:
                #         data.append(line.encode('utf-8'))
                #     except UnicodeEncodeError:
                #         self.log.error('Cannot encode line to utf-8: %r', line)
                #         data.append(line)
                # f.write('\n'.join(data))
                # f.close()
                # self.tpoint('txt write')

                # LaTeX
                fmt = FormatTex()
                gen.format(req, rows, fmt, self.strings)
                self.tpoint('tex format')

                #for ln in fmt.out:
                #    if type(ln) is unicode: print repr(ln)
                #    else: print ln
                source = '\n'.join(fmt.out)
                if type(source) is unicode:
                    source = source.encode('utf-8')

            if 'texsource' in req.debug:
                result['texsource'] = source

            # PS / PDF
            fname = self.make(req.outputformat, str(docid), source)
            self.tpoint(req.outputformat + ' latex')
            if fname is None:
                raise NoOutputError()
            pcount = self.countPages(fname)
            self.tpoint(req.outputformat + ' count pages')
            # move output
            texfname = str(docid) + '.tex'
            os.rename(texfname, cfg.output + '/' + texfname)
            os.rename(fname, cfg.output + '/' + fname)
            # result
            result['output'] = (docid, req.outputformat, pcount)

            # update cache state
            self.db.setDocumentReady(docid, pcount)

            if 'delays' in req.debug:
                result['delays'] = self.tlist

            self.result = result

            self.log.info('Worker done: %d', docid)
            #prof.close()
            #stats = hotshot.stats.load("/tmp/offergen.prof")
            #stats.strip_dirs()
            #stats.sort_stats('time', 'calls')
            #stats.print_stats(30)
        except NoOutputError, exc:
            self.log.info('Worker done with no output: %d', docid)
            self.db.setDocumentReady(docid, 0)
            self.result = {'exception' : exc}
        except Exception, exc:
            self.log.exception("Worker exception (%d)", docid)
            self.db.setDocumentReady(docid, -1)
            self.result = {'exception' : exc}


    def make(self, format, fname, doc):
        # save doc to .tex file
        f = open(fname + '.tex', 'w')
        f.write(doc)
        f.close()
        # make PS/PDF
        if format == 'ps':
            result = self.makePS(fname, doc)
        else:
            result = self.makePDF(fname, doc)
        # check output file
        try:
            os.stat(result)
        except OSError:
            # no output
            return None
        return result


    def makePS(self, fname, doc):
        # latex - make dvi
        cmd = "TEXINPUTS='./cls:' latex %s.tex </dev/null >/dev/null" % fname
        rc = os.system(cmd)
        if rc != 0:
            raise LatexError(rc)
        # second latex run
        os.system(cmd)
        # dvips
        cmd = "dvips %s.dvi -o %s.ps 2>/dev/null" % (fname, fname)
        rc = os.system(cmd)
        if rc != 0:
            raise DviPsError(rc)
        # clean up
        for ext in ['aux', 'log', 'dvi']:
            os.unlink(fname + '.' + ext)
        # return
        return fname + '.ps'


    def makePDF(self, fname, doc):
        # latex - make pdf
        cmd = "TEXINPUTS='./cls:' pdflatex %s.tex </dev/null >/dev/null" % fname
        rc = os.system(cmd)
        #if rc != 0:
        #    raise LatexError(rc)
        # second latex run
        os.system(cmd)
        # clean up
        for ext in ['aux', 'log']:
            os.unlink(fname + '.' + ext)
        # return
        return fname + '.pdf'


    def countPages(self, fname):
        if fname.endswith('.ps'):
            return self.countPagesPS(fname)
        else:
            return self.countPagesPDF(fname)


    def countPagesPS(self, fname):
        cmd = "yes 2>/dev/null| gs -q -dBATCH -sDEVICE=nullpage %s | grep -c showpage" % fname
        try:
            count = int(os.popen(cmd, 'r').read())
        except ValueError:
            count = 0
        return count


    def countPagesPDF(self, fname):
        cmd = "pdfinfo %s 2>/dev/null | sed -n -r 's/Pages: *//p'" % fname
        try:
            count = int(os.popen(cmd, 'r').read())
        except ValueError:
            count = 0
        return count

