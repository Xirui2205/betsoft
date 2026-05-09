# -*- coding: utf-8 -*-

#TODO: reimplement, this is just copy of offers_gen.py

import time
from datetime import date

from offergen.config import cfg
from offergen.utils import reformatDate


class ResultsGen:
    scores = ['ft', 'ot', 'ap', 'wo', 'c', '(', '1t', '2t', '3t', '1q', '2q', '3q', '4q', 'set1', 'set2', 'set3', 'set4', 'set5', 'ht', ')' ]
    def getScore(self, row):
        first = True
        score = ''
        inSublist = False
        sublist = ''
        sublistCount = 0
        for c in self.__class__.scores :
            if '(' == c :
                inSublist = True
                sublistCount = 0
                sublist = ''
            elif ')' == c :
                if inSublist:
                    if sublistCount > 0 :
                        score += ' (' + sublist + ')'
                    inSublist = False
            elif c in row:
                s = row[c]
                text = None
                textKey = c + '_text'
                if textKey in row : text = row[textKey]
                if text is None : text = ''
                if s is not None :
                    if inSublist:
                        if sublistCount > 0:
                            sublist += ','
                        sublist += s + text
                        sublistCount += 1
                    else:
                        if not first : score += ','
                        else : first = False
                        score += s + text
        return score.encode('utf-8')
#        score = row['score']
#        ot = row.get('ot', None)
#        if ot is not None:
#            text = row.get('ot_text', None)
#            if text is None: text = ''
#            score += ',' + row['ot'] + text
#        ap = row.get('ap', None)
#        if ap is not None:
#            text = row.get('ap_text', None)
#            if text is None: text = ''
#            score += ',' + row['ap'] + text
#        return score.encode('utf-8')

    def format(self, req, rows, fmt, tr):
        # class header
        fmt.addClassHeader(
            cls = 'results',
            twocolumn = True,
            landscape = (req.pageformat == 'landscape'),
            left = req.margins[0],
            right = req.margins[1],
            top = req.margins[2],
            bottom = req.margins[3])

        # nadpis podle druhu nabidky
        title = '! no title !'
        if req.datefilter == 'today':
            title = tr['title-results-today']
        else:
            title = tr['title-results-complete']
        fmt.addOfferTitle('%s %s' % (title, time.strftime('%x %X')))

        # document header
        fmt.addBeginDocument()

        # loop over lines
        last_sport = last_udalost = last_typ = last_sazka = last_sloupec = None
        close_tab = False
        sazka_nazev = None
        sazka_ako = None
        kurzy = False
        tipy = False
        print_head = False
        tabtitle = 'xxx'
        #print rows
            
        for row in rows:
            #print row
            sport_id = int(row['sport_id'])
            udalost_id = int(row['udalost_id'])
            typ_id = int(row['typ_id'])
            sazka_id = int(row['sazka_id'])
            score_note = row['score_note']
            #sazka_handle = str(row['alias']).rjust(4, '0') + '/' + str(row['typ_alias_id']).rjust(2, '0')
            #sloupec_id = int(row['sloupec_id'])
            #sazka_score = str(row['score']) #use getScore

            #print sazka_id
            ##print sazka_handle
            #print 'last sazka'
            #print last_sazka
            
            # print
            if sazka_id != last_sazka:
                if print_head:
                    if close_tab:
                        fmt.addOfferEndTab()
                    close_tab = True
                    print_head = False
                    fmt.addOfferBeginTab(ncols = 1)
                    fmt.addOfferTabTitle(tabtitle)
                    fmt.addResultsTabHead(info = 'číslo', nazev = 'příležitost',
                        score = 'výsledek', datum = 'datum')

                if kurzy:
                    fmt.addResultsTabLine(
                        info = sazka_handle, #str(sazka_id),
                        nazev = sazka_nazev,
                        ako = sazka_ako,
                        datum = sazka_datum,
                        score = sazka_score
                        )
                    #print 'Printing line'
                    #print sazka_nazev
                    #print sazka_handle
                    #print sazka_id
                    #print sazka_score
                    #print '-----------'

            # parse
            if sport_id != last_sport or udalost_id != last_udalost or typ_id != last_typ:
                print_head = True
                tabtitle = '%s - %s - %s' % (
                       row['snazev'].encode('utf-8'),
                       row['onazev'].encode('utf-8'),
                       row['unazev'].encode('utf-8'))
                       #row['tnazev'].encode('utf-8'))
                last_sport = sport_id
                last_udalost = udalost_id
                last_typ = typ_id
                
            if sazka_id != last_sazka:
                kurzy = True
                sazka_nazev = row['mtext'].encode('utf-8')
                sazka_handle = str(row['alias']).rjust(4, '0')
                #+ '/' + str(row['typ_alias_id']).rjust(2, '0')
                sazka_ako = str(row['ako'])
                sazka_datum = '%s' % (reformatDate(str(row['platna_do']), '%d.%m. %H:%M'))
                sazka_score = self.getScore(row)
                if score_note is not None:
                    sazka_score = '%s %s ' % (sazka_score, row['score_note'].encode('utf-8'))
                    
                last_sazka = sazka_id

            
        # end of "for row in rows"

        if print_head:
            if close_tab:
                fmt.addOfferEndTab()
            close_tab = True
            print_head = False
            fmt.addOfferBeginTab(ncols = 1)
            fmt.addOfferTabTitle(tabtitle)
            fmt.addResultsTabHead(info = 'číslo', nazev = 'příležitost',
                score = 'výsledek', datum = 'datum')

        
        if kurzy:
            fmt.addResultsTabLine(
                info = sazka_handle, #str(sazka_id),
                nazev = sazka_nazev,
                ako = sazka_ako,
                datum = sazka_datum,
                score = sazka_score
                )
            #print 'Printing line'
            #print sazka_nazev
            #print sazka_handle
            #print sazka_id
            #print sazka_score
            #print '*************'
        

        if close_tab:
            fmt.addOfferEndTab()

        fmt.addEndDocument()
