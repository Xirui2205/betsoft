<?php

class It6_Models_Markets extends It6_Models_DbDependent {

	/**
	 * nahraje vsechna potrebna data k terno sazkam
	 * sazky definovane bookmakerem
	 *
	 * @return array
	 */
	public static function loadTernoMarkets($langId=null) {
		if($langId == null)
			$langId = $_SESSION['lang_id'];
			
		$now = It6_Date::dbNow();

		try{
/*
			$frontendOptions = array(
				'lifetime' => MARKETS_CACHE_LIFETIME, // cache lifetime of 2 hours
				'automatic_serialization' => true
			);
			$backendOptions = $GLOBALS['ZEND_CACHE_BACKEND_OPTIONS'];
			$cache = Zend_Cache::factory('Core','File',$frontendOptions,$backendOptions);
			if(!$row = $cache->load('market_'. $_SESSION['lang_id'] .'_terno')) {
*/

				$row = Zend_Registry::get('db')->select()
					->from(
						array('sz'=>'sazky'),
						array(
							'snazev'=>'TRANSLATE(s.nazev,'.$langId.')',
							'onazev'=>'TRANSLATE(o.nazev,'.$langId.')',
							'unazev'=>'TRANSLATE(u.nazev,'.$langId.')',
							'u.udalost_id',
							'sz.typ_id',
							'sz.info',
							'sz.ako',
							'sz.sazka_id',
							'sz.jednoducha',
							'mtext'=>"IF(sz.ticket_text IS NULL OR sz.ticket_text='',sz.text,sz.ticket_text)",
							'sz.podtyp_id',
							'tnazev'=>'TRANSLATE(t.nazev,'.$langId.')',
							'pnazev'=>'TRANSLATE(ps.nazev,'.$langId.')',
							'ska.sloupec_id',
							'ska.kurz',
							'date'=>'sz.platna_do',
							'alias_new',
							'ptext'=>'TRANSLATE(p.text,'.$langId.')',
							'textNote' => 'sz.text_note',
							'liveBetId' => 'ml.id'
					))

					->join(
						array('u'=>'udalost'),
						'sz.udalost_id = u.udalost_id', null)
					->join(
						array('o'=>'oblast'),
						'o.oblast_id = u.oblast_id')
					->join(
						array('ska' => 'sazka_kurz_aktualni'),
						'ska.sazka_id = sz.sazka_id')
					->join(
						array('ps' => 'podtyp_sloupce'),
						'ska.sloupec_id = ps.sloupec_id')
					->join(
						array('h'=>'hot_bet'),
						'h.sazka_id = sz.sazka_id', null)
					->join(
						array('s'=>'sport'),
						's.sport_id = u.sport_id', null)
					->join(
						array('t'=>'typ'),
						'sz.typ_id = t.typ_id', null)
					->join(
						array('p'=>'podtyp'),
						'sz.podtyp_id = p.podtyp_id', null)
					->join(
						array('tu'=>'typ_udalost'),
						'tu.typ_id = t.typ_id AND tu.udalost_id = u.udalost_id', null)
					->joinLeft(
							array('ml' => 'match_live'),
							'sz.sazka_id = ml.special_id', null
						)
					->where('sz.live = 0')
					->where('sz.status = ?', 0)
					->where('sz.platna_od <= ?', $now )
					->where('sz.platna_do >= ?', $now )
					->where('sz.risk_limit > sz.risk_limit_balance')
					->where('s.zobrazeno = ?', 1)
					->where('u.zobrazeno = ?', 1)
					->where('u.platne_od <= ?', $now)
					->where('u.platne_do >= ?', $now)
					->where('tu.is_binded = 1')
					->order(array(
						'sz.platna_do ASC',
						's.pozice',
						'u.pozice',
						't.poradi',
						'sz.sazka_id',
						'ps.poradi'
					))
					->query()->fetchAll();
		}
		catch ( Exception $e ) {
			throw new exception($e);
		}

		$data = array();
		foreach($row as $h){
			if(!isset($data[$h['sazka_id']])){
				$data[$h['sazka_id']] = array(
					'snazev'		=> $h['snazev'],
					'onazev'		=> $h['onazev'],
					'unazev'		=> $h['unazev'],
					'tnazev'		=> $h['tnazev'].(!empty($h['textNote']) ? " {$h['textNote']}" : ''),
					'ptext'			=> $h['ptext'],
					'typ_id'		=> $h['typ_id'],
					'podtyp_id'		=> $h['podtyp_id'],
					'jednoducha'	=> $h['jednoducha'],
					'mtext'			=> $h['mtext'],
					'date'			=> It6_Date::fromDbShort($h['date']),
					'timestamp'		=> It6_Date::fromDbAsTimestamp($h['date']),
					'ako'			=> $h['ako'],
					'info'			=> $h['info'],
					'aliasNew'		=> $h['alias_new']
				);
			}
			$data[$h['sazka_id']]['sloupec'][$h['sloupec_id']]['rate']		= $h['kurz'];
			$data[$h['sazka_id']]['sloupec'][$h['sloupec_id']]['pnazev']	= $h['pnazev'];
		}

		return $data;
	}
	
