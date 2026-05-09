# -*- coding: utf-8 -*-
import MySQLdb, logging, time, datetime, calendar
import array
from config import cfg
from error import DbError, DbBadKeyError

class DatabaseCore:
    def __init__(self):
        self.pool = []
        self.pool_keep_open = cfg.dbminconn
        self.sqllog = logging.getLogger('sql')


    def __del__(self):
        for conn in self.pool:
            conn.close()


    def connect(self, db='main'):
        try:
            db_params = {}
            if db == 'main':
                db_params = {
                    'host': cfg.dbhost,
                    'user': cfg.dbuser,
                    'passwd': cfg.dbpass,
                    'db': cfg.dbname,
                    'charset': cfg.dbcharset
                }
                try:
                    cfg.dbport
                    db_params['port'] = cfg.dbport
                except AttributeError:
                    pass
            
            elif db == 'admin':
                db_params = {
                    'host': cfg.admin_dbhost,
                    'user': cfg.admin_dbuser,
                    'passwd': cfg.admin_dbpass,
                    'db': cfg.admin_dbname,
                    'charset': cfg.admin_dbcharset
                }
                try:
                    db_params['port'] = cfg.admin_dbport
                except AttributeError:
                    pass
            
            conn = MySQLdb.connect(**db_params)
        
        except MySQLdb.DatabaseError, e:
            raise DbError(str(e))
        return conn


    def get_conn(self, db='main'):
        conn = None
        while len(self.pool) and conn is None:
            conn = self.pool.pop()
        if conn is None:
            conn = self.connect(db)
            curs = conn.cursor()
            curs.execute("SET time_zone=\'GMT\'")
        return conn


    def put_conn(self, conn):
        if not conn:
            return
        if len(self.pool) >= self.pool_keep_open:
            conn.close()
        else:
            conn.close()
            #self.pool.append(conn)


    def execute(self, q, args, db='main'):
        conn = None
        try:
            try:
                conn = self.get_conn(db)
                curs = conn.cursor()
                curs.execute(q, args)
                conn.commit()
                self.sqllog.info('%s %s', q, args)
            except MySQLdb.OperationalError, e:
                # disconnected?
                if conn:
                    conn.rollback()
                    conn.close()
                conn = self.get_conn()
                curs = conn.cursor()
                curs.execute(q, args)
                conn.commit()
                self.sqllog.info('%s %s', q, args)
        except MySQLdb.DatabaseError, e:
            self.put_conn(conn)
            raise DbError(str(e))
        return curs


    def cleanup(self, curs):
        self.put_conn(curs.connection)


    def rowsdict(self, curs, rows):
        cols = [x[0] for x in curs.description]
        return [dict(zip(cols, row)) for row in rows]

    # this one removes first char from names
    def rowsdict2(self, curs, rows):
        cols = [x[0][1:] for x in curs.description]
        return [dict(zip(cols, row)) for row in rows]


