# -*- coding: utf-8 -*-
import re
from lxml import etree

from error import RequestError
from config import cfg

class Request:
    def __init__(self):
        # relevantni parametry podle typu dokumentu
        self.compress_parameters = {
            'offer' : ('pageformat', 'datefilter', 'datefrom', 'dateto', 'sport', 'category', 'bettype'),
            'results' : ('pageformat', 'datefilter', 'datefrom', 'dateto', 'sport', 'bettype'),
            'aliases' : tuple(['pageformat']),
        }
        # vycty
        self.documenttype_enum = ['offer', 'results', 'aliases']
        self.pageformat_enum = ('portrait', 'landscape')
        self.sortby_enum = ('date', 'sport')
        self.gamefilter_enum = ('all', 'main')
        self.datefilter_enum = ('today', 'fromto', 'all')
        self.outputformat_enum = ('ps', 'pdf')
        self.outputtype_enum = ('link', 'attach')
        self.tickets_type_enum = ('cancelled', 'cashed')

        self.category = 1 # id from offer_category
        self.stationid = 0 # id_stanice
        # derived:
        self.stationmid = 0 # mid
        self.stationname = ''
        self.centreid = 0 # id_sberna

        self.userid = 0
        # derived:
        self.username = ''

        self.group = 3

        self.customer = 0

        self.documenttype = 'offer'

        # options
        self.pageformat = 'portrait' # portrait, landscape
        self.sortby = 'date' # date, sport
        self.gamefilter = 'all' # all, main
        self.datefilter = 'today' # today, fromto, all
        self.datefrom = None
        self.dateto = None
        self.timefrom = '10:00:00'
        self.sport = []
        self.action = []
        self.bettype = []

        # output
        self.outputformat = 'pdf'
        self.outputtype = 'link'
        self.margins = (20, 20, 20, 20)
        self.printer = 0  # id_szn_tiskarna

        # debug
        self.debug = [] # nocache, parameters, texsource, delays, xml


    def normalize(self, val):
        return val.lower()


    def load_qs(self, q):
        rexp = re.compile('(.+)\[(\d+)\]')
        for k in q:
            name = self.normalize(k)
            index = None
            match = rexp.match(name)
            if not match is None:
                name = match.group(1)
                index = int(match.group(2))
            vals = q[k]
            if name in self.__dict__:
                vartype = type(self.__dict__[name])
                value = None
                if vartype is list:
                    if name == 'sport' or name == 'action' or name == 'bettype':
                        #self.__dict__[name] = [int(x) for x in vals]
                        for val in vals : self.__dict__[name].append(val)
                    else:
                        self.__dict__[name].append( self.normalize(vals[0]) )
                    continue
                if vartype is int:
                    self.__dict__[name] = int(vals[0])
                    continue
                if vartype is float:
                    self.__dict__[name] = float(vals[0])
                    continue
                self.__dict__[name] = self.normalize(vals[0])
        self.adjust()


    def adjust(self):
        # today offer - override SortBy, set always to Date
        if self.datefilter == 'today':
            self.sortby = 'date'


    def check(self):
        result = []
        #
        result += [('StationId', self.stationid, True)]
        result += [('StationMid', self.stationmid, True)]
        result += [('StationName', self.stationname, True)]
        result += [('Group', self.group, True)]
        result += [('Customer', self.customer, True)]
        #
        result += [('DocumentType', self.documenttype, self.documenttype in self.documenttype_enum)]
        result += [('PageFormat', self.pageformat, self.pageformat in self.pageformat_enum)]
        result += [('SortBy', self.sortby, self.sortby in self.sortby_enum)]
        result += [('GameFilter', self.gamefilter, self.gamefilter in self.gamefilter_enum)]
        result += [('DateFilter', self.datefilter, self.datefilter in self.datefilter_enum)]
        result += [('DateFrom', self.datefrom, True)]
        result += [('DateTo', self.dateto, True)]
        result += [('TimeFrom', self.timefrom, True)]
        result += [('Sport', self.sport, True)]
        result += [('Action', self.action, True)]
        result += [('Category', self.category, True)]
        result += [('BetType', self.bettype, True)]
        #
        result += [('OutputFormat', self.outputformat, self.outputformat in self.outputformat_enum)]
        result += [('Printer', self.printer, True)]
        result += [('Margins', self.margins, True)]
        return result


    def compress(self):
        if not self.documenttype in self.compress_parameters:
            return None
        cf_digit = lambda x: str(x) + ':'
        cf_int = lambda x: x != 0 and str(x)+':' or ':'
        cf_date = lambda dt: dt.replace('-','').replace(':','')[2:] + ':'
        cf_time = lambda dt: dt.replace(':','') + ':'
        cf_firstchar = lambda x: x[0].upper() + ':'
        cf_array = lambda arr: ','.join([str(x) for x in sorted(arr)]) + ':'
        compressfunc = {
            'customer' : cf_int,
            'group' : cf_digit,
            'pageformat' : cf_firstchar,
            'sortby' : cf_firstchar,
            'gamefilter' : cf_firstchar,
            'datefilter' : cf_firstchar,
            'datefrom' : cf_date,
            'dateto' : cf_date,
            'timefrom' : cf_time,
            'sport' : cf_array,
            'action' : cf_array,
            'bettype' : cf_array,
            'reportid' : cf_int,
            'category' : cf_int
        }

        res = []
        for par in self.compress_parameters[self.documenttype]:
            # special conditions
            if self.documenttype != 'changes' and par in ('datefrom', 'dateto') and self.datefilter != 'fromto':
                continue
            # compress the parametr
            data = self.__dict__[par]
            if data is None:
                continue
            func = compressfunc[par]
            res += [func(data)]

        return ''.join(res)


    def getDocumentTypeId(self):
        if not self.documenttype in self.documenttype_enum:
            raise RequestError('Unknown document type: %s' % self.documenttype)
        # zakladni dokumenty - podle poradi v documenttype_enum
        idx = list(self.documenttype_enum).index(self.documenttype) + 1
        if idx <= 12:
            return idx
        # ulozeny report - vzdy 14
        if self.documenttype == 'storedreport':
            return 14
        # dalsi dokumenty vzdy 13
        return 13


    def getOutputFormatId(self):
        if not self.outputformat in self.outputformat_enum:
            raise RequestError('Unknown output format: %s' % self.outputformat)
        return list(self.outputformat_enum).index(self.outputformat) + 1


    def getTicketsStatus(self):
        if not self.tickets_type in self.tickets_type_enum:
            raise RequestError('Unknown tickets type: %s' % self.tickets_type)
        return list(self.tickets_type_enum).index(self.tickets_type) + 3