	/**
	 * nahraje vsechna potrebna data k supertip sazkam na homepage
	 * 
	 * @return array
	 */
	public static function loadSupertipMarkets($langId=null) {
		if($langId == null)
			$langId = $_SESSION['lang_id'];
			
		$now = It6_Date::dbNow();

		try{

				$row = Zend_Registry::get('db')->select()
					->from(
						array('sz'=>'sazky'),
						array(
							'snazev'=>'TRANSLATE(s.nazev,'.$langId.')',
							'onazev'=>'TRANSLATE(o.nazev,'.$langId.')',
							'unazev'=>'TRANSLATE(u.nazev,'.$langId.')',
							'u.udalost_id',
							'sz.typ_id',
							'sz.sazka_id',
							'sz.ako',
							'sz.info',
							'sz.jednoducha',
							'mtext'=>"IF(sz.ticket_text IS NULL OR sz.ticket_text='',sz.text,sz.ticket_text)",
							'sz.podtyp_id',
							'tnazev'=>'TRANSLATE(t.nazev,'.$langId.')',
							'pnazev'=>'TRANSLATE(ps.nazev,'.$langId.')',
							'ska.sloupec_id',
							'ska.kurz',
							'alias_new',
							'date'=>'sz.platna_do',
							'ptext'=>'TRANSLATE(p.text,'.$langId.')',
							'textNote' => 'sz.text_note',
							'liveBetId' => 'ml.id'
					))

					->join(
						array('u'=>'udalost'),
						'sz.udalost_id = u.udalost_id', null)
					->join(
						array('o'=>'oblast'),
						'o.oblast_id = u.oblast_id')
					->join(
						array('ska' => 'sazka_kurz_aktualni'),
						'ska.sazka_id = sz.sazka_id')
					->join(
						array('ps' => 'podtyp_sloupce'),
						'ska.sloupec_id = ps.sloupec_id')
					->join(
						array('s'=>'sport'),
						's.sport_id = u.sport_id', null)
					->join(
						array('t'=>'typ'),
						'sz.typ_id = t.typ_id', null)
					->join(
						array('p'=>'podtyp'),
						'sz.podtyp_id = p.podtyp_id', null)
					->join(
						array('tu'=>'typ_udalost'),
						'tu.typ_id = t.typ_id AND tu.udalost_id = u.udalost_id', null)
					->joinLeft(
							array('ml' => 'match_live'),
							'sz.sazka_id = ml.special_id', null
						)
					->where('u.sport_id = 1056') // <-- Supertip
					->where('sz.live = 0')
					->where('sz.status = ?', 0)
					->where('sz.platna_od <= ?', $now )
					->where('sz.platna_do >= ?', $now )
					->where('sz.risk_limit > sz.risk_limit_balance')
					->where('s.zobrazeno = ?', 1)
					->where('u.zobrazeno = ?', 1)
					->where('u.platne_od <= ?', $now)
					->where('u.platne_do >= ?', $now)
					->where('tu.is_binded = 1')
					->order(array(
						'sz.platna_do ASC',
						's.pozice',
						'u.pozice',
						't.poradi',
						'sz.sazka_id',
						'ps.poradi'
					))
					->query()->fetchAll();
		}
		catch ( Exception $e ) {
			throw new exception($e);
		}

		$data = array();
		foreach($row as $h){
			if(!isset($data[$h['sazka_id']])){
				$data[$h['sazka_id']] = array(
					'snazev'		=> $h['snazev'],
					'onazev'		=> $h['onazev'],
					'unazev'		=> $h['unazev'],
					'tnazev'		=> $h['tnazev'].(!empty($h['textNote']) ? " {$h['textNote']}" : ''),
					'ptext'			=> $h['ptext'],
					'typ_id'		=> $h['typ_id'],
					'podtyp_id'		=> $h['podtyp_id'],
					'jednoducha'	=> $h['jednoducha'],
					'mtext'			=> $h['mtext'],
					'date'			=> It6_Date::fromDbShort($h['date']),
					'timestamp'		=> It6_Date::fromDbAsTimestamp($h['date']),
					'ako'			=> $h['ako'],
					'info'			=> $h['info'],
					'aliasNew'		=> $h['alias_new']
				);
			}
			$data[$h['sazka_id']]['sloupec'][$h['sloupec_id']]['rate']		= $h['kurz'];
			$data[$h['sazka_id']]['sloupec'][$h['sloupec_id']]['pnazev']	= $h['pnazev'];
		}

		return $data;
	}
}