class Database(DatabaseCore):
    def __init__(self):
        DatabaseCore.__init__(self)
        self.queryfunc = {
            'offer' : self.queryOffers,
            'results' : self.queryResults,
            'aliases' : self.queryAliases
        }


    def registerQueryFunc(self, doctype, query):
        self.queryfunc[doctype] = partial(self.queryGeneric, query)


    def queryGroups(self):
        return []


    def queryActions(self, group):
        return []


    def queryPrinters(self):
        return []


    def queryStatsDates(self):
        query = '''SELECT DISTINCT cas_od::date AS datum FROM tiskopisy.tab_statistika ORDER BY datum'''
        curs = self.execute(query, [])
        rows = curs.fetchall()
        self.cleanup(curs)
        return rows


    def queryStats(self, date=None):
        if date is None:
            date = 'today'
        query = '''SELECT cas_od, pozadavky_celkem, pripraveno_celkem,
            nalezeno_v_cache, chyba, zadny_vystup
            FROM tiskopisy.tab_statistika
            WHERE cas_od::date = %s::date ORDER BY cas_od'''
        curs = self.execute(query, [date])
        rows = curs.fetchall()
        self.cleanup(curs)
        return rows


    def queryDocumentCache(self, req):
        pridat = True

        parameters = req.compress()
        doctype_id = req.getDocumentTypeId()
        conn = self.get_conn()
        curs = conn.cursor()
        curs.execute('BEGIN')
        query = '''SELECT d.id_tiskopis, d.pocet_stran, d.pozadavek, d.pripraven,
                CASE
                    WHEN d.pozadavek < now() - INTERVAL t.obnova1 SECOND THEN 1  -- obnovit
                    WHEN d.pripraven IS NULL THEN 2                               -- generovan jinym
                    ELSE 0                                                        -- ok
                END AS status
            FROM %s AS d
            JOIN %s AS t USING (id_typ_tiskopisu)
            WHERE d.id_typ_tiskopisu = %s AND d.parametry = %s
                AND d.pozadavek >= now() - INTERVAL t.obnova2 SECOND
                AND d.pocet_stran > 0
            ORDER BY d.pozadavek DESC''' % (cfg.db_document_table, cfg.db_document_type_table, '%s', '%s')
        curs.execute(query, [doctype_id, parameters])
        rows = self.rowsdict(curs, curs.fetchall())

        res = []
        for row in rows:
            if row['status'] in (2, 0):
                # 2 - generovan jinym -> nepridavat, cekat
                # 0 - ok, nepridavat
                pridat = False
            res += [row]

        if pridat:
            curs.execute('''INSERT INTO %s (id_typ_tiskopisu, parametry)
                VALUES (%s, %s)''' % (cfg.db_document_table, '%s', '%s'),
                [doctype_id, parameters])
            row = {}
            row['id_tiskopis'] = curs.lastrowid
            row['pocet_stran'] = None
            row['pozadavek'] = None
            row['pripraven'] = None
            row['status'] = 3
            res += [row]

        conn.commit()

        self.put_conn(conn)

        return res


    def setDocumentReady(self, docid, pagecount):
        if docid == 0:
            return
        query = '''UPDATE %s SET pripraven = now(), pocet_stran = %s
            WHERE id_tiskopis = %s''' % (cfg.db_document_table, '%s', '%s')
        curs = self.execute(query, [pagecount, docid])
        self.cleanup(curs)


    def checkDocument(self, docid):
        query = 'SELECT pripraven, pocet_stran FROM %s WHERE id_tiskopis=%s' % (cfg.db_document_table)
        curs = self.execute(query, [docid])
        row = curs.fetchone()
        self.cleanup(curs)
        return row


    def queryDocument(self, req, tpoint=lambda x:x):
        return self.queryfunc[req.documenttype](req, tpoint)

    # parses string with local date/time in format YYYY-mm-ddtHH:MM:SS or YYYY-mm-dd
    # optionally datetime.timedelta object can be specified as added offset (delta parameter)
    # optionally can be specified time portion to be used when date was passed (dateTime parameter)
    # returns string representation of datetime in UTC timezone
    def localtimeToUtc(self, s, delta=None, dateTime=None):
        try:
            tm = time.strptime(s, '%Y-%m-%dt%H:%M:%S')
        except ValueError:
            if dateTime is not None:
                s += 't' + dateTime
                tm = time.strptime(s, '%Y-%m-%dt%H:%M:%S')
            else:
                tm = time.strptime(s, '%Y-%m-%d')
        secs = time.mktime(tm)
        if delta is None: return str( datetime.datetime.utcfromtimestamp(secs) )
        else: return str( datetime.datetime.utcfromtimestamp(secs) + delta )

    def requestTimeToDb(self, s, delta=None, dateTime=None):
        #return self.localtimeToUtc(s, delta, dateTime)
        try:
            tm = time.strptime(s, '%Y-%m-%dt%H:%M:%S')
        except ValueError:
            if dateTime is not None:
                s += 't' + dateTime
                tm = time.strptime(s, '%Y-%m-%dt%H:%M:%S')
            else:
                tm = time.strptime(s, '%Y-%m-%d')
        if delta is None:
            return time.strftime('%Y-%m-%d %H:%M:%S', tm)
        else:
            secs = calendar.timegm(tm)
            return str( datetime.datetime.utcfromtimestamp(secs) + delta )

    def queryAllSports(self):
        # cache this?
        query = '''SELECT
            s.sport_id, COALESCE(p.text, s.nazev) AS nazev
            FROM sport s
            LEFT JOIN preklady p
            ON s.nazev=p.index_pole AND p.lang_id=1
            ORDER BY s.nazev ASC'''
        curs = self.execute(query, {})
        rows = curs.fetchall()
        cols = [x[0] for x in curs.description]
        sports = self.rowsdict(curs, rows)
        for sport in sports:
            if type(sport['nazev']) is array.array : sport['nazev'] = sport['nazev'].tostring()
        self.cleanup(curs)
        return sports


    def queryAllEvents(self, sportGroups):
        # cache this?
        query = '''SELECT
            u.udalost_id, u.sport_id, TRIM(COALESCE(pu.text, u.nazev)) AS unazev, TRIM(COALESCE(po.text, o.nazev)) AS onazev
            FROM udalost u
            LEFT JOIN preklady pu
            ON u.nazev=pu.index_pole AND pu.lang_id=1
            JOIN oblast o
            ON u.oblast_id=o.oblast_id
            LEFT JOIN preklady po
            ON o.nazev=po.index_pole AND po.lang_id=1
            ORDER BY u.sport_id ASC, onazev COLLATE utf8_czech_ci ASC, unazev COLLATE utf8_czech_ci ASC'''
        curs = self.execute(query, {})
        rows = curs.fetchall()
        if sportGroups :
            cols = [x[0] for x in curs.description]
            events = dict()
            for row in rows :
                row = dict(zip(cols, row))
                sportId = row['sport_id']
                eventId = row['udalost_id']
                eventName = row['unazev']
                if type(eventName) is array.array : eventName = eventName.tostring()
                areaName = row['onazev']
                if type(areaName) is array.array : areaName = areaName.tostring()
                row['nazev'] = areaName + ' - ' + eventName
                if sportId not in events : events[sportId] = []
                events[sportId].append(row)
        else :
            events = self.rowsdict(curs, rows)
        self.cleanup(curs)
        return events


    def getFilterSportsEvents(self, req):
        sports = dict()
        events = dict()
        for action in req.sport:
            prefix = action[0]
            number = int(action[1:])
            if 's' == prefix: sports["s%s" % number] = number
            if 'u' == prefix: events["e%s" % number] = number
        return sports,events

    def queryAllBetTypes(self):
        query = '''SELECT
            t.typ_id, TRIM(COALESCE(p.text, t.nazev)) AS nazev
            FROM typ t
            LEFT JOIN preklady p
            ON t.nazev=p.index_pole AND p.lang_id=1
            WHERE (t.typ_alias_group IS NULL OR t.group_master=1) AND t.typ_alias_id<100
            ORDER BY TRIM(COALESCE(p.text, t.nazev)) COLLATE utf8_czech_ci ASC'''
        curs = self.execute(query, {})
        rows = curs.fetchall()
        cols = [x[0] for x in curs.description]
        betTypes = self.rowsdict(curs, rows)
        for betType in betTypes:
            if type(betType['nazev']) is array.array : betType['nazev'] = betType['nazev'].tostring()
        self.cleanup(curs)
        return betTypes

    def getFilterBetTypes(self, req):
        betTypes = dict()
        for betType in req.bettype:
            number = int(betType)
            betTypes["t%s" % number] = number
        return betTypes

    def queryOffers(self, req, tpoint):
        # filter by date
        f_date_from = self.requestTimeToDb( time.strftime('%Ft00:00:00') )
        f_date_from2 = self.requestTimeToDb( time.strftime('%Ft23:59:59') )
        f_date_to = self.requestTimeToDb( time.strftime('%Ft23:59:59') )
        if req.datefilter == 'fromto':
            f_date_from = self.requestTimeToDb(req.datefrom, dateTime='00:00:00')
            f_date_to = self.requestTimeToDb(req.dateto, dateTime='23:59:59')
        if req.datefilter == 'today':
            #only bets that are valid
            f_date_from = self.localtimeToUtc( time.strftime('%Y-%m-%dt%H:%M:%S') )
            #todays bets include bets valid to tomorrow 6am (NHL,..)
            timeframe_end = self.queryParameter('offer.today.timeFrame.end', tpoint)
            #raise Exception('%Y-%m-%dt'+timeframe_end)
            f_date_to = self.requestTimeToDb( time.strftime('%Y-%m-%dt'+timeframe_end), datetime.timedelta(days=1) )
        if req.datefilter == 'all':
            #only bets that are valid
            f_date_from = self.localtimeToUtc( time.strftime('%Y-%m-%dt%H:%M:%S') )
            f_date_to = '3000-01-01 00:00:00'

        # filter main/all
        f_main = (req.gamefilter == 'main')

        # order by date
        f_by_date = (req.sortby == 'date')

        sports, events = self.getFilterSportsEvents(req)
        fmtSports  = []
        for x in sports.keys() :
            fmtSports.append("%%(%s)s" % x)
        fmtSports = ','.join(fmtSports)
        fmtEvents = []
        for x in events.keys() :
            fmtEvents.append("%%(%s)s" % x)
        fmtEvents = ','.join(fmtEvents)

        betTypes = self.getFilterBetTypes(req)
        fmtBetTypes = []
        for x in betTypes.keys() :
            fmtBetTypes.append("%%(%s)s" % x)
        fmtBetTypes = ','.join(fmtBetTypes)

        if len(betTypes) :
            category = 3
        else :
            category = req.category

        # prepare query
        query = '''SELECT
            `s`.`sport_id`,
            s.nazev AS `snazev`,
            u.nazev AS `unazev`,
            `u`.`udalost_id`,
            o.nazev AS `onazev`,
            COALESCE(`sz`.`alias`, '----') AS `alias`,
            `sz`.`typ_id`,
            COALESCE(`rt`.`typ_alias_id`,`t`.`typ_alias_id`) AS `typ_alias_id`,
            `sz`.`alias_new`,
            `t`.`poradi` AS `typ_poradi`,
            COALESCE(`rt`.`poradi`,`t`.`poradi`) AS `typ_poradi2`,
            `sz`.`sazka_id`,
            `sz`.`jednoducha`,
             IF(sz.ticket_text IS NULL OR sz.ticket_text='',sz.text,sz.ticket_text) AS `mtext`,
            `sz`.`podtyp_id`,
            `t`.`nazev` AS `tnazev`,
            COALESCE(`tsn`.`text_note`, '') AS `tnote`,
            `ps`.`nazev` AS `pnazev`,
            `ps`.`sloupec_id`,
            `sk`.`kurz`,
            `sz`.`ako`,
            DATE_FORMAT(sz.platna_do,"%%k:%%i") AS `hour`,
            DATE_FORMAT(sz.platna_do,"%%Y-%%m-%%d %%k:%%i") AS `platna_do`,
            p.`text` AS `ptext`,
            p.`radek_sloupec`,
            `sz`.`info`,
            `tot`.`name` AS `typeOrder`
            FROM `sazky` AS `sz`
            INNER JOIN `udalost` AS `u` ON sz.udalost_id=u.udalost_id
            INNER JOIN `sport` AS `s` ON s.sport_id=u.sport_id
            INNER JOIN `oblast` AS `o` ON o.oblast_id=u.oblast_id
            INNER JOIN `typ` AS `t` ON sz.typ_id=t.typ_id AND t.offer_category_id<=%(category)s
            LEFT JOIN `typ` AS `rt` ON COALESCE(sz.real_typ_id, sz.typ_id)=rt.typ_id
            LEFT JOIN `typ_order_type` AS `tot` ON rt.order_type_id=tot.id
            INNER JOIN `podtyp` AS `p` ON sz.podtyp_id=p.podtyp_id
            INNER JOIN `typ_udalost` AS `tu` ON tu.typ_id=t.typ_id and tu.udalost_id=u.udalost_id
            LEFT JOIN `typ_sport_note` AS `tsn` ON tsn.typ_id=t.typ_id and tsn.sport_id=s.sport_id
            INNER JOIN `sazka_kurz_aktualni` AS `sk` ON sk.sazka_id = sz.sazka_id
            INNER JOIN `podtyp_sloupce` AS `ps` ON ps.sloupec_id = sk.sloupec_id
            WHERE (sz.live=0)
            AND (sz.status=0)
            AND (sz.risk_limit > sz.risk_limit_balance)
            AND (sz.platna_do >= %(date_from)s)
            AND (sz.platna_do <= %(date_to)s)
            AND (s.zobrazeno=1) AND (u.zobrazeno=1) AND (t.zobrazeno=1)
            AND (u.platne_od <= %(date_from)s)
            AND (tu.is_binded = 1)'''
        params = {'date_from' : f_date_from, 'date_to' : f_date_to, 'date_from2' : f_date_from2, 'category' : category }
        if len(sports) and len(events) :
            query += " AND (u.sport_id IN (%s)" % fmtSports + " OR u.udalost_id IN (%s))" % fmtEvents
            params.update(sports)
            params.update(events)
        elif len(sports) :
            query += " AND (u.sport_id IN (%s))" % fmtSports
            params.update(sports)
        elif len(events) :
            query += " AND (u.udalost_id IN (%s))" % fmtEvents
            params.update(events)

        if len(betTypes) :
            query += " AND (t.typ_id IN (%s))" % fmtBetTypes
            params.update(betTypes)

        query += ''' ORDER BY `u`.`pozice_offergen` ASC, `u`.`nazev` ASC, `tu`.`order` ASC, `typ_poradi2` ASC, 
                    `p`.`podtyp_id` ASC, `sz`.`platna_do` ASC,`sz`.`alias_new`, `ps`.`poradi` ASC '''

        # execute
        curs = self.execute(query, params)
        tpoint('query execute')

        rows = curs.fetchall()
        tpoint('query fetch')

        res = self.rowsdict(curs, rows)
        self.cleanup(curs)

        # translate all text columns
        texts = dict()
        for row in res:
            t = row['snazev']
            texts[t] = t
            t = row['onazev']
            texts[t] = t
            t = row['unazev']
            texts[t] = t
            t = row['tnazev']
            texts[t] = t
            t = row['pnazev']
            texts[t] = t
            t = row['ptext']
            texts[t] = t
        if 0 < len(texts):
            formats = ','.join(['%s'] * len(texts))
            curs = self.execute("SELECT `index_pole`,`text` FROM `preklady` WHERE `lang_id`=1 AND `index_pole` IN (%s)" % formats,
                tuple(texts.keys()))

            for row in curs.fetchall():
                texts[row[0]] = row[1]
            self.cleanup(curs)
            for row in res:
                row['snazev'] = texts[row['snazev']]
                row['onazev'] = texts[row['onazev']]
                row['unazev'] = texts[row['unazev']]
                row['tnazev'] = texts[row['tnazev']]
                row['pnazev'] = texts[row['pnazev']]
                row['ptext'] = texts[row['ptext']]

        return res

    def queryResults(self, req, tpoint):
        # filter by date
        f_date_from = time.strftime('%F 00:00:00')
        f_date_from2 = time.strftime('%F 23:59:59')
        f_date_to = time.strftime('%F 23:59:59')
        if req.datefilter == 'fromto':
            f_date_from = self.requestTimeToDb(req.datefrom, dateTime='00:00:00')
            f_date_to = self.requestTimeToDb(req.dateto, dateTime='23:59:59')
        if req.datefilter == 'yesterday':
            #yesterdays results
            today = datetime.datetime.today()
            yesterday = today + datetime.timedelta(days=-1)
            f_date_from = self.requestTimeToDb( yesterday.strftime('%Y-%m-%dt00:00:00') )
            f_date_to = self.requestTimeToDb( today.strftime('%Y-%m-%dt06:00:00') )
        if req.datefilter == 'all':
            today = datetime.datetime.today()
            oneWeekAgo = today + datetime.timedelta(days=-7)
            f_date_from =  self.requestTimeToDb( oneWeekAgo.strftime('%Y-%m-%dt00:00:00') )
            f_date_to = self.requestTimeToDb( today.strftime('%Y-%m-%dt%H:%M:%S') )

        # filter main/all
        f_main = (req.gamefilter == 'main')

        sports, events = self.getFilterSportsEvents(req)
        fmtSports  = []
        for x in sports.keys() :
            fmtSports.append("%%(%s)s" % x)
        fmtSports = ','.join(fmtSports)
        fmtEvents = []
        for x in events.keys() :
            fmtEvents.append("%%(%s)s" % x)
        fmtEvents = ','.join(fmtEvents)

        betTypes = self.getFilterBetTypes(req)
        fmtBetTypes = []
        for x in betTypes.keys() :
            fmtBetTypes.append("%%(%s)s" % x)
        fmtBetTypes = ','.join(fmtBetTypes)

        # order by date
        f_by_date = (req.sortby == 'date')

        # prepare query
        # for columns from sr alias particular column s from srt with _text suffix, generator relies on it
        query = '''SELECT
            `s`.`sport_id`,
            `s`.`nazev` AS `snazev`,
            `o`.`nazev` AS `onazev`,
            `u`.`nazev` AS `unazev`,
            `u`.`udalost_id`,
            COALESCE(`sz`.`alias`, '----') AS `alias`,
            `sz`.`typ_id`,
            `sz`.`alias_new`,
            COALESCE(`rt`.`typ_alias_id`,`t`.`typ_alias_id`) AS `typ_alias_id`,
            `t`.`poradi` AS `typ_poradi`,
            COALESCE(`rt`.`poradi`,`t`.`poradi`) AS `typ_poradi2`,
            `sz`.`sazka_id`,
            `sz`.`jednoducha`,
             IF(sz.ticket_text IS NULL OR sz.ticket_text='',sz.text,sz.ticket_text) AS `mtext`,
            `sz`.`podtyp_id`,
            `t`.`nazev` AS `tnazev`,
            `ps`.`nazev` AS `pnazev`,
            `ps`.`sloupec_id`,
            `sz`.`ako`,
            DATE_FORMAT(sz.platna_do,"%%k:%%i") AS `hour`,
            DATE_FORMAT(sz.platna_do,"%%Y-%%m-%%d %%k:%%i") AS `platna_do`,
            `p`.`text` AS `ptext`,
            `sz`.`info`,
            `sz`.`score` as `score`,
            `sz`.`score_note` as `score_note`,
            `sr`.`ft`, `sr`.`ht`, `sr`.`ot`, `sr`.`ap`,
            `sr`.`1t`, `sr`.`2t`, `sr`.`3t`,
            `sr`.`1q`, `sr`.`2q`, `sr`.`3q`, `sr`.`4q`,
            `sr`.`set1`,  `sr`.`set2`,  `sr`.`set3`,  `sr`.`set4`, `sr`.`set5`,
            `sr`.`wo`, `sr`.`c`,
            `srt`.`ot` AS `ot_text`, `srt`.`ap` AS `ap_text`
            FROM `sazky` AS `sz`
            INNER JOIN `udalost` AS `u` ON sz.udalost_id=u.udalost_id
            INNER JOIN `sport` AS `s` ON s.sport_id=u.sport_id
            INNER JOIN `oblast` AS `o` ON o.oblast_id=u.oblast_id
            INNER JOIN `typ` AS `t` ON sz.typ_id=t.typ_id
            LEFT JOIN `typ` AS `rt` ON sz.real_typ_id=rt.typ_id
            INNER JOIN `podtyp` AS `p` ON sz.podtyp_id=p.podtyp_id
            INNER JOIN `typ_udalost` AS `tu` ON tu.typ_id=t.typ_id and tu.udalost_id=u.udalost_id
            INNER JOIN `sazka_kurz_aktualni` AS `sk` ON sk.sazka_id = sz.sazka_id
            INNER JOIN `podtyp_sloupce` AS `ps` ON ps.sloupec_id = sk.sloupec_id
            LEFT JOIN `sazky_result` AS `sr` ON sz.sazka_id=sr.sazka_id
            LEFT JOIN `sport_result_text` AS `srt` ON s.sport_id=srt.sport_id AND srt.lang_id=1
            WHERE (sz.live=0)
            AND (sz.platna_do <= %(date_to)s)
            AND (sz.platna_do >= %(date_from)s)
            AND (s.zobrazeno=1) AND (u.zobrazeno=1) AND (t.zobrazeno=1)
            AND (u.platne_od <= %(date_from)s)
            AND (sz.parent_id IS NULL) AND (sz.alias IS NOT NULL)
            AND (tu.is_binded = 1)'''
        params = {'date_from' : f_date_from, 'date_to' : f_date_to, 'date_from2' : f_date_from2 }
        if len(sports) and len(events) :
            query += " AND (u.sport_id IN (%s)" % fmtSports + " OR u.udalost_id IN (%s))" % fmtEvents
            params.update(sports)
            params.update(events)
        elif len(sports) :
            query += " AND (u.sport_id IN (%s))" % fmtSports
            params.update(sports)
        elif len(events) :
            query += " AND (u.udalost_id IN (%s))" % fmtEvents
            params.update(events)

        if len(betTypes) :
            query += " AND (t.typ_id IN (%s))" % fmtBetTypes
            params.update(betTypes)

        query += ' ORDER BY `u`.`pozice_offergen` ASC, `u`.`nazev` ASC, `tu`.`order` ASC, `typ_poradi2` ASC, `t`.`typ_id`, `sz`.`platna_do` ASC, `sz`.`sazka_id`, `ps`.`poradi` ASC'

        # execute
        curs = self.execute(query, params)
        tpoint('query execute')

        rows = curs.fetchall()
        tpoint('query fetch')

        res = self.rowsdict(curs, rows)
        self.cleanup(curs)

        # translate all text columns
        texts = dict()
        for row in res:
            t = row['snazev']
            texts[t] = t
            t = row['onazev']
            texts[t] = t
            t = row['unazev']
            texts[t] = t
            t = row['tnazev']
            texts[t] = t
            t = row['pnazev']
            texts[t] = t
            t = row['ptext']
            texts[t] = t
        if 0 < len(texts):
            formats = ','.join(['%s'] * len(texts))
            curs = self.execute("SELECT `index_pole`,`text` FROM `preklady` WHERE `lang_id`=1 AND `index_pole` IN (%s)" % formats,
                tuple(texts.keys()))

            for row in curs.fetchall():
                texts[row[0]] = row[1]
            self.cleanup(curs)
            for row in res:
                row['snazev'] = texts[row['snazev']]
                row['onazev'] = texts[row['onazev']]
                row['unazev'] = texts[row['unazev']]
                row['tnazev'] = texts[row['tnazev']]
                row['pnazev'] = texts[row['pnazev']]
                row['ptext'] = texts[row['ptext']]

        return res

    def queryAliases(self, req, tpoint):
        query = '''SELECT s.sport_id, COALESCE(p1.text, s.nazev) AS snazev, LPAD(COALESCE(gt.typ_alias_id, t.typ_alias_id), 2, '0') AS talias, 
                   COALESCE(p2.text, gt.nazev, t.nazev) AS tnazev 
                   FROM (SELECT sport_id, typ_id FROM typ_podtyp GROUP BY sport_id, typ_id) st 
                   JOIN sport s ON st.sport_id=s.sport_id 
                   LEFT JOIN preklady p1 ON s.nazev=p1.index_pole AND p1.lang_id=1 
                   JOIN typ t ON t.typ_id=st.typ_id AND (t.group_master=1 OR t.typ_alias_group IS NULL) 
                   LEFT JOIN typ gt ON t.typ_alias_group=gt.typ_alias_group 
                   LEFT JOIN preklady p2 ON t.nazev=p2.index_pole AND p2.lang_id=1 
                   ORDER BY snazev, talias'''
        curs = self.execute(query, {})
        rows = curs.fetchall()
        types = self.rowsdict(curs, rows)
        return types

    def queryTickets(self, req, tpoint):
        query = 'SELECT * FROM tiskopisy.tisk_tikety(%s, %s, %s, %s)'
        params = [req.stationid, req.datefrom, req.dateto, req.getTicketsStatus()]

        # execute
        curs = self.execute(query, params)
        tpoint('query execute')

        rows = curs.fetchall()
        tpoint('query fetch')

        res = self.rowsdict2(curs, rows)
        self.cleanup(curs)

        return res


    def queryOther(self, dbfunc, reqparams, req, tpoint):
        query = 'SELECT * FROM tiskopisy.%s(%s)' % (dbfunc, ','.join(['%s'] * len(reqparams)))
        params = [req.__dict__[x] for x in reqparams]

        # execute
        curs = self.execute(query, params)
        tpoint('query execute')

        rows = curs.fetchall()
        tpoint('query fetch')

        res = self.rowsdict2(curs, rows)
        self.cleanup(curs)

        return res


    def queryGeneric(self, query, req, tpoint):
        # execute
        curs = self.execute(query, req.__dict__)
        tpoint('query execute')

        rows = curs.fetchall()
        tpoint('query fetch')

        res = self.rowsdict(curs, rows)
        self.cleanup(curs)

        return res


    def queryStoredReport(self, req, tpoint):
        query = "SELECT uzaverka_text FROM tab_uzaverky \
            WHERE typ_dokladu IN (6,7,8,9,10) AND id_uzaverka=%s"
        curs = self.execute(query, [req.reportid])
        tpoint('query execute')

        row = curs.fetchone()
        tpoint('query fetch')

        self.cleanup(curs)

        if row is None:
            return []

        return [{'source' : row[0]}]
        
        
    def queryParameter(self, param_name, tpoint):
        query = "SELECT value FROM parameter \
            WHERE name = %s"
        curs = self.execute(q=query, args=param_name, db='admin')
        tpoint('query execute')

        row = curs.fetchone()
        tpoint('query fetch')

        self.cleanup(curs)

        if row is None:
            return None

        return row[0]


