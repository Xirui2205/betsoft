# -*- coding: utf-8 -*-

from escape_tex import escape_tex, escape_tex_dict

class FormatTex:
    def __init__(self):
        self.out = []


    # general

    def upperUtf8(self, s):
        return s.decode('utf-8').upper().encode('utf-8')

    def addClassHeader(self, **kw):
        """generate class header

        margins is dict with keys left, top, right, bottom
        """
        kw['_options'] = ''
        if 'twocolumn' in kw and kw['twocolumn']:
            kw['_options'] += 'twocolumn,'
        if 'landscape' in kw and kw['landscape']:
            kw['_options'] += 'landscape,'
        self.out += [
            r'\batchmode',
            r'\documentclass[%(_options)sleft=%(left)spt,top=%(top)spt,right=%(right)spt,bottom=%(bottom)spt]{%(cls)s}' % kw,
            r'\usepackage[utf8]{inputenc}',
            r'\usepackage[czech]{babel}']


    def addOfferTitle(self, title):
        title = escape_tex(title)
        self.out += [
            r'\settitle{%s}' % title,
            r'']


    def addBeginDocument(self, makeheader = False):
        self.out += [
            r'\begin{document}',
            r'']
        if makeheader:
            self.out += [r'\makeheader', '']


    def addEndDocument(self):
        self.out += [r'\end{document}', '']


    def addPart(self, noendl=False, **kw):
        self.out += [r'\part{%(title)s}' % kw]
        if not noendl:
            self.out += ['']


    def addBeginTab(self):
        self.out += [r'\tab']


    def addEndTab(self):
        self.out += [r'\endtab', '']


    def addBeginTables(self):
        self.out += [r'\tables']


    def addEndTables(self):
        self.out += [r'\endtables', '']


    # offers

    def addOfferBeginDocument(self, **kw):
        self.out += [
            r'\setfoot{%(print_s)s}{%(print_time)s}{%(page_s)s}' % kw,
            r'\begin{document}' % kw,
            r'\logo{logo/%(logo)s}' % kw]


    def addOfferType(self, **kw):
        self.out += [r'\topnadpis{%(name)s}' % kw]


    def addOfferDate(self, repeat=False, **kw):
        if repeat:
            kw['_rep'] = 'r'
        else:
            kw['_rep'] = ''
        self.out += [r'\datum{%(date)s}{%(_rep)s}' % kw]

    def addOfferSportTitle(self, title):
        title = escape_tex(title)
        self.out += [r'\tabtitlesport{%s}' % self.upperUtf8(title)]

    def addOfferEventTitle(self, title):
        title = escape_tex(title)
        self.out += [r'\tabtitleevent{%s}' % title]


    def addOfferTabTitle(self, title):
        title = escape_tex(title)
        self.out += [r'\tabtitle{%s}' % title]


    def addOfferNote(self, **kw):
        escape_tex_dict(kw)
        self.out += [r'\note{%(note)s}' % kw]


    def addOfferBeginTables(self, **kw):
        self.out += [r'\tabulky{%(title)s}' % kw]


    def addOfferEndTables(self):
        self.out += [r'\endtabulky']


    def addOfferBeginTab(self, main=False, **kw):
        self.out += [r'\tab{%(ncols)s}' % kw]


    def addOfferEndTab(self, main=False):
        if main:
            self.out += [r'\endtabmain']
        else:
            self.out += [r'\endtab']

    def addOfferTabHead(self, **kw):
        escape_tex_dict(kw)
        ln = [r'\headline{%(info)s}{%(betType)s}']
        for t in kw['tipy']:
            if t[0] == '^':
                ln += ['{', '~' * (int(t[1]) * 3), '}']
            else:
                ln += ['{', t, '}']
        #ln += ['{%(ako)s}']
        ln += ['{}{%(datum)s}']
        self.out += [''.join(ln) % kw]


    def addOfferTabLine(self, **kw):
        escape_tex_dict(kw)
       
        ln = [r'\tabline{%(info)s}{%(nazev)s}']

        for k in kw['kursy']:
            if k is None:
                ln += [r'{-\--}']
            else:
                ln += ['{', k, '}']

        #ln += ['{%(ako)s}{%(datum)s}']
        ln += ['{}{%(datum)s}']

        self.out += [(''.join(ln) % kw)]

    def addOfferTypeBTabLine(self, **kw):
        escape_tex_dict(kw)
        
        if 'landscape' in kw and kw['landscape']:
            ln = [r'\tab{10}']
        else:
            ln = [r'\tab{10}']

        if 'sportTitle' in kw and kw['sportTitle']:
            ln.append(r'\tabtitlesport{%(sportTitle)s}' % kw)

        if 'eventTitle' in kw and kw['eventTitle']:
            ln.append(r'\tabtitleevent{%(eventTitle)s}' % kw)

        if 'title' in kw and kw['title']:
            ln.append(r'\tabtitleandsubtitle{%(title)s}{%(info)s %(nazev)s %(datum)s}' % kw)
        else:
            ln.append(r'\tabsubtitle{%(info)s %(nazev)s %(datum)s}' % kw)
        ln.append(r'\headlineB')

        if 'landscape' in kw and kw['landscape']:
            SIZE = 6
        else:
            SIZE = 6

        for k in range(len(kw['kursy'])):
            if k % SIZE == 0:
                ln.append(r'\tablineb')
            if kw['kursy'][k] is None:
                ln.append('{' + kw['tipy'][k] + '}{-\--}')
            else:
                ln.append('{' + kw['tipy'][k] + '}{' + kw['kursy'][k] + '}')

        for k in range(SIZE-(len(kw['kursy'])%SIZE)):
            ln.append('{}{}')

        ln.append(r'\endtab')

        self.out += ln


    def addChangesTabLine(self, **kw):
        escape_tex_dict(kw)
        ln = [r'\tabline {%(info)s} {\%(_zpolozka)s{} %(nazev)s']
        if kw['handicap']:
            ln += [' | %(handicap)s']
        if kw['pozn']:
            ln += [r'\pozn{%(pozn)s}']
        ln += ['} ']

        if kw['zpolozka']:
            kw['_zpolozka'] = 'chngd'
        else:
            kw['_zpolozka'] = 'unchn'

        for k,zm in zip(kw['kursy'], kw['zkursy']):
            ln += ['{']
            if zm:
                ln += [r'\chngd{}']
            else:
                ln += [r'\unchn{}']
            if k is None:
                ln += [r'-\--} ']
            else:
                ln += [k, '} ']

        if kw['datum']:
            ln += ['{%(datum)s} ']

        if kw['cas']:
            ln += ['{%(cas)s} ']

        self.out += [''.join(ln) % kw]


    def addResultsTabLine(self, **kw):
        escape_tex_dict(kw)
        #if not kw['ako']:
            #kw['ako'] = '~'
        #ln = [r'\tabline{%(info)s}{%(nazev)s}']
        #ln += ['{%(score)s}{}{%(datum)s}']

        self.out += [r'\tabline{%(info)s}{%(nazev)s}{%(score)s}{}{%(datum)s}' % kw]

        #escape_tex_dict(kw)
        #ln = [r'\tabline {%(info)s} {']
        #if 'strong' in kw and kw['strong']:
            #ln += ['\strong ']
        #ln += [kw['nazev']]
        #if kw['handicap']:
            #ln += [' | %(handicap)s']
        #if kw['pozn']:
            #ln += [r'\pozn{%(pozn)s}']
        #ln += ['} {%(vysledky)s} ']
        #if not kw['vyherni']:
            #ln += ['{-} ']
        #else:
            #ln += ['{(%(vyherni)s)} ']
        #if kw['datum']:
            #ln += ['{%(datum)s} ']
        #if kw['cas']:
            #ln += ['{%(cas)s} ']
        #self.out += [''.join(ln) % kw]
        
    def addResultsTabHead(self, **kw):
        escape_tex_dict(kw)
        ln = [r'\headline{%(info)s}{%(nazev)s}']
        ln += ['{%(score)s}{}']
        ln += ['{%(datum)s}']
        self.out += [''.join(ln) % kw]

    # aliases

    def addAliasesTabHead(self, **kw):
        #escape_tex_dict(kw)
        #ln = [r'\headline{%(alias)s}{%(nazev)s}']
        #self.out += [''.join(ln) % kw]
        self.out += [r'\headline']

    def addAliasesTabLine(self, **kw):
        escape_tex_dict(kw)
        ln = [r'\tabline{%(alias)s}{%(nazev)s}']
        self.out += [(''.join(ln) % kw)]

    # lottery

    def addLotteryNumbers(self, **kw):
        escape_tex_dict(kw)
        ln = [r'\numbers{%(title)s}{%(time)s} ' % kw]
        for num in kw['numbers']:
            ln += [str(num), ' ']
        ln += [r'\endnumbers']
        self.out += [''.join(ln)]


    def addLotteryBeginDay(self, **kw):
        self.out += [r'\setdate{%(date)s}' % kw]
        self.out += [r'\makeheader', '']


    def addLotteryEndDay(self):
        self.out += ['', r'\break']


    def addLotteryNotDrawnTab(self, **kw):
        self.out += [r'\tab{%(title)s}{%(date)s}' % kw]
        self.out += [r'\headline{číslo}{počet}']

    def addLotteryNotDrawnNumber(self, **kw):
        self.out += [r'\tabline{%(number)s}{%(count)s}' % kw]


    # cashdesk

    def addCashdeskHeadline(self, col1, col2, col3, col4, col5):
        ln = [r'\headline{']
        if col1:
            ln += [escape_tex(col1)]
        ln += ['}{']
        if col2:
            ln += [col2]
        ln += ['}{']
        if col3:
            ln += [col3]
        ln += ['}{']
        if col4:
            ln += [col4]
        ln += ['}{']
        if col5:
            ln += [col5]
        ln += ['}']
        self.out += [''.join(ln)]


    def addCashdeskTablineBalance(self, name, value):
        self.out += [r'\tabline{%s}{}{}{}{%.2f}' % (escape_tex(name), value)]


    def addCashdeskTablineValue(self, name, count, value):
        self.out += [r'\tabline{%s}{%d}{%.2f}{}{}' % (escape_tex(name), count, value)]


    def addCashdeskTablineIn(self, name, count, value):
        self.out += [r'\tabline{%s}{%d}{%.2f}{---}{}' % (escape_tex(name), count, value)]


    def addCashdeskTablineOut(self, name, count, value):
        name = escape_tex(name)
        if name.startswith('   '):
            name = '~~~~~' + name
        self.out += [r'\tabline{%s}{%d}{---}{%.2f}{}' % (name, count, value)]


    def addCashdeskTablineGoods(self, name, count, value, value_pt, count_pt):
        self.out += [r'\tabline{%s}{%d}{%.2f}{%d}{%d}' % (escape_tex(name), count, value, value_pt, count_pt)]


    def addCashdeskWarn(self, msg):
        self.out += [r'\warnline{%s}' % escape_tex(msg)]


    def addCashdeskEmlineInout(self, name, valin, valout):
        if valout and valout >= 0.0:
            self.out += [r'\emline{%s}{}{%.2f}{%.2f}{}' % (escape_tex(name), valin, valout)]
        else:
            self.out += [r'\emline{%s}{}{%.2f}{}{}' % (escape_tex(name), valin)]


    def addCashdeskEmlineGoods(self, name, count, value, value_pt, count_pt):
        self.out += [r'\emline{%s}{%d}{%.2f}{%d}{%d}' % (escape_tex(name), count, value, value_pt, count_pt)]


    # tickets

    def addTicketsHeadline(self, col1, col2, col3):
        self.out += [r'\headline{%s}{%s}{%s}' % (escape_tex(col1), col2, col3)]


    def addTicketsTabline(self, number, price, winning):
        self.out += [r'\tabline{%s}{%.2f}{%.2f}' % (number, price, winning)]


    def addTicketsEmline(self, number, price, winning):
        self.out += [r'\emline{%s}{%.2f}{%.2f}' % (number, price, winning)]


    # sales

    def addSalesHeadline(self, col1, col2, col3):
        self.out += [r'\headline{%s}{}{%s}{%s}' % (escape_tex(col1), col2, col3)]


    def addSalesTabline(self, num, rcpt, value, cancel):
        line = [r'\tabline{']
        if num:
            line += [str(num)]
        line += ['}{']
        if rcpt:
            line += [rcpt]
        line += ['}{']
        if not value is None:
            if type(value) is float:
                line += ['%.2f' % value]
            else:
                line += [str(value)]
        line += ['}{']
        if not cancel is None:
            if type(cancel) is float:
                line += ['%.2f' % cancel]
            else:
                line += [str(cancel)]
        line += ['}']
        self.out += [''.join(line)]


    def addSalesEmline(self, name, value):
        self.out += [r'\emline{}{%s}{%.2f}{}' % (escape_tex(name), value)]


    # moneylist

    def addMoneyListHeadline(self, col1, col2, col3):
        self.out += [r'\headline{%s}{%s}{%s}' % (escape_tex(col1), col2, col3)]


    def addMoneyListTabline(self, nval, count, amount):
        if nval < 1.0:
            self.out += [r'\tabline{%.2f}{%d}{%.2f}' % (nval, count, amount)]
        else:
            self.out += [r'\tabline{%d}{%d}{%.2f}' % (int(nval), count, amount)]


    def addMoneyListEmline(self, title, amount):
        self.out += [r'\emline{%s}{}{%.2f}' % (title, amount)]


    # fortunky

    def addFortunkyHeadline(self, **kw):
        self.out += [r'\headline{%(date_s)s}{%(bets_s)s}{%(paid_s)s}'
            '{%(winning_s)s}{%(fees_s)s}{%(profit_s)s}{%(rent_s)s}' % kw]


    def addFortunkyTabline(self, **kw):
        self.out += [r'\tabline{%(year)d-%(month).2d}{%(bets).0f}{%(paid).0f}'
            '{%(winning).0f}{%(fees).0f}{%(profit).0f}{%(rent).0f}' % kw]


    # championship

    def addChartsHeader(self, **kw):
        escape_tex_dict(kw)
        self.out += [r'\newchart{%(size)s}' % kw]
        self.out += [r'\header%(type)s{%(etapa)s}{%(begin)s -- %(end)s}' % kw]


    def addChartsTabline(self, **kw):
        escape_tex_dict(kw)
        self.out += [r'\tabline{%(number)s.}{%(points)s}{%(name)s}' % kw]


    def addChartsTablineTotal(self, **kw):
        escape_tex_dict(kw)
        self.out += [r'\tablinetotal{%(number)s.}{%(points)s}{%(name)s}{%(branchname)s}' % kw]


    def addChartsSmaller(self):
        self.out += [r'\smaller']


    def addChartsEnd(self):
        self.out += [r'\endcharts']


