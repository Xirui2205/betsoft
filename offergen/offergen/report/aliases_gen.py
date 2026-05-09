# -*- coding: utf-8 -*-

import time
from datetime import date

from offergen.config import cfg
from offergen.utils import reformatDate


class AliasesGen:
    def format(self, req, rows, fmt, tr):
        # class header
        fmt.addClassHeader(
            cls = 'aliases',
            twocolumn = True,
            landscape = (req.pageformat == 'landscape'),
            left = req.margins[0],
            right = req.margins[1],
            top = req.margins[2],
            bottom = req.margins[3])

        # nadpis podle druhu nabidky
        title = tr['title-aliases']
        fmt.addOfferTitle('%s %s' % (title, time.strftime('%x %X')))

        # document header
        fmt.addBeginDocument()

        # loop over lines
        last_sport = last_alias = None
        sport_id = None
        sport_nazev = None
        alias = None
        typ_nazev = None
        print_head = (0 < len(rows))
        close_tab = False
        tabtitle = 'xxx'
        #print rows
            
        for row in rows:
            #print row
            sport_id = int(row['sport_id'])
            alias = str(row['talias'])
            sport_nazev = str(row['snazev'])
            typ_nazev = str(row['tnazev'])
        
            # print
            if sport_id != last_sport:
 #               if print_head:
                if close_tab:
                    fmt.addOfferEndTab()
                close_tab = True
#                    print_head = False
                tabtitle = sport_nazev
                fmt.addOfferBeginTab(ncols = 1)
                fmt.addOfferTabTitle(sport_nazev)
                fmt.addAliasesTabHead(alias = 'alias', nazev = 'typ')
                last_sport = sport_id

            fmt.addAliasesTabLine(
                alias = alias,
                nazev = typ_nazev)

        if close_tab:
            fmt.addOfferEndTab()

        fmt.addEndDocument()
