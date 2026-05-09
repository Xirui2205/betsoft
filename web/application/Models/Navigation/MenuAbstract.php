<?php


abstract class Models_Navigation_MenuAbstract{

	/**
	 * Vraci  menu s ohledem na pocet aktivnich sazek
	 *
	 * @param int $lang_id   ID jazyka
	 * @return array
	 */

	protected function getActiveMenu($lang_id = 1, $timeFilter = null){

	 $now = It6_Date::dbNow();

	 try{

	 	// Cache of the menu is not working when is not deleted on update,
	 	//TODO write better cache
	 	$frontendOptions = array(
          'lifetime' => MENU_CACHE_LIFETIME, 
          'automatic_serialization' => true
	 	);
		$backendOptions = $GLOBALS['ZEND_CACHE_BACKEND_OPTIONS'];

	 	$cache = Zend_Cache::factory('Core','File',$frontendOptions,$backendOptions);

	 	//if(!$row = $cache->load('sportMenu_'.$_SESSION['lang_id'])) {

			$db = Zend_Registry::get('db');

			$collation = It6_Models_Language::get($lang_id, 'collation', $db);
			$collation = (empty($collation) ? '' : ' COLLATE ' . $collation);
			
			$select = $db->select()
				->from(
					array('s'=>'sport'),
					/*
					array(
						'upocet'=>'count(u.udalost_id)',
						's.sport_id','sport_highlight'=>'s.zvyrazneni',
						'snazev'=>'TRANSLATE(s.nazev,'. $lang_id .')',
						'sport_pozice'=>'s.pozice',
						'o.oblast_id',
						'onazev'=>'TRIM(BOTH from TRANSLATE(o.nazev,'. $lang_id .'))',
						'o.iso',
						'unazev'=>'TRANSLATE(u.nazev,'. $lang_id .')',
						'u.udalost_id',
						'uzvyrazneni'=>'u.zvyrazneni',
						'upozice'=>'u.pozice'))
					*/
					array(
						'upocet'=>'count(distinct sz.betradar_match_id)',
						's.sport_id',
						'sport_highlight'=>'s.zvyrazneni',
						'snazev'=> 's.nazev',
						'sport_pozice'=>'s.pozice',
						'o.oblast_id',
						'onazev'=> "(TRIM(COALESCE(tro.text,o.nazev))$collation)",
						'opozice'=>'o.pozice',
						'o.iso',
						'unazev'=>'u.nazev',
						'u.udalost_id',
						'uzvyrazneni'=>'u.zvyrazneni',
						'upozice'=>'u.pozice'))
				->join(array('u'=>'udalost'),'s.sport_id=u.sport_id', array())
				->join(array('o'=>'oblast'),'u.oblast_id=o.oblast_id', array())
				->joinLeft(array('tro' => 'preklady'),'o.nazev=tro.index_pole AND tro.lang_id='.intval($lang_id),array())
				->join(
					array('sz'=>'sazky'),
					'sz.udalost_id=u.udalost_id AND sz.live = 0 AND sz.status = 0 AND sz.risk_limit > sz.risk_limit_balance AND sz.platna_od<= \''.$now.'\' AND sz.platna_do>=\''.$now.'\' ',
					array()
				)
				->join(array('t'=>'typ'), 'sz.typ_id=t.typ_id', array())
				->where('s.zobrazeno=?',1)
				->where('s.hide_in_sportmenu=?',0)
				//->where('sz.platna_od<=?',$now )
				//->where('sz.platna_do>=?',$now )
				//->where('sz.live = 0')
				//->where('sz.status=0')
				//->where('sz.risk_limit > sz.risk_limit_balance')
				->where('u.zobrazeno=?',1)
				->where('t.zobrazeno=?',1)
				->where('u.platne_od<=?',$now)
				->where('u.platne_do>=?',$now)
				->group(array('s.sport_id', 'u.udalost_id'))
				->order(array('sport_pozice','opozice', 'onazev','upozice'));
			
			if (Models_Markets_MarketData::TIME_FILTER_ONLY_TODAY == $timeFilter ||
					Models_Markets_MarketData::TIME_FILTER_TODAY_AND_TOMOROW == $timeFilter || 
					Models_Markets_MarketData::TIME_FILTER_TODAY == $timeFilter || 
					Models_Markets_MarketData::TIME_FILTER_TOMOROW == $timeFilter ||
					Models_Markets_MarketData::TIME_FILTER_DAY_AFTER_TOMOROW == $timeFilter)
			{
				//$timeWhere = Models_Markets_MarketData::getTodayToomorowTimeInterval($timeFilter);
				//$select = $select->where($timeWhere);
			}
			elseif (Models_Markets_MarketData::TIME_FILTER_1_HOUR == $timeFilter || Models_Markets_MarketData::TIME_FILTER_3_HOURS == $timeFilter || Models_Markets_MarketData::TIME_FILTER_6_HOURS == $timeFilter || Models_Markets_MarketData::TIME_FILTER_12_HOURS == $timeFilter){
				$timeWhere = Models_Markets_MarketData::getHourTimeInterval($timeFilter);
				$select = $select->where($timeWhere);
			} 
			elseif (Models_Markets_MarketData::TIME_FILTER_WEEK == $timeFilter) {
				$timeWhere = Models_Markets_MarketData::getWeekTimeInterval();
				$select = $select->where($timeWhere);
			}
			elseif (Models_Markets_MarketData::TIME_FILTER_WEEKEND == $timeFilter) {
				$timeWhere = Models_Markets_MarketData::getWeekendInterval($timeFilter);
				$select = $select->where($timeWhere);
			}
			elseif (Models_Markets_MarketData::TIME_FILTER_10_MINUTES == $timeFilter || Models_Markets_MarketData::TIME_FILTER_20_MINUTES == $timeFilter || Models_Markets_MarketData::TIME_FILTER_30_MINUTES == $timeFilter) {
				$timeWhere = Models_Markets_MarketData::getMinutesTimeInterval($timeFilter);
				$select = $select->where($timeWhere);
			}elseif (Models_Markets_MarketData::TIME_FILTER_DATETIME_RANGE == $timeFilter) {
				$timeWhere = Models_Markets_MarketData::getCustomInterval(true);
				$select = $select->where($timeWhere);
			}
			
                        ////echo  $select; exit;
			$rows  = $select->query()->fetchAll();
			$keys = array();
			foreach ($rows as $row) {
				$keys[$row['snazev']] = true;
				//$keys[$row['onazev']] = true;
				$keys[$row['unazev']] = true;
			}
			if (!empty($keys)) {
				$dict = It6_Models_Translator::translate(array_keys($keys), $lang_id, $db);
				foreach ($rows as &$row) {
					$row['snazev'] = $dict[$row['snazev']];
					//$row['onazev'] = trim($dict[$row['onazev']]);
					$row['unazev'] = $dict[$row['unazev']];
				}
			}
			
			$cache->save($rows, 'sportMenu_'.$_SESSION['lang_id']);

		//}

	 } catch ( Exception $e ) {

	 	Models_Exception_Handler::handle($e);

	 }

	 return $rows;


	}

	public function getExpiration() {
		$now = It6_Date::dbNow();

		$select = Zend_Registry::get('db')->select()
			->from(
				array('s'=>'sport'),
				array('expiration'=>
					'LEAST(sz.platna_do,
						IF(sz.platna_od > \''.$now.'\',sz.platna_od,sz.platna_do),
						IF(u.platne_od > \''.$now.'\',u.platne_od,u.platne_do))',
					'a' => 'sz.platna_do',
					'b' => 'sz.platna_od',
					'c' => 'u.platne_od',
					'd' => 'u.platne_do'))
			->join(array('u'=>'udalost'),'s.sport_id=u.sport_id')
			->join(array('o'=>'oblast'),'u.oblast_id=o.oblast_id')
			->join(
					array('sz'=>'sazky'),
					'sz.udalost_id=u.udalost_id AND sz.live = 0 AND sz.status = 0 AND sz.risk_limit > sz.risk_limit_balance')
			->join(array('t'=>'typ'),'sz.typ_id=t.typ_id')
			->where('s.zobrazeno=?',1)
			->where('sz.platna_do>=?',$now )
			->where('sz.live = 0')
			->where('sz.status=0')
			->where('sz.risk_limit > sz.risk_limit_balance')
			->where('u.zobrazeno=?',1)
			->where('t.zobrazeno=?',1)
			->where('u.platne_do>=?',$now)
			->order('expiration')
			->limit(1);

		$row  = $select->query()->fetchObject();

		return empty($row) ? 0 : It6_Date::fromDbAsTimestamp($row->expiration);

	}
	
	
	
	/**
	 * Vraci cele menu bez ohledu kolik je aktivnich sazek
	 *
	 * @param int $lang_id   ID jazyka
	 * @return array
	 */

	protected function getAllMenu($lang_id = 1){

	$now = It6_Date::dbNow();

	try{

		$db = Zend_Registry::get('db');
		$collation = It6_Models_Language::get($lang_id, 'collation', $db);
		$collation = (empty($collation) ? '' : ' COLLATE ' . $collation);

		$select = $db->select()->from(
			array('s'=>'sport'),
			array( 's.sport_id','sport_highlight'=>'s.zvyrazneni','snazev'=>'TRANSLATE(s.nazev,'. $lang_id .')','sport_pozice'=>'s.pozice',
				'o.oblast_id','onazev'=>"TRIM(BOTH from TRANSLATE(o.nazev,'. $lang_id .'))$collation",'opozice'=>'o.pozice','o.iso',
				'unazev'=>'TRANSLATE(u.nazev,'. $lang_id .')','u.udalost_id','uzvyrazneni'=>'u.zvyrazneni','upozice'=>'u.pozice'
			)
		)
		->join(array('u'=>'udalost'),'s.sport_id=u.sport_id')
		->join(array('o'=>'oblast'),'u.oblast_id=o.oblast_id')
		->where('s.zobrazeno=?',1)
		->where('u.zobrazeno=?',1)
		->where('u.platne_od<=?',$now)
		->where('u.platne_do>=?',$now)
		->order(array('sport_pozice','opozice','onazev','upozice'));

	 	$stm  = $select->query();
	 	$row = $stm->fetchAll();

	 } catch ( Exception $e ) {

	 	Models_Exception_Handler::handle($e);

	 }

	 return $row;


	}



}
