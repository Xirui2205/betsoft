# -*- coding: utf-8 -*-

from datetime import datetime

class FormatTxt:
    def __init__(self, linewidth = 96):
        self.lw = linewidth
        self.colw = 12
        self.out = []
        self.title = None
        self.date = None
        self.station = None
        self.author = None
        self.printed = None


    def dict_to_unicode(self, d):
        for key in d:
            if type(d[key]) == str:
                d[key] = unicode(d[key], 'utf-8')


    def to_unicode(self, text):
        if not type(text) is unicode:
            return unicode(text, 'utf-8')
        return text


    def left(self, text, padchar = ' '):
        text = self.to_unicode(text)
        if padchar == ' ':
            return text
        rest = self.lw - len(text) - 1
        return u' '.join([text, unicode(padchar) * rest])


    def right(self, text, padchar = ' '):
        text = self.to_unicode(text)
        rest = self.lw - len(text) - 1
        return u' '.join([unicode(padchar) * rest, text])


    def center(self, text, padchar = ' '):
        padchar = unicode(padchar)
        text = self.to_unicode(text)
        rest = self.lw - len(text) - 2
        l = rest / 2
        r = rest - l
        ln = [padchar * l, text]
        if padchar != u' ':
            ln.append(padchar * r)
        return u' '.join(ln)


    def leftright(self, left, right, padchar = ' '):
        left = self.to_unicode(left)
        right = self.to_unicode(right)
        rest = self.lw - len(left) - len(right) - 2
        if rest < 0:
            left = left[:rest]
        return ' '.join([left, unicode(padchar) * rest, right])


    def line(self, char = ' '):
        if char == ' ':
            return u''
        return unicode(char) * self.lw


    # str.rjust for utf-8
    def rjust(self, text, w):
        text = unicode(text, 'utf-8')
        return text.rjust(w).encode('utf-8')


    # str.center for utf-8
    def ucenter(self, text, w):
        text = unicode(text, 'utf-8')
        return text.center(w).encode('utf-8')


    def split(self, line, maxlen):
        l = 0
        ln = []
        lines = []
        for x in line.split(' '):
            l += len(x) + 1
            if l > maxlen:
                lines += [ln]
                ln = []
                l = len(x) + 1
            ln += [x]
        lines += [ln]
        return [' '.join(ln) for ln in lines]


    def untex(self, s):
        s = s.replace('~~~', ' ')
        s = s.replace('--', '-')
        return s


    # general

    def addClassHeader(self, **kw):
        pass


    def addClassVars(self, **kw):
        self.title = kw['title']
        if kw['date_to']:
            self.date = '%(date_from)s - %(date_to)s' % kw
        else:
            self.date = kw['date_from']
        self.station = '%(station_id)s  %(station_name)s' % kw
        self.author = '%(make_user_s)s  %(user)s, %(make_date)s' % kw
        kw['_print'] = datetime.now().strftime('%x %H:%M')
        self.printed = '%(print_s)s %(_print)s' % kw


    def addClassVarsShort(self, **kw):
        self.title = kw['title']
        kw['_print'] = datetime.now().strftime('%x %H:%M')
        self.printed = '%(print_s)s %(_print)s' % kw
        if kw['date']:
            self.date = kw['date']


    def addOfferTitle(self, title):
        pass


    def addBeginDocument(self, makeheader = False):
        if self.title:
            self.out += [self.center(self.title)]
            self.out += [self.line('-')]
        if self.date:
            self.out += [self.center(self.date)]
        if self.station and self.author:
            self.out += [self.leftright(self.station, self.author)]
        self.out += [self.line()]


    def addEndDocument(self):
        pass


    def addPart(self, noendl=False, **kw):
        self.out += [self.line(), self.left(kw['title']), self.line()]


    def addBeginTab(self):
        pass


    def addEndTab(self):
        self.out += [self.line()]


    def addBeginTables(self):
        pass


    def addEndTables(self):
        self.out += [self.line()]


    def addOfferBeginDocument(self, **kw):
        self.printed = '%(print_s)s %(print_time)s' % kw


    def addOfferType(self, **kw):
        self.out += [self.center(kw['name'])]


    def addOfferDate(self, **kw):
        self.out += [self.center(kw['date'], '#')]


    def addOfferSportTitle(self, title):
        self.out += [self.center('*** ' + title + ' ***')]

    def addOfferEventTitle(self, title):
        self.out += [self.center('* ' + title + ' *')]

    def addOfferTabTitle(self, title):
        self.out += [self.center(title)]


    def addOfferNote(self, **kw):
        self.out += [self.left(kw['note'])]


    def addOfferBeginTables(self, **kw):
        self.out += [self.center(kw['title'], '-')]


    def addOfferEndTables(self):
        self.out += [self.line()]


    def addOfferBeginTab(self, **kw):
        self.out += [self.line()]


    def addOfferEndTab(self, main=False):
        pass


    def addOfferTabHead(self, **kw):
        if len(kw['tipy']) > 6:
            colw = 6
        else:
            colw = 8

        left = [str(kw['info']).ljust(9), kw['nazev']]
        right = []
        for t in kw['tipy']:
            if t[0] == '^':
                right += [' ' * colw]
            else:
                right += [str(t).center(colw)]

        right += [self.ucenter(kw['ako'], 3)]
        right += [self.ucenter(kw['datum'], 12)]

        self.out += [self.leftright(''.join(left), ''.join(right))]


    def addOfferTabLine(self, **kw):
        if len(kw['kursy']) > 6:
            colw = 6
        else:
            colw = 8

        left = [str(kw['info']).ljust(9), kw['nazev']]

        right = []
        for k in kw['kursy']:
            if k is None:
                right += ['--'.center(colw)]
            else:
                right += [str(k).center(colw)]

        right += [kw['ako'].rjust(3)]
        right += [kw['datum'].rjust(12)]

        self.out += [self.leftright(''.join(left), ''.join(right))]

    def addOfferTypeBTabLine(self, **kw):
        
        
        ln = []
        if 'title' in kw and kw['title']:
            ln.append(self.line())
            ln.append(self.center("%(title)s" %kw))
            ln.append(self.left("%(info)s %(nazev)s %(datum)s %(ako)s" % kw))
        else:
            ln.append(self.left("%(info)s %(nazev)s %(datum)s %(ako)s" % kw))

        SIZE = 6

        first = True
        for k in range(len(kw['kursy'])):
            if k % SIZE == 0:
                if not first:
                    ln.append(self.left(tmp))
                    tmp = ''
                else:
                    first = False
                    tmp = ''
            if kw['kursy'][k] is None:
                tmp += str(kw['tipy'][k]).center(8) + ' ' + ' '.center(7)
            else:
                tmp += str(kw['tipy'][k]).center(8) + ' ' + str(kw['kursy'][k]).center(7)

        ln.append(self.line())
        self.out += ln;

    def addChangesTabLine(self, **kw):
        if len(kw['kursy']) > 6:
            colw = 6
        else:
            colw = 8

        left = [str(kw['info']).ljust(9)]
        if kw['zpolozka']:
            left += ['!']
        left += [kw['nazev']]
        if kw['handicap']:
            left += ['|%(handicap)s' % kw]

        right = []
        for k,zm in zip(kw['kursy'], kw['zkursy']):
            if k is None:
                kurs = '--'
            else:
                kurs = str(k)
            if zm and k != '':
                kurs = '!' + kurs
            right += [kurs.center(colw)]
        if kw['datum']:
            right += [kw['datum'].rjust(6)]
        if kw['cas']:
            right += [kw['cas'].rjust(8)]

        self.out += [self.leftright(''.join(left), ''.join(right))]

        if kw['pozn']:
            self.out += [self.left('      (%(pozn)s)' % kw)]


    def addResultsTabLine(self, **kw):
        left = [str(kw['info']).ljust(9), kw['nazev']]

        right = []

        #right += [kw['ako'].rjust(3)]
        right += [kw['score'].rjust(12)]
        right += [kw['datum'].rjust(12)]

        self.out += [self.leftright(''.join(left), ''.join(right))]
        #left = [str(kw['info']).ljust(9), kw['nazev']]
        #if kw['handicap']:
            #left += ['|%(handicap)s' % kw]

        #right = [kw['vysledky'].center(10)]
        #vyherni_rest = None
        #if not kw['vyherni']:
            #right += ['-'.center(30)]
        #else:
            #if len(kw['vyherni']) > 30:
                #lines = self.split(kw['vyherni'], 30)
                #vyherni = lines[0]
                #vyherni_rest = lines[1:]
            #else:
                #vyherni = kw['vyherni']
            #right += [vyherni.center(30)]

        #if kw['datum']:
            #right += [kw['datum'].rjust(6)]
        #if kw['cas']:
            #right += [kw['cas'].rjust(8)]

        #self.out += [self.leftright(''.join(left), ''.join(right))]

        #out2 = []
        #if kw['pozn']:
            #out2 += ['      (%(pozn)s)' % kw]
        #if vyherni_rest:
            #if len(out2) == 0:
                #out2 += ['']
            #pos = self.lw - 8 - (kw['datum'] and 6 or 0) - 30
            #out2[0] += (pos - len(out2[0])) * ' '
            #out2[0] += vyherni_rest[0].center(30)
            #for vyherni in vyherni_rest[1:]:
                #out2 += [pos * ' ' + vyherni.center(30)]
        #self.out += [self.left(o) for o in out2]
        
    def addResultsTabHead(self, **kw):

        left = [str(kw['info']).ljust(9), kw['nazev']]
        right = []

        right += [self.ucenter(kw['score'], 5)]
        right += [self.ucenter(kw['datum'], 12)]

        self.out += [self.leftright(''.join(left), ''.join(right))]

    # aliases

    def addAliasesTabHead(self, **kw):
        #left = [str(kw['alias']).ljust(3), kw['nazev']]
        #right = []
        #self.out += [self.leftright(''.join(left), ''.join(right))]
        pass

    def addAliasesTabLine(self, **kw):
        left = [str(kw['alias']).ljust(3), kw['nazev']]
        right = []
        self.out += [self.leftright(''.join(left), ''.join(right))]

    # lottery

    def addLotteryNumbers(self, **kw):
        self.out += [self.left('%(title)s - %(time)s' % kw)]
        ln = ['    ']
        for num in kw['numbers']:
            ln += [str(num), ' ']
        self.out += [self.left(''.join(ln)), self.line()]


    def addLotteryBeginDay(self, **kw):
        self.out += [self.line('-')]
        self.out += [self.center(kw['date'])]
        self.out += [self.line('-')]


    def addLotteryEndDay(self):
        self.out += [self.line()]


    def addLotteryNotDrawnTab(self, **kw):
        self.out += [self.left('%(title)s' % kw)]
        self.out += [self.left('%(date)s' % kw)]
        self.out += [self.left('číslo  počet')]

    def addLotteryNotDrawnNumber(self, **kw):
        self.out += [self.left('%(number)2s     %(count)2s' % kw)]


    # cashdesk

    def addCashdeskHeadline(self, col1, col2, col3, col4, col5):
        colw = self.colw

        if not col1: col1 = ' '
        if not col2: col2 = ' '
        if not col3: col3 = ' '
        if not col4: col4 = ' '
        if not col5: col5 = ' '

        left = self.untex(col1)
        right = [self.rjust(col2, colw), self.rjust(col3, colw),
            self.rjust(col4, colw), self.rjust(col5, colw)]
        self.out += [self.leftright(left, ''.join(right))]
        self.out += [self.line('-')]


    def addCashdeskTablineBalance(self, name, value):
        left = self.untex(name)
        right = ('%.2f' % value).rjust(self.colw)
        self.out += [self.leftright(left, right)]


    def addCashdeskTablineValue(self, name, count, value):
        colw = self.colw
        left = self.untex(name)
        right = [
            str(count).rjust(colw),
            ('%.2f' % value).rjust(colw),
            (2*colw) * ' ']
        self.out += [self.leftright(left, ''.join(right))]


    def addCashdeskTablineIn(self, name, count, value):
        colw = self.colw
        left = self.untex(name)
        right = [
            str(count).rjust(colw),
            ('%.2f' % value).rjust(colw),
            '---'.rjust(colw),
            colw * ' ']
        self.out += [self.leftright(left, ''.join(right))]


    def addCashdeskTablineOut(self, name, count, value):
        colw = self.colw
        left = self.untex(name)
        right = [
            str(count).rjust(colw),
            '---'.rjust(colw),
            ('%.2f' % value).rjust(colw),
            colw * ' ']
        self.out += [self.leftright(left, ''.join(right))]


    def addCashdeskTablineGoods(self, name, count, value, value_pt, count_pt):
        colw = self.colw
        left = self.untex(name)
        right = [
            str(count).rjust(colw),
            ('%.2f' % value).rjust(colw),
            str(value_pt).rjust(colw),
            str(count_pt).rjust(colw)]
        self.out += [self.leftright(left, ''.join(right))]


    def addCashdeskWarn(self, msg):
        self.out += [self.left('  ### %s ###' % self.untex(msg))]


    def addCashdeskEmlineInout(self, name, valin, valout):
        colw = self.colw
        left = self.untex(name)
        right = [
            ' ' * colw,
            ('%.2f' % valin).rjust(colw)]
        if valout and valout >= 0.0:
            right += [('%.2f' % valout).rjust(colw)]
        else:
            right += [' ' * colw]
        right += [' ' * colw]
        self.out += [self.line('-')]
        self.out += [self.leftright(left, ''.join(right))]
        self.out += [self.line('-')]


    def addCashdeskEmlineGoods(self, name, count, value, value_pt, count_pt):
        colw = self.colw
        left = self.untex(name)
        right = [
            str(count).rjust(colw),
            ('%.2f' % value).rjust(colw),
            str(value_pt).rjust(colw),
            str(count_pt).rjust(colw)]
        self.out += [self.line('-')]
        self.out += [self.leftright(left, ''.join(right))]
        self.out += [self.line('-')]


    # tickets

    def addTicketsHeadline(self, col1, col2, col3):
        colw2 = self.colw * 2
        left = self.untex(col1)
        right = [
            self.rjust(col2, 2*colw2),
            self.rjust(col3, colw2)]
        self.out += [self.leftright(left, ''.join(right))]
        self.out += [self.line('-')]


    def addTicketsTabline(self, number, price, winning):
        colw2 = self.colw * 2
        left = self.untex(number)
        right = [
            ('%.2f' % price).rjust(colw2),
            ('%.2f' % winning).rjust(colw2)]
        self.out += [self.leftright(left, ''.join(right))]


    def addTicketsEmline(self, number, price, winning):
        colw2 = 2 * self.colw
        left = self.untex(number)
        right = [
            ('%.2f' % price).rjust(colw2),
            ('%.2f' % winning).rjust(colw2)]
        self.out += [self.line('-')]
        self.out += [self.leftright(left, ''.join(right))]
        self.out += [self.line('-')]


    # sales

    def addSalesHeadline(self, col1, col2, col3):
        colw2 = self.colw * 2
        left = self.untex(col1)
        right = [
            self.rjust(col2, 2*colw2),
            self.rjust(col3, colw2)]
        self.out += [self.leftright(left, ''.join(right))]
        self.out += [self.line('-')]


    def addSalesTabline(self, num, rcpt, value, cancel):
        colw2 = self.colw * 2
        left = []
        if num:
            left += [self.untex(str(num)).ljust(self.colw)]
        else:
            left += [' ' * self.colw]
        if rcpt:
            left += [rcpt]

        right = []

        if not value is None:
            if type(value) is float:
                val = '%.2f' % value
            else:
                val = str(value)
            right += [val.rjust(colw2)]
        else:
            right += [' ' * colw2]

        if not cancel is None:
            if type(cancel) is float:
                val = '%.2f' % cancel
            else:
                val = str(cancel)
            right += [val.rjust(colw2)]
        else:
            right += [' ' * colw2]

        self.out += [self.leftright(''.join(left), ''.join(right))]


    def addSalesEmline(self, name, value):
        colw2 = 2 * self.colw
        left = [self.colw * ' ', self.untex(name)]
        right = [
            ('%.2f' % value).rjust(colw2),
            ' ' * colw2]
        self.out += [self.line('-')]
        self.out += [self.leftright(''.join(left), ''.join(right))]
        self.out += [self.line('-')]


    # moneylist

    def addMoneyListHeadline(self, col1, col2, col3):
        colw2 = self.colw * 2
        left = self.untex(col1)
        right = [
            self.rjust(col2, 2*colw2),
            self.rjust(col3, colw2)]
        self.out += [self.leftright(left, ''.join(right))]
        self.out += [self.line('-')]


    def addMoneyListTabline(self, nval, count, amount):
        colw2 = self.colw * 2
        line = []
        if nval < 1.0:
            line += ['%.2f' % nval]
        else:
            line += ['%d' % nval]
        line += [str(count).rjust(colw2)]
        line += [('%.2f' % amount).rjust(colw2)]
        self.out += [self.right(''.join(line))]


    def addMoneyListEmline(self, title, amount):
        left = self.untex(title)
        right = '%.2f' % amount
        self.out += [self.line('-')]
        self.out += [self.leftright(left, right)]


    # fortunky

    def addFortunkyHeadline(self, **kw):
        self.dict_to_unicode(kw)
        print repr(kw['winning_s'].rjust(self.colw))
        left = kw['date_s']
        right = [
            kw['bets_s'].rjust(self.colw),
            kw['paid_s'].rjust(self.colw),
            kw['winning_s'].rjust(self.colw),
            kw['fees_s'].rjust(self.colw),
            kw['profit_s'].rjust(self.colw),
            kw['rent_s'].rjust(self.colw)]

        self.out += [self.leftright(left, ''.join(right))]
        self.out += [self.line('-')]


    def addFortunkyTabline(self, **kw):
        left = ['%(year)d-%(month).2d' % kw]
        right = [
            ('%(bets).f' % kw).rjust(self.colw),
            ('%(paid).f' % kw).rjust(self.colw),
            ('%(winning).f' % kw).rjust(self.colw),
            ('%(fees).f' % kw).rjust(self.colw),
            ('%(profit).f' % kw).rjust(self.colw),
            ('%(rent).f' % kw).rjust(self.colw)]
        self.out += [self.leftright(''.join(left), ''.join(right))]


    # championship

    def addChartsHeader(self, **kw):
        self.out += [self.line('-')]
        self.out += [self.center("%(etapa)s %(begin)s - %(end)s" % kw)]
        self.out += [self.line('-')]


    def addChartsTabline(self, **kw):
        colw2 = self.colw * 2
        right = kw['name'].ljust(colw2)
        self.out += [self.leftright("%(number)2d . %(points)10d" % kw, right)]


    def addChartsTablineTotal(self, **kw):
        colw2 = self.colw * 2
        right = kw['name'].ljust(colw2) + kw['branchname'].ljust(colw2)
        self.out += [self.leftright("%(number)2d. %(points)10d" % kw, right)]


    def addChartsSmaller(self):
        pass


    def addChartsEnd(self):
        pass


