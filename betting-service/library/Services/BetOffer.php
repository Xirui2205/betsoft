<?php 
class Services_BetOffer{

	/**
     * Return sum of two variables
     *
     * @param  timestamp $validFrom
     * @param  timestamp $validTo
     * @param  array $sports
     * @return array
     */

     private static $convSports = array(
               1 => 1026, //americky fotbal
               2 => 1006, //basketbal
               3 => 1040, //plazovy volejbal
               4 => 1014, //sipky
               5 => 1022, //florbal
               6 => 1001, //fotbal
               7 => 1035, //futsal
               8 => 1020, //házená
               9 => 1011, //hokej
               10 => 1003, //tenis
               11 => 1013, //tenis
               12 => 1015, //Ragby
               13 => 1025, //Baseball
               14 => 1044, //Plazovy fotbal
               15 => 1032, //Vodní polo
               16 => 1019, //Snooker
               17 => 1016, //Stolni tenis,
               18 => 1012, //zábava
          );

	public function getBetOffer($validFrom = null, $validTo = null, $sportIds = array()) {

          $now = It6_Date::dbNow();

		if (is_null($validFrom)) {
               $jsonError = new Zend_Json_Server_Error('Missing param validFrom!', ERROR_INVALID_PARAMS);
               return $jsonError->toJson();
          }
          else if (is_null($validTo)) {
               $jsonError = new Zend_Json_Server_Error('Missing param validTo!', ERROR_INVALID_PARAMS);
               return $jsonError->toJson();
          }
          else if (empty($sportIds)) {
               $jsonError = new Zend_Json_Server_Error('Missing param sportIds!', ERROR_INVALID_PARAMS);
               return $jsonError->toJson();
          }

          $sports = array();
          foreach($sportIds as $sportId){
               $sports[] = self::$convSports[$sportId];
          }

          $db = Zend_Registry::get("db");

          $select = $db->select()
                              ->from(
                                   array('sz'=>'sazky'),
                                   array(
                                        's.sport_id',
                                        'snazev'=>'s.nazev',
                                        'unazev'=>'u.nazev',
                                        'onazev'=>"(TRIM(COALESCE(tro.text,o.nazev))$langColSql)",
                                        'u.udalost_id',
                                        'u.betradar_udalost_id',
                                        'sz.typ_id',
                                        'sz.real_typ_id',
                                        'sz.sazka_id',
                                        'sz.jednoducha',
                                        'sz.ako',
                                        //'sz.betradar_sazka_id',
                                        'sz.betradar_match_id',
                                        'mtext'=>"IF(sz.ticket_text IS NULL OR sz.ticket_text='',sz.text,sz.ticket_text)",
                                        'terno' => "IF(EXISTS(select sazka_id from hot_bet where sazka_id = sz.sazka_id), 1, 0)",
                                        'sz.podtyp_id',
                                        'tnazev'=>'t.nazev',
                                        'textNote'=>'tsn.text_note',
                                        'betTextNote' => 'sz.text_note',
                                        'pnazev'=>'ps.nazev',
                                        'ps.sloupec_id',
                                        'sk.kurz',
                                        'platna_do'=>'sz.platna_do',
                                        'ptext'=>'p.text',
                                        'sz.info',
                                        'szAlias' => 'alias_new',
                                        'parent_id' => 'sz.parent_id',
                                        'realTypeId' => 't2.typ_id',
                                        't2.typ_alias_id',
                                        'typeOrder' => 'tot.name',
                                        'p.radek_sloupec',
                                        'p.sloupec_pocet_max',
                                        'liveBetId' => 'ml.id',
                                        'oblastId' => 'o.oblast_id'
                              ))
                              ->join(
                                   array('u'=>'udalost'),
                                   'sz.udalost_id=u.udalost_id AND u.zobrazeno = 1',null
                              )
                              ->join(
                                   array('s'=>'sport'),
                                   's.sport_id=u.sport_id AND s.zobrazeno = 1', null
                              )
                              ->join(
                                   array('o'=>'oblast'),
                                   'o.oblast_id=u.oblast_id', null
                              )
                              ->joinLeft(
                                   array('tro' => 'preklady'),
                                   'o.nazev=tro.index_pole AND tro.lang_id=' . intval(1),
                                   array()
                              )
                              ->join(
                                   array('t'=>'typ'),
                                   'sz.typ_id=t.typ_id AND t.zobrazeno', null
                              )
                              ->join(
                                   array('t2'=>'typ'),
                                   'COALESCE(sz.real_typ_id, sz.typ_id)=t2.typ_id', null
                              )
                              ->joinLeft     (
                                   array('tot'=>'typ_order_type'),
                                   'tot.id=t2.order_type_id', null
                              )
                              ->join(
                                   array('p'=>'podtyp'),
                                   'sz.podtyp_id=p.podtyp_id', null
                              )
                              ->joinLeft(
                                   array('tu'=>'typ_udalost'),
                                   'tu.typ_id=t.typ_id AND tu.udalost_id=u.udalost_id', null
                              )
                              ->joinLeft(
                                   array('tsn'=>'typ_sport_note'),
                                   'tsn.typ_id=t.typ_id AND tsn.sport_id=s.sport_id', null
                              )
                              ->join(
                                   array('sk'=>'sazka_kurz_aktualni'),
                                   'sk.sazka_id = sz.sazka_id', null
                              )
                              ->join(
                                   array('ps'=>'podtyp_sloupce'),
                                   'ps.sloupec_id = sk.sloupec_id', null
                              )
                              ->joinLeft(
                                   array('ml' => 'match_live'),
                                   'sz.sazka_id = ml.special_id', null
                              )
                              ->where('sz.live=0')
                              ->where('sz.status=0')
                              ->where('sz.platna_od<=?',$now )
                              ->where('sz.platna_do >= ?', It6_Date::timestampToDb($validFrom))
                              ->where('sz.platna_do <= ?', It6_Date::timestampToDb($validTo))
                              ->where('sz.risk_limit > sz.risk_limit_balance')
                              ->where('s.sport_id IN(' . implode(",", $sports) . ')')
                              ->where('sz.parent_id IS NULL')
                              ->where('(sz.typ_id IN(19,22,20,26,165) OR (sz.typ_id = 31 AND s.sport_id = 1012) OR (s.sport_id = 1012 AND sz.typ_id = 32))')//pouze vitez a vitez zapasu, vice_mene
                              ->where('u.platne_od<=?',$now)
                              ->where('u.platne_do>=?',$now);
                          //    ->where('u.udalost_id IN (?)',$events)
                             // ->limit(10);

                         $rows2 = $select->query()->fetchAll();

                         $bets = array();

                         $revertSports = array_flip(self::$convSports);

                         foreach ($rows2 as $row){
                              $bets[$row["sazka_id"]]["columns"][] = array(
                                        "name" => $row["pnazev"],
                                        "rate" => (string) $row["kurz"]
                                   );
                              $bets[$row["sazka_id"]]["betName"] = $row["mtext"];
                              $bets[$row['sazka_id']]["eventName"] = It6_Models_Translator::translate($row["unazev"], 1, $db);
                              $bets[$row["sazka_id"]]["eventId"] = $row["udalost_id"];
                              $bets[$row['sazka_id']]["betTypeName"] =It6_Models_Translator::translate($row["tnazev"], 1, $db);
                              $bets[$row["sazka_id"]]["time"] = It6_Date::fromDbAsTimestamp($row["platna_do"]);
                              $bets[$row['sazka_id']]["sportName"] = $row["snazev"];
                              $bets[$row['sazka_id']]["areaName"] = $row["onazev"];
                              $bets[$row['sazka_id']]["areaId"] = $row["oblastId"];
                              $bets[$row["sazka_id"]]["sportId"] = $revertSports[$row["sport_id"]];
                              $bets[$row["sazka_id"]]["betAlias"] = $row["szAlias"];
                              $bets[$row["sazka_id"]]["terno"] = $row["terno"];
                         }

                         return It6_ArrayWrapper::toNativeArray($bets);
                         //return $select->__toString();

                         //__toString new Zend_Controller_Request_Http();
	}

