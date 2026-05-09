# -*- coding: utf-8 -*-
import time
import collections
from datetime import date
import datetime

from offergen.config import cfg
from offergen.utils import reformatDate
from collections import defaultdict
from operator import itemgetter

class OffersGen:
    def format(self, req, rows, fmt, tr):
        # class header
        fmt.addClassHeader(
            cls = 'nabidkavic',
            twocolumn = True,
            landscape = (req.pageformat == 'landscape'),
            left = req.margins[0],
            right = req.margins[1],
            top = req.margins[2],
            bottom = req.margins[3])

        now = datetime.datetime.now()

        # nadpis podle druhu nabidky
        title = '! no title !'
        if req.datefilter == 'today':
            title = tr['title-offer-today']
        else:
            title = tr['title-offer-complete']
        fmt.addOfferTitle('%s %s %s' % (title, now.strftime("%A"), time.strftime('%x %X')))

        # document header
        fmt.addBeginDocument()

        # loop over lines
        last_sport = last_udalost = last_typ = last_sazka = last_sloupec = None
        close_tab = False
        sazka_nazev = None
        sazka_nazev_info = ''
        sazka_ako = None
        kurzy = []
        tipy = []
        print_head = False
        prevSportTitle = ''
        prevEventTitle = ''
        tabtitle = ''

        orderMap = {
            'rate_asc': ['platna_do','kurz'],
            'alias_rate_asc': ['platna_do','alias_new'],
            'treshold_asc': ['platna_do','mtext'],
            'text_asc': ['platna_do','mtext']
        }
        typeOrders = {}
        res_sorted = []
        grouped = collections.OrderedDict()

        for r in rows:
            if (r['typeOrder']):
                typeOrders[r['typ_id']] = r['typeOrder']
            grouped.setdefault(r['sport_id'],collections.OrderedDict()).setdefault(r['udalost_id'],collections.OrderedDict()).setdefault(r['typ_id'],collections.OrderedDict()).setdefault(r['sazka_id'],[])
            grouped[r['sport_id']][r['udalost_id']][r['typ_id']][r['sazka_id']].append(r)

        for key_s, value_s in grouped.iteritems():
            for key_e, value_e in grouped[key_s].iteritems():
                for key_t, value_t in grouped[key_s][key_e].iteritems():
                    if key_t in typeOrders.keys():
                        value_t_sorted = sorted(value_t.iteritems(),key=lambda (k,v): (v[0][orderMap[typeOrders[key_t]][0]], v[0][orderMap[typeOrders[key_t]][1]]))
                        for key_sorted, value_sorted in value_t_sorted:
                            res_sorted = res_sorted + value_sorted
                    else:
                        for key_b, value_b in grouped[key_s][key_e][key_t].iteritems():
                            res_sorted = res_sorted + value_b

        for row in res_sorted:
            sport_id = int(row['sport_id'])
            udalost_id = int(row['udalost_id'])
            typ_id = int(row['typ_id'])
            podtyp_id = int(row['podtyp_id'])
            sazka_id = int(row['sazka_id'])
            sloupec_id = int(row['sloupec_id'])
            sazka_info = row['info']
            showTitle = False

            if sazka_id != last_sazka:
                if print_head and tipy:
                    if close_tab:
                        fmt.addOfferEndTab()
                    if not radek_sloupec:
                        close_tab = True
                    else:
                        close_tab = False
                    print_head = False
                    if radek_sloupec:
                        showTitle = tabtitle
                        pass
                    elif len(tipy) > 6:
                        fmt.addOfferBeginTab(ncols = 2)
                        if sportTitle != prevSportTitle:
                            fmt.addOfferSportTitle(sportTitle)
                            prevSportTitle = sportTitle
                        if eventTitle != prevEventTitle:
                            fmt.addOfferEventTitle(eventTitle)
                            prevEventTitle = eventTitle\
                        # commented out by Martin 4.10.2012 mantis:1320
                        #fmt.addOfferTabTitle(tabtitle)
                        fmt.addOfferTabHead(info = 'číslo', betType = tabtitle, nazev = 'příležitost',
                            ako = 'AKO', datum = 'datum', tipy = ['tip', 'kurz'])
                    else:
                        fmt.addOfferBeginTab(ncols = len(tipy))
                        if sportTitle != prevSportTitle:
                            fmt.addOfferSportTitle(sportTitle)
                            prevSportTitle = sportTitle
                        if eventTitle != prevEventTitle:
                            fmt.addOfferEventTitle(eventTitle)
                            prevEventTitle = eventTitle
                        # commented out by Martin 4.10.2012 mantis:1320
                        #fmt.addOfferTabTitle(tabtitle)
                        fmt.addOfferTabHead(info = 'číslo', betType = tabtitle, nazev = 'příležitost',
                            ako = 'AKO', datum = 'datum', tipy = tipy[:6])

                if kurzy:
                    #print kurzy
                    for i,value in enumerate(kurzy):
                        oprKurz = kurzy[i].rstrip('0').rstrip('.')
                        kurzy[i] = value.replace(kurzy[i], oprKurz)

                    if radek_sloupec:
                        fmt.addOfferTypeBTabLine(
                                info = sazka_handle, #str(sazka_id),
                                nazev = sazka_nazev,
                                ako = sazka_ako,
                                datum = sazka_datum,
                                kursy = kurzy,
                                tipy = tipy,
                                title = showTitle,
                                sportTitle = {True: sportTitle, False: False}[sportTitle != prevSportTitle],
                                eventTitle = {True: eventTitle, False: False}[eventTitle != prevEventTitle],
                                landscape = (req.pageformat == 'landscape'))
                        showTitle = False
                        prevSportTitle = sportTitle
                        prevEventTitle = eventTitle
                    elif len(kurzy) > 6:
                        # kazdy tip na vlastni radek
                        for k in range(len(kurzy)):
                            fmt.addOfferTabLine(
                                info = sazka_handle, #str(sazka_id),
                                nazev = sazka_nazev,
                                ako = sazka_ako,
                                datum = sazka_datum,
                                kursy = [tipy[k], kurzy[k]])
                    else:
                        fmt.addOfferTabLine(
                            info = sazka_handle, #str(sazka_id),
                            nazev = sazka_nazev,
                            ako = sazka_ako,
                            datum = sazka_datum,
                            kursy = kurzy)
                    kurzy = []

            # parse
            newBlock = (sport_id != last_sport or udalost_id != last_udalost or typ_id != last_typ or podtyp_id != last_podtyp)
            newTipSet = (not newBlock and podtyp_id != last_podtyp)
            if newBlock or newTipSet:
                print_head = newBlock
                tipy = []

                if int(row['ako']) > 0:
                    ako_str = ' (AKO ' + str(row['ako']) + ')'
                else:
                    ako_str = ''

                sportTitle = row['snazev'].encode('utf-8')
                eventTitle = '%s - %s - %s' % (row['snazev'].encode('utf-8'), row['onazev'].encode('utf-8'), row['unazev'].encode('utf-8'),)
                tabtitle = '%s %s%s' % (
                       row['tnazev'].encode('utf-8'),
                       row['tnote'].encode('utf-8'),
                       ako_str)
                last_sport = sport_id
                last_udalost = udalost_id
                last_typ = typ_id
                last_podtyp = podtyp_id
                
            if sloupec_id != last_sloupec or sazka_id != last_sazka:
                tipy += [row['pnazev'].encode('utf-8')]
                kurzy += [str(row['kurz'])]
                last_sloupec = sloupec_id
                radek_sloupec = bool(row['radek_sloupec'])

            if sazka_id != last_sazka:
                if len(sazka_info) > 0:
                    sazka_nazev_info = row['mtext'] + " (" + row['info'] + ')'
                    sazka_nazev = sazka_nazev_info.encode('utf-8')
                else:
                    sazka_nazev = row['mtext'].encode('utf-8')

                if not row['alias_new']:
                    sazka_handle = str(row['alias']) + str(row['typ_alias_id']).rjust(2, '0')
                else:
                    sazka_handle = row['alias_new']

                sazka_ako = str(row['ako'])
                sazka_datum = '%s' % (reformatDate(str(row['platna_do']), '%d.%m. %H:%M'))
                last_sazka = sazka_id
                radek_sloupec = bool(row['radek_sloupec'])
        # end of "for row in rows"

        if print_head and tipy:
            if close_tab:
                fmt.addOfferEndTab()
            if not radek_sloupec:
                close_tab = True
            else:
                close_tab = False
            print_head = False
            if not radek_sloupec:
                fmt.addOfferBeginTab(ncols = len(tipy[:6]))
                if sportTitle != prevSportTitle:
                    fmt.addOfferSportTitle(sportTitle)
                    prevSportTitle = sportTitle
                if eventTitle != prevEventTitle:
                    fmt.addOfferEventTitle(eventTitle)
                    prevEventTitle = eventTitle
                # commented out by Martin 4.10.2012 mantis:1320
                #fmt.addOfferTabTitle(tabtitle)
                fmt.addOfferTabHead(info = 'číslo', betType = tabtitle, nazev = 'příležitost',
                    ako = 'AKO', datum = 'datum', tipy = tipy[:6])

        if kurzy:
            for i,value in enumerate(kurzy):
                oprKurz = kurzy[i].rstrip('0').rstrip('.')
                kurzy[i] = value.replace(kurzy[i], oprKurz)

            if radek_sloupec:
                fmt.addOfferTypeBTabLine(
                        info = sazka_handle, #str(sazka_id),
                        nazev = sazka_nazev,
                        ako = sazka_ako,
                        datum = sazka_datum,
                        kursy = kurzy,
                        tipy = tipy,
                        sportTitle = {True: sportTitle, False: False}[sportTitle != prevSportTitle],
                        eventTitle = {True: eventTitle, False: False}[eventTitle != prevEventTitle],
                        title = showTitle,
                        landscape = (req.pageformat == 'landscape'))
                showTitle = False
            elif len(kurzy) > 6:
                # kazdy tip na vlastni radek
                for k in range(len(kurzy)):
                    fmt.addOfferTabLine(
                        info = sazka_handle, #str(sazka_id),
                        nazev = sazka_nazev,
                        ako = sazka_ako,
                        datum = sazka_datum,
                        kursy = [tipy[k], kurzy[k]])
            else:
                fmt.addOfferTabLine(
                    info = sazka_handle, #str(sazka_id),
                    nazev = sazka_nazev,
                    ako = sazka_ako,
                    datum = sazka_datum,
                    kursy = kurzy)

        if close_tab:
            fmt.addOfferEndTab()

        fmt.addEndDocument()