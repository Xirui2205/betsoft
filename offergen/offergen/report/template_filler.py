# -*- coding: utf-8 -*-

import re

from offergen.format.format_txt import FormatTxt
from offergen.format.format_tex import FormatTex
from offergen.format.escape_tex import escape_tex
from offergen.form import Option, Column, Eval
from offergen.config import cfg

class TemplateFiller:
    def __init__(self, form):
        self.template_tex = None
        self.template_txt = None
        self.fields = form.fields
        if form.template_tex:
            self.template_tex = open(cfg.forms + '/' + form.template_tex).readlines()
        if form.template_txt:
            self.template_txt = open(cfg.forms + '/' + form.template_txt).readlines()


    def format(self, req, rows, fmt, tr):
        if fmt.__class__ is FormatTxt:
            fmt.out += self.fillin(self.template_txt, req, rows)
        if fmt.__class__ is FormatTex:
            fmt.out += self.fillin(self.template_tex, req, rows, escape=escape_tex)


    def fillin(self, template, req, rows, escape=lambda x:x):
        def repl(m):
            name = m.group(1)
            if name in self.fields:
                val = self.fields[name]
                if type(val) != str:
                    val = val.evaluate(req, rows)
                return escape(val)
            return escape(m.group(0))

        out = []
        for ln in template:
            ln = re.sub(r'\*\*([A-Za-z]+)\*\*', repl, ln.rstrip('\n'))
            out += [unicode(ln, 'utf-8')]

        return out