     /**
     * Return bet results
     *
     * @return array
     */


     public function getBetResults() {

          $t = localtime(time(), true);

          $start = mktime(
               $t["tm_hour"] - 1,
               0,
               0,
               $t["tm_mon"] + 1,
               $t["tm_mday"],
               $t["tm_year"] + 1900
               );

          $end = gmmktime(
               $t["tm_hour"] -1, 
               59,
               59,
               $t["tm_mon"] + 1,
               $t["tm_mday"],
               $t["tm_year"] + 1900
               );

          $db = Zend_Registry::get("db");

          $select = $db->select()->from(
                    array("r" => "sazky_result"), 
                    array(
                         "result" => "r.ft",
                         "betId" => "s.sazka_id",
                         'mtext'=>"IF(s.ticket_text IS NULL OR s.ticket_text='',s.text,s.ticket_text)",
                         "s.udalost_id"
               ))
               ->join(
                    array('s'=>'sazky'),
                    's.sazka_id=r.sazka_id', null
               )
               ->join(
                    array('u'=>'udalost'),
                    's.udalost_id=u.udalost_id AND u.zobrazeno = 1',null
               )
               ->join(
                    array('sp'=>'sport'),
                    'sp.sport_id=u.sport_id AND sp.zobrazeno = 1', null
               )
               ->where("r.ft IS NOT NULL")
               ->where("r.date_written > ?", It6_Date::timestampToDb($start))              
               ->where('s.parent_id IS NULL')
               ->where('sp.sport_id IN(' . implode(",", array_values(self::$convSports)) . ')')
               ->where('s.typ_id IN(19,22)')//pouze vitez a vitez zapasu
               ->order("r.date_written desc");

          $rows = $select->query()->fetchAll();
          return It6_ArrayWrapper::toNativeArray($rows);
          //return $select->__toString();
          //return $rows;
     }

}