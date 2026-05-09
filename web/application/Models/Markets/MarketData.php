<?php

class Models_Markets_MarketData{

	const MARKET_DATE_ORDER_DIRECTION = 'ASC';

	const TIME_FILTER_ONLY_TODAY = 1;
	const TIME_FILTER_TODAY_AND_TOMOROW = 2;
	const TIME_FILTER_TOMOROW = 3;
	const TIME_FILTER_DAY_AFTER_TOMOROW = 4;
	const TIME_FILTER_1_HOUR = 5;
	const TIME_FILTER_3_HOURS = 6;
	const TIME_FILTER_6_HOURS = 7;
	const TIME_FILTER_12_HOURS = 8;
	const TIME_FILTER_WEEKEND = 9;
	const TIME_FILTER_WEEK = 10;
	const TIME_FILTER_TODAY = 11;
	const TIME_FILTER_10_MINUTES = 12;
	const TIME_FILTER_20_MINUTES = 13;
	const TIME_FILTER_30_MINUTES = 14;
	const TIME_FILTER_DATETIME_RANGE = 15;
	/**
	 * typ pole znovelych hodnot
	 * @access private
	 * @var array
	 */
	private static $openUrl = array();

	/**
	 * retezec pro cachovani dat
	 * @access private
	 * @var string
	 */
	private static $cacheString = '';

	/**
	 * data z databaze
	 * @access private
	 * @var array
	 */
	private static $marketData = array();

	/**
	 * data z databaze typy
	 * @access private
	 * @var array
	 */
	private static $typetData = array();

	/**
	 * doslo k nahrani specialnich dat
	 * @access private
	 * @var bool
	 */
	private static $isException = false;




	/**
	 * doslo k nahrani specialnich dat zde je podminka
	 * @access private
	 * @var string
	 */
	private static $whereException = '';

	/**
	 * id sazky pro detail
	 * @access private
	 * @var integer
	 */
	private static $bet_id = null;


	public static $page = null;
	public static $isLast = null;
	public static $types = null;
	public static $nextOddsUrls = null;
	private static $isAjax = false;
	
	public static $rateMin = null;
	public static $rateMax = null;
	
	public static $range = null;
	
	public static $dateFrom = null;
	public static $dateTo = null;
	
	public static function isAjax($isAjax = null) {
		if (!isset($isAjax))
			return self::$isAjax;
		else
			self::$isAjax = $isAjax;
	}

	/**
	 * najde nejblizsi hodnotu v poli
	 *
	 * @param array $openUrl   typ pole hodnot
	 * @param int $num   hledane cislo
	 */
	public static function closest($num, $arr) {
		$curr = $arr[0];
		$diff = abs($num - $curr);
		for ($val = 0; $val < count($arr); $val++) {
			$newdiff = abs($num - $arr[$val]);
			if ($newdiff < $diff) {
				$diff = $newdiff;
				$curr = $arr[$val];
			}
		}
		return $curr;
	}

	/**
	 * nastavi promenne
	 *
	 * @param array $openUrl   typ pole znovelych hodnot
	 * @param int $bet_id   id sazky
	 */
	public static function init($openUrlData, $bet_id = null, $dateOrder = null, $timeFilter = null, $types = null, $detailView = FALSE){
		self::$range = explode(',', Models_Helpers_Panels::getRangeValues());

		if ( null == $types )
			self::$types = isset($_REQUEST['type']) && is_array($_REQUEST['type']) ? $_REQUEST['type'] : array();

		self::$openUrl	= $openUrlData;
		self::$bet_id	= $bet_id;
		self::$rateMin = str_replace(",", ".", isset($_SESSION["rateMin"]) ? self::closest($_SESSION["rateMin"], self::$range) : null);
		self::$rateMax = str_replace(",", ".", isset($_SESSION["rateMax"]) ? self::closest($_SESSION["rateMax"], self::$range) : null);
		
		self::setDateTimeRange();
		
		foreach (self::$openUrl as $h) {
			if($h != 0) self::$cacheString .= $h;
		}

		if($dateOrder === null)
			$dateOrder = self::MARKET_DATE_ORDER_DIRECTION;

		self::loadMarkets($dateOrder, $timeFilter, $detailView);
	}

	/**
	 * Vraci spojene sazky
	 * @param int $bet_id  id sazky
	 * @return integer
	 */
	protected static function plusReturn($bet_id){
/*
		$bet = array();
		$select =
			Zend_Registry::get('db')
			->select()
			->from(array('sk'=>'sazka_kombinace'),array('sazka1_id' ,'sazka2_id'))
			->join(
				array('s1'=>'sazky'),
				's1.sazka_id = sk.sazka1_id AND s1.platna_od < "'.It6_Date::dbNow().'" AND s1.platna_do > "'.It6_Date::dbNow().'" AND s1.status=0',
				array('platna_od','platna_od','status')
			)
			->join(
				array('s2'=>'sazky'),
				's2.sazka_id = sk.sazka2_id AND s2.platna_od < "'.It6_Date::dbNow().'" AND s2.platna_do > "'.It6_Date::dbNow().'" AND s2.status=0',
				array('platna_od','platna_od','status')
			)
			->where('sazka1_id=?',$bet_id)
			->orWhere('sazka2_id=?',$bet_id)
			->where('kombinace_show=1');

//		var_dump($select->assemble());exit;
		$row = $select->query()->fetchAll();

		foreach($row as $h){
			$bet[] = $h['sazka1_id'];
			$bet[] = $h['sazka2_id'];
		}

		return implode(',',$bet);
*/
		return It6_Models_Bet::readRealCorrelatedBets($bet_id,'list');
	}



	/**
	 * Vraci jmeno tymu spojene sazky
	 * @param int $bet_id  id sazky
	 * @return integer
	 */
	public static function getName(){
		if(self::$bet_id == null)
			return;

		$row = Zend_Registry::get('db')->select()
			->from(array('sazky'),array('text'))
			->where('sazka_id=?',self::$bet_id)
			->query()->fetchAll();

		return $row[0]['text'];
	}



	/**
	 * Zde se handluji Dnesni nabidky apod.
	 *
	 */

	public static function loadExceptions(){

		//TODO: neslo by toto radsi napojit na Models_Helpers_MenuExceptions?
		#Dnesni nabidka#
		if(Zend_Registry::get('c_id') == 4){

			self::$openUrl['sport'] = 0;
			self::$openUrl['oblast'] = 0;
			self::$openUrl['udalost'] = 0;
			self::$cacheString = 'c_id'.Zend_Registry::get('c_id');
			self::$isException = true;
			self::$whereException = 'DATE_FORMAT(sz.platna_do,"%Y-%m-%d")=DATE_FORMAT(NOW(),"%Y-%m-%d")';

		}
		#Dnes a zitra#
		else if(Zend_Registry::get('c_id') == 5){

			self::$openUrl['sport'] = 0;
			self::$openUrl['oblast'] = 0;
			self::$openUrl['udalost'] = 0;
			self::$cacheString = 'c_id'.Zend_Registry::get('c_id');
			self::$isException = true;
			self::$whereException = '(DATE_FORMAT(sz.platna_do,"%Y-%m-%d")=DATE_FORMAT(NOW(),"%Y-%m-%d") or DATE_FORMAT(sz.platna_do,"%Y-%m-%d")=DATE_ADD(DATE_FORMAT(NOW(),"%Y-%m-%d"),INTERVAL 1 DAY))';

		}
		#Fotbal dnes#
		else if(Zend_Registry::get('c_id') == 6){

			self::$openUrl['sport'] = 0;
			self::$openUrl['oblast'] = 0;
			self::$openUrl['udalost'] = 0;
			self::$cacheString = 'c_id'.Zend_Registry::get('c_id');
			self::$isException = true;
			self::$whereException = 'DATE_FORMAT(sz.platna_do,"%Y-%m-%d")=DATE_FORMAT(NOW(),"%Y-%m-%d") and s.sport_id=1001';

		}
		#Tenis dnes#
		else if(Zend_Registry::get('c_id') == 7){

			self::$openUrl['sport'] = 0;
			self::$openUrl['oblast'] = 0;
			self::$openUrl['udalost'] = 0;
			self::$cacheString = 'c_id'.Zend_Registry::get('c_id');
			self::$isException = true;
			self::$whereException = 'DATE_FORMAT(sz.platna_do,"%Y-%m-%d")=DATE_FORMAT(NOW(),"%Y-%m-%d") and s.sport_id=1003';

		}
		#Detail#
		else if(Zend_Registry::get('c_id') == 9 && self::$bet_id != null){

			self::$openUrl['sport'] = 0;
			self::$openUrl['oblast'] = 0;
			self::$openUrl['udalost'] = 0;
			self::$cacheString = 'c_id'.Zend_Registry::get('c_id') .'_'. self::$bet_id ;
			self::$isException = true;
			$list = trim(self::plusReturn(self::$bet_id));
			if (empty($list))
				self::$whereException = '0=1';
			else
				self::$whereException = "sz.sazka_id IN ($list) AND sz.sazka_id <> " . self::$bet_id ;
		}
		#Search#
		else if (Zend_Registry::get('c_id') == 45) {
			self::$openUrl['sport'] = 0;
			self::$openUrl['oblast'] = 0;
			self::$openUrl['udalost'] = 0;
			self::$cacheString = 'c_id'.Zend_Registry::get('c_id') .'_'. md5($_GET['query']);
			self::$isException = true;

			//self::$whereException = 'sz.sazka_id IN (SELECT sazka_id FROM sazky_search WHERE MATCH(sazka_text) AGAINST(\''. Models_Helpers_Help::slash($_GET['query']) .'*\' IN BOOLEAN MODE))';
			$search = $_GET['query'];
			if (mb_strlen($search) > 3)
				self::$whereException = 'sz.sazka_id IN (SELECT sazka_id FROM sazky WHERE `text` LIKE \'%'. Models_Helpers_Help::slash($search) .'%\')';
			else
				self::$whereException = '0=1';
//			var_dump(self::$whereException );
		}

		if (isset(self::$openUrl['sport']) && self::$openUrl['sport'] != 0 && ctype_digit(self::$openUrl['sport'])) self::$cacheString .=  '_S'.self::$openUrl['sport'];
		if (isset(self::$openUrl['oblast']) && self::$openUrl['oblast'] != 0 && ctype_digit(self::$openUrl['oblast'])) self::$cacheString .=  '_O'.self::$openUrl['oblast'];
		if (isset(self::$openUrl['udalost']) && self::$openUrl['udalost'] != 0 && ctype_digit(self::$openUrl['udalost'])) self::$cacheString .=  '_U'.self::$openUrl['udalost'];
	}

	/**
	 * nahraje vsechna potrebna data k trhum vcetne kurzu
	 * @return array
	 */
	private static function loadMarkets($dateOrder, $timeFilter, $detailView = FALSE){
		
		if (!$timeFilter) {
			if (strpos($_SERVER["HTTP_REFERER"],'today=1') !== false) {
				$timeFilter = self::TIME_FILTER_TODAY; //11
			}
			if (strpos($_SERVER["HTTP_REFERER"],'tomorow=1') !== false) {
				$timeFilter = self::TIME_FILTER_TOMOROW; //3
			}
			if (strpos($_SERVER["HTTP_REFERER"],'todayAndTomorow=1') !== false) {
				$timeFilter = self::TIME_FILTER_TODAY_AND_TOMOROW; //2
			}			
		}
		//echo "<br/>timefilter:".$timeFilter."<br/>";

		$now = It6_Date::dbNow();
		$db = Zend_Registry::get('db');
		try{
			self::loadExceptions();

			//if(!self::$isException && self::$openUrl['sport'] == 0)
			//	return false;

			$fillInTypes = empty(self::$types);
			$langIso = $_SESSION['lang'];
			$langId = $_SESSION['lang_id'];
			$langColSql = trim($_SESSION['lang_collation']);
			if (!empty($langColSql))
				$langColSql = " COLLATE $langColSql";
			$rows = array();
			$page = empty($_GET['page']) ? 0 : $_GET['page'];
			$page = ltrim($page,'P-');
			$count = empty($_GET['page']) ? 15 : 15;
			$isLast = false;
			$timeWhere = null;
			$stopper = true;
			while ( true && $stopper ) {

				if ($detailView) $stopper = false;

				$select = $db->select()
					->from(array('u'=>'udalost'), array())
					->join(
							array('s'=>'sport'),
							's.sport_id=u.sport_id AND s.zobrazeno = 1',
							array())
					->join(
							array('o'=>'oblast'),
							'o.oblast_id=u.oblast_id',
							array())
					->joinLeft(
							array('tro' => 'preklady'),
							'o.nazev=tro.index_pole AND tro.lang_id=' . intval($langId),
							array())
					->join(
							array('sz'=>'sazky'),
							'u.udalost_id=sz.udalost_id AND sz.live=0 AND sz.status=0 AND sz.risk_limit > sz.risk_limit_balance',
							array())
					->join(
							array('t'=>'typ'),
							'sz.typ_id=t.typ_id AND t.zobrazeno = 1',
							array()
						)
					->join(
							array('tu'=>'typ_udalost'),
							'tu.typ_id=t.typ_id AND tu.udalost_id=u.udalost_id',
							array()
						)
					->where('sz.live=0')
					->where('sz.status=0')
					->where('sz.platna_do >= ?',$now)
					->where('sz.platna_od <=?', $now )

					->where('sz.risk_limit > sz.risk_limit_balance')
					->where('u.platne_od<=?',$now)
					->where('u.platne_do>=?',$now )
					->where('u.zobrazeno = 1')
					->group('u.udalost_id')
					->order(array('s.pozice','o.pozice','onazev','u.pozice'))

					->columns(array('u.udalost_id','onazev'=>"(TRIM(COALESCE(tro.text,o.nazev))$langColSql)"));

				if (isset($_GET['betIdFilter'])) {
					$betIdFilter = intval($_GET['betIdFilter']);
					$select = $select->where("sz.sazka_id = $betIdFilter");
				}
		
				if (static::TIME_FILTER_ONLY_TODAY == $timeFilter ||
						static::TIME_FILTER_TODAY_AND_TOMOROW == $timeFilter || 
						static::TIME_FILTER_TODAY == $timeFilter || 
						static::TIME_FILTER_TOMOROW == $timeFilter ||
						static::TIME_FILTER_DAY_AFTER_TOMOROW == $timeFilter)
				{
					$timeWhere = self::getTodayToomorowTimeInterval($timeFilter);
					$select = $select->where($timeWhere);
				}
				elseif (static::TIME_FILTER_1_HOUR == $timeFilter || static::TIME_FILTER_3_HOURS == $timeFilter || static::TIME_FILTER_6_HOURS == $timeFilter || static::TIME_FILTER_12_HOURS == $timeFilter){
					$timeWhere = self::getHourTimeInterval($timeFilter);
					$select = $select->where($timeWhere);
				} 
				elseif (static::TIME_FILTER_WEEK == $timeFilter) {
					$timeWhere = self::getWeekTimeInterval();
					$select = $select->where($timeWhere);
				}
				elseif (static::TIME_FILTER_WEEKEND == $timeFilter) {
					$timeWhere = self::getWeekendInterval($timeFilter);
					$select = $select->where($timeWhere);
				}
				elseif (static::TIME_FILTER_10_MINUTES == $timeFilter || static::TIME_FILTER_20_MINUTES == $timeFilter || static::TIME_FILTER_30_MINUTES == $timeFilter) {
					$timeWhere = self::getMinutesTimeInterval($timeFilter);
					$select = $select->where($timeWhere);
				}elseif (static::TIME_FILTER_DATETIME_RANGE == $timeFilter) {
					$timeWhere = self::getCustomInterval();
					$select = $select->where($timeWhere);
				}

				if(self::$isException)
					$select =  $select->where(self::$whereException);
				if(self::$openUrl['sport'] != 0   && ctype_digit(self::$openUrl['sport']))
					$select =  $select->where('s.sport_id=?',self::$openUrl['sport']);
				if(self::$openUrl['oblast'] != 0  && ctype_digit(self::$openUrl['oblast']))
					$select =  $select->where('o.oblast_id=?',self::$openUrl['oblast']);
				if(self::$openUrl['udalost'] != 0)
					$select =  $select->where('u.udalost_id=?',intval(self::$openUrl['udalost']));
				if( !empty(self::$types) )
					$select =  $select->where('t.typ_id IN (?)', self::$types);
				else {
					if (!$detailView)
						$select =  $select->where('tu.is_default = 1');
				}

				if (self::$isAjax && !$detailView)
					$select->limit(1, $page);
                                $query = $select->query();

				$events = array();
				if ($event = $query->fetch()) {
					$events[] = $event['udalost_id'];
				}

				if (!self::$isAjax) {
					$nextEvents = $events;
					while ($event = $query->fetch())
						$nextEvents[] = $event['udalost_id'];
					self::$nextOddsUrls = array();
					if (!empty($nextEvents)) {
						$nextEventUrls = It6_Models_Sportsbook::getEventUrl($nextEvents, true, $db);
						foreach ($nextEvents as $id) {
							if (!empty($nextEventUrls[$id]))
								self::$nextOddsUrls[] = $nextEventUrls[$id];
						}
					}
				}

				if ( empty($events) ) {
					$isLast = true;
					break;
				}

				if (!self::$isAjax && !$detailView)
					break; // we won't fetch any matches, AJAX wil do it in lazy way

				if ( !empty($rows) && count($rows) >= $count ) {
					break;
				}

					$select = $db->select()
						->from(
							array('sz'=>'sazky'),
							array(
								's.sport_id',
								'u.oblast_id',
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
								'szAlias' => 'alias',
								'szAliasNew' => 'alias_new',
								'parent_id' => 'sz.parent_id',
								'realTypeId' => 't2.typ_id',
								't2.typ_alias_id',
								'typeOrder' => 'tot.name',
								'p.radek_sloupec',
								'p.sloupec_pocet_max',
								'liveBetId' => 'ml.id'
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
							'o.nazev=tro.index_pole AND tro.lang_id=' . intval($langId),
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
						->joinLeft	(
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
						->where('sz.platna_do >= ?',$now)							
						->where('sz.risk_limit > sz.risk_limit_balance')
						->where('u.platne_od<=?',$now)
						->where('u.platne_do>=?',$now)
						->where('u.udalost_id IN (?)',$events)
						//->order('sz.udalost_id')
						->order('s.pozice')
						->order('o.pozice')
						->order('onazev')
						->order('u.pozice')
						->order('tu.order')
						->order('sz.platna_do '.$dateOrder)
						//->order("IF(sz.ticket_text IS NULL OR sz.ticket_text='',sz.text,sz.ticket_text)")
						//->order('sz.alias')
						->order('sz.alias_new')
						->order('sz.sazka_id')
						->order('ps.poradi');
					
					if (isset($_GET['betIdFilter'])) {
						$betIdFilter = intval($_GET['betIdFilter']);
						$select = $select->where("sz.sazka_id = $betIdFilter");
					}
					
					if (self::$rateMin && self::$rateMax) {
						$select = $select->where('sk.kurz BETWEEN '.self::$rateMin.' AND '.self::$rateMax);
					}
					
					if(!is_null($timeWhere))
						$select = $select->where($timeWhere);

					if(self::$isException)
						$select =  $select->where(self::$whereException);
					if(self::$openUrl['sport'] != 0   && ctype_digit(self::$openUrl['sport']))
						$select =  $select->where('s.sport_id=?',self::$openUrl['sport']);
					if(self::$openUrl['oblast'] != 0  && ctype_digit(self::$openUrl['oblast']))
						$select =  $select->where('o.oblast_id=?',self::$openUrl['oblast']);
					if(self::$openUrl['udalost'] != 0)
						$select =  $select->where('u.udalost_id=?',intval(self::$openUrl['udalost']));
					if( !empty(self::$types) )
						$select = $select->where('t.typ_id IN (?)', self::$types);
					else {
						if (!$detailView)
							$select =  $select->where('tu.is_default = 1');
					}
					$selectFirstTry = $select->where('tu.is_binded = 1');
					
					//$timeWhere = self::getTodayToomorowTimeInterval($timeFilter);
					//$select = $select->where($timeWhere);
					
					//echo "<br/>".$timeFilter."-".$select."<br/>";

					$rows2 = $select->query()->fetchAll();

					if (!empty($rows2))
						$rows = array_merge($rows, $rows2);

					$page++;
				//}
				break;
			}

				//FIXME: this is bad, but Im not sure what depends on this so I do not want to change the output format of this method

			$dateCache = array();
			$dict = array();
			foreach($rows as &$event) {
				if ( !array_key_exists( $event['platna_do'], $dateCache) )
					$dateCache[$event['platna_do']] = array(
							It6_Date::fromDbAsTime($event['platna_do'], It6_Date::PART_TIME_SHORT),
							It6_Date::fromDbAsDate($event['platna_do'])
						);

				$event['dbDateTime'] = $event['platna_do'];
				$event['hour'] = $dateCache[$event['platna_do']][0];
				$event['platna_do'] = $dateCache[$event['platna_do']][1];

				if ( $fillInTypes && !in_array($event['typ_id'],self::$types) )
					self::$types[] = $event['typ_id'];

				$dict[$event['snazev']] = true;
				//$dict[$event['onazev']] = true;
				$dict[$event['unazev']] = true;
				$dict[$event['tnazev']] = true;
				$dict[$event['pnazev']] = true;
				$dict[$event['ptext']] = true;
			}
			unset($dateCache);
			$dict = It6_Models_Translator::translate(array_keys($dict), $_SESSION['lang_id'], $db);

			foreach ($rows as &$event) {
				$event['snazev'] = $dict[$event['snazev']];
				//$event['onazev'] = trim($dict[$event['onazev']]);
				$event['unazev'] = $dict[$event['unazev']];
				$event['tnazev'] = $dict[$event['tnazev']];
				$event['pnazev'] = $dict[$event['pnazev']];
				$event['ptext'] = $dict[$event['ptext']];
			}

			self::$page = $page;
			self::$isLast = $isLast;

		}
		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}

		// complex ordering according to typ.order_type_id (vic_main.typ_order_type), now support only for 'rate_asc'
		$grouped = array();
		$typeOrders = array();
		foreach ($rows as $row) {
			$typeId = $row['typ_id'];
			if (!empty($row['typeOrder']))
				$typeOrders[$typeId] = $row['typeOrder'];
			$grouped[$row['sport_id']][$row['udalost_id']][$typeId][$row['sazka_id']][] = $row;
		}
		if (!empty($typeOrders)) {
			// $fields is priority list for ordering, fields are compared alphanumerically,
			// first not equal wins. Fields must be present keys in $a and $b structures.
			$fnOrder = function($a, $b, array $fields) {
				foreach ($fields as $field) {
					$va = $a[$field];
					$vb = $b[$field];
					if ($va != $vb) {
						return ($va < $vb ? -1 : 1);
					}
				}
				return 0;
			};
			$fnOrderRateAsc = function($a, $b) use ($fnOrder) {
				return $fnOrder($a[0], $b[0], array('dbDateTime', 'kurz'));
			};
			$fnOrderTextAsc = function($a, $b) use ($fnOrder) {
				$cmpA = array(
					'time' => $a[0]['dbDateTime'],
					'threshold' => $a[0]['mtext'],
				);
				$cmpB = array(
					'time' => $b[0]['dbDateTime'],
					'threshold' => $b[0]['mtext'],
				);
				return $fnOrder($cmpA, $cmpB, array('time', 'threshold'));
			};
			$fnOrderAliasRateAsc = function($a, $b) use ($fnOrder) {
				return $fnOrder($a[0], $b[0], array('dbDateTime', 'szAliasNew', 'kurz'));
			};
			$fnOrderThreshold = function($a, $b) use($fnOrder) {
				if (preg_match('/\\s+(\\S+)$/', trim($a[0]['mtext']), $matches)) {
					$threshA = floatval(str_replace(',', '.', $matches[1]));
				} else {
					$threshA = 0;
				}
				if (preg_match('/\\s+(\\S+)$/', trim($b[0]['mtext']), $matches)) {
					$threshB = floatval(str_replace(',', '.', $matches[1]));
				} else {
					$threshB = 0;
				}
				$cmpA = array(
					'time' => $a[0]['dbDateTime'],
					'alias' => $a[0]['szAliasNew'],
					'threshold' => $threshA,
				);
				$cmpB = array(
					'time' => $b[0]['dbDateTime'],
					'alias' => $b[0]['szAliasNew'],
					'threshold' => $threshB,
				);
				return $fnOrder($cmpA, $cmpB, array('time', 'alias', 'threshold'));
			};
			$orderMap = array(
				'rate_asc' => $fnOrderRateAsc,
				'alias_rate_asc' => $fnOrderAliasRateAsc,
				'threshold_asc' => $fnOrderThreshold,
				'text_asc' => $fnOrderTextAsc
			);
			$rows2 = array();
			foreach ($grouped as $sportId => $_sport) {
				foreach ($_sport as $eventId => $_event) {
					foreach ($_event as $typeId => &$_bets) {
						if (!empty($typeOrders[$typeId])) {
							$order = $typeOrders[$typeId];
							if (isset($orderMap[$order]))
								usort($_bets, $orderMap[$order]);
						}
						foreach ($_bets as $_rows)
							$rows2 = array_merge($rows2, $_rows);
					}
				}
			}
			self::$marketData = $rows2;
		}
		else
			self::$marketData = $rows;
	}



	/**
	 * Vraci data pro naplneni tiketu na strankach
	 * @param int $bet_id  id sazky
	 * @param int $id_col  id sloupce
	 * @return array
	 */
	public static function getBetTicketData($bet_id, $col_id) {
		$now	= It6_Date::dbNow();
		$langId	= $_SESSION['lang_id'];

		try{
			$row = Zend_Registry::get('db')->select()
				->from(
					array('sz'=>'sazky'),
					array(
						'simple' => 'sz.jednoducha',
						'mtext' => "IF(sz.text IS NULL OR sz.text = '', sz.ticket_text, sz.text)",
						'ako',
						'tnazev' => 'TRANSLATE(t.nazev,'.$langId.')',
						'textNote' => 'sz.text_note',	
						'pnazev' => 'TRANSLATE(ps.nazev,'.$langId.')',
					)
				)
				->join(
					array('ska'=>'sazka_kurz_aktualni'),
					'sz.sazka_id = ska.sazka_id'
				)
				->join(
					array('t' => 'typ'),
					'sz.typ_id = t.typ_id'	
				)
				->join(
					array('ps' => 'podtyp_sloupce'),
					'ska.sloupec_id = ps.sloupec_id'
				)
				->where('ska.sazka_id=?', $bet_id)
				->where('ska.sloupec_id=?', $col_id)
				->query()->fetch();
				
			return $row;
		}

		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
	}



	/**
	 * Vraci kurzy
	 *
	 * @return array
	 */
	public static function getOdds(){
		$bets = array();
		foreach(self::$marketData as $h){

			if(!isset($bets[$h['sport_id']][$h['udalost_id']])){
				$bets[$h['sport_id']][$h['udalost_id']] = array(
					'oblast_id' => $h['oblast_id'],
					'text' => mb_strtoupper($h['snazev']) . " - {$h['onazev']} - {$h['unazev']}",
					'betradar_udalost_id' => $h['betradar_udalost_id'],
				);
			}
			$ptr = &$bets[$h['sport_id']][$h['udalost_id']];

			if(!isset($ptr['type'][$h['typ_id']][$h['podtyp_id']])){
				$ptr['type'][$h['typ_id']][$h['podtyp_id']] = array(
					'text' => $h['tnazev'],
					'textNote' => $h['textNote'],
					'fullText' => $h['tnazev'] . (!empty($h['betTextNote']) ? " {$h['betTextNote']}" : ''),
					'radek_sloupec' => $h['radek_sloupec'],
					'sloupec_pocet_max' => $h['sloupec_pocet_max'],
				);
			}
			$ptr = &$ptr['type'][$h['typ_id']][$h['podtyp_id']];

			if(!isset($ptr['sloupec'][$h['sloupec_id']]))
				$ptr['sloupec'][$h['sloupec_id']] = $h['pnazev'];

			//$h['platna_do'] = It6_Date::fromDbAsDate($h['platna_do']);

			if(!isset($ptr['date'][$h['platna_do']][$h['sazka_id']])) {
				$ptr['date'][$h['platna_do']][$h['sazka_id']] = array(
					'hour' => $h['hour'],
					'text' => $h['mtext'],
					'info' => $h['info'],
					'alias' => $h['szAlias'].str_pad($h['typ_alias_id'],2,'0',STR_PAD_LEFT),
					'alias_new' => $h['szAliasNew'],
					'simple' => $h['jednoducha'],
					'ako' => $h['ako'],
					'plus' => self:: plusShow($h['sazka_id']),
					//'betradar_sazka_id' => $h['betradar_sazka_id'],
					'betradar_match_id' => $h['betradar_match_id'],
					'parent_id' => $h['parent_id'],
					'liveBetId' => $h['liveBetId'],
				);
			}

			$h['kurz'] =  $h['kurz'] * pow(10, 2) / pow(10, 2);

			$ptr['date'][$h['platna_do']][$h['sazka_id']]['sloupec'][$h['sloupec_id']] = $h['kurz'];
		}
		return $bets;
	}


	/**
	 * Vraci pocet kombinaci viditelnych
	 * @param int $bet_id  id sazky
	 * @return integer
	 */
	public static function plusShow($bet_id){
		return It6_Models_Bet::readRealCorrelatedBets($bet_id,'count');
	}



	/**
	 * Vraci zvolene druhy
	 *
	 * @return array
	 */
	public static function getTypes($timeFilter = null){
		
                $now = It6_Date::dbNow();
		try{
                    
			$frontendOptions = array(
				'lifetime' => MARKETS_CACHE_LIFETIME, // cache lifetime of 2 hours
				'automatic_serialization' => true
			);
			$backendOptions = $GLOBALS['ZEND_CACHE_BACKEND_OPTIONS'];

			//$cache = Zend_Cache::factory('Core','File',$frontendOptions,$backendOptions);

			//if(!$row = $cache->load('type_'.$_SESSION['lang_id'].self::$cacheString)) {

			$select = Zend_Registry::get('db')->select()
				->from(array('sz'=>'sazky'),array(
					'tuvychozi'=>'tu.is_default',
					'pocet'=>'count(sz.typ_id)',
					'tnazev'=>'t.nazev',
					't.typ_id'
				))
				->join(array('t'=>'typ'),'sz.typ_id=t.typ_id')
				->join(array('u'=>'udalost'),'sz.udalost_id=u.udalost_id')
				->join(array('s'=>'sport'),'s.sport_id=u.sport_id')
				->join(array('o'=>'oblast'),'o.oblast_id=u.oblast_id')
				->join(array('tu'=>'typ_udalost'),'tu.typ_id=t.typ_id and tu.udalost_id=u.udalost_id')
				->where('sz.live=?',0)
				->where('sz.status=?',0)
				->where('sz.platna_od<=?',$now)
				->where('sz.platna_do>=?', $now)
				->where('sz.risk_limit > sz.risk_limit_balance')
				->where('tu.is_binded=1')
				->group('t.typ_id')
				->order(array('t.poradi'));

				if ( empty($timeFilter) )
					$select = $select;
				else if($timeFilter == static::TIME_FILTER_ONLY_TODAY)
					$select = $select->where('DATE(sz.platna_do) = ?', It6_Date::dbNowAsDate());

				else if($timeFilter == static::TIME_FILTER_TODAY_AND_TOMOROW)
					$select = $select->where('(DATE_FORMAT(sz.platna_do,"%Y-%m-%d")=DATE_FORMAT(NOW(),"%Y-%m-%d") or DATE_FORMAT(sz.platna_do,"%Y-%m-%d")=DATE_ADD(DATE_FORMAT(NOW(),"%Y-%m-%d"),INTERVAL 1 DAY))');
				else
				if(self::$isException)
					$select =  $select->where(self::$whereException);
				if(self::$openUrl['sport'] != 0   && ctype_digit(self::$openUrl['sport']))
					$select =  $select->where('s.sport_id=?',self::$openUrl['sport']);
				if(self::$openUrl['oblast'] != 0  && ctype_digit(self::$openUrl['oblast']))
					$select =  $select->where('o.oblast_id=?',self::$openUrl['oblast']);
				if(self::$openUrl['udalost'] != 0 && ctype_digit(self::$openUrl['udalost']))
					$select =  $select->where('u.udalost_id=?',self::$openUrl['udalost']);
                                
                                //echo  $select; exit; 
                                
                                // which gives exact mysql query.
                                $row  = $select->query()->fetchAll();
				//$cache->save($row, 'type_'.$_SESSION['lang_id'].self::$cacheString);
			//}
		}
		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}

		self::$typetData = $row;
		return self::$typetData;
	}



	/**
	 * nahraje vsechna potrebna data k lastminute sazkam
	 * sazky definovane bookmakerem
	 *
	 * @return array
	 */
	public static function loadLastMarkets(){

		$now = It6_Date::dbNow();

		try{
			//NOTE: we could spare some time by translating not with TRANSLATE() stored proc
		    //      but later using It6_Models_Translator
			$stmt = Zend_Registry::get('db')->query(
"SELECT
 sz.*,
 TRANSLATE(sz.snazev," . $_SESSION['lang_id'] . ") AS snazev,
 TRANSLATE(sz.unazev," . $_SESSION['lang_id'] . ") AS unazev,
 TRANSLATE(o.nazev," . $_SESSION['lang_id'] . ") AS onazev,
 TRANSLATE(t.nazev," . $_SESSION['lang_id'] . ") AS tnazev,
 TRANSLATE(p.text," . $_SESSION['lang_id'] . ") AS ptext,
 TRANSLATE(ps.nazev," . $_SESSION['lang_id'] . ") AS pnazev,
 k.sloupec_id,
 k.kurz
FROM (
 SELECT
  s.sazka_id,
  s.alias_new,
  s.typ_id,
  s.podtyp_id,
  s.platna_do,
  s.jednoducha,
  IF(s.ticket_text IS NULL OR s.ticket_text='', s.`text`, s.ticket_text) AS `mtext`,
  s.text_note,
  s.udalost_id,
  s.parent_id,
  s.platna_do AS `date`,
  u.oblast_id,
  u.sport_id,
  u.nazev AS unazev,
  sp.nazev AS snazev,
  s.ako,
  s.info 
 FROM sazky s
 JOIN udalost u
 ON u.udalost_id=s.udalost_id AND u.zobrazeno=1
 JOIN sport sp
 ON sp.sport_id=u.sport_id AND sp.zobrazeno=1
 WHERE
  typ_id IN (19, 22)
  AND s.platna_od<='$now'
  AND s.platna_do>='$now'
  AND s.status=0
  AND s.risk_limit>s.risk_limit_balance
 ORDER BY typ_id ASC, platna_do ASC, sazka_id DESC
 LIMIT 10
) sz
JOIN sazka_kurz_aktualni k
ON sz.sazka_id=k.sazka_id
JOIN typ t
ON t.typ_id=sz.typ_id
JOIN podtyp p
ON p.podtyp_id=sz.podtyp_id
JOIN podtyp_sloupce ps
ON k.sloupec_id=ps.sloupec_id
JOIN oblast o
ON o.oblast_id=sz.oblast_id
ORDER BY sz.typ_id ASC, sz.platna_do ASC, sz.sazka_id DESC, k.poradi"
			);
		}
		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}

		$data	= array();
		$betIds	= array();
		while ($h = $stmt->fetch()){
			if(in_array($h['sazka_id'], $betIds) === false) {
				if(!isset($data[$h['sazka_id']])) {
					$t = It6_Date::fromDbAsTimestamp($h['date']);
					$data[$h['sazka_id']] = array(
						'sid'           => $h['sport_id'],
						'snazev'		=> $h['snazev'],
						'unazev'		=> $h['unazev'],
						'onazev'		=> $h['onazev'],
						'tnazev'		=> $h['tnazev'] . (!empty($h['text_note']) ? " {$h['text_note']}" : ''),
						'ptext'			=> $h['ptext'],
						'typ_id'		=> $h['typ_id'],
						'podtyp_id'		=> $h['podtyp_id'],
						'jednoducha'	=> $h['jednoducha'],
						'mtext' 		=> $h['mtext'],
						'date' 			=> It6_Date::timestampToDateTimeShort($t),
						'timestamp' 	=> $t,
						'ako'			=> $h['ako'],
						'info'			=> $h['info'],
						'aliasNew'		=> $h['alias_new']
					);

					if(empty($h['parent_id']))
						$betIds[] = $h['sazka_id'];
					else
						$betIds[] = $h['parent_id'];
				}
			}
			
			if(isset($data[$h['sazka_id']])) {
				if (
					(empty($data[$h['sazka_id']]['sloupec']) || 3 > count($data[$h['sazka_id']]['sloupec']))
					&& !isset($data[$h['sazka_id']]['sloupec'][$h['sloupec_id']])
				) {
					$data[$h['sazka_id']]['sloupec'][$h['sloupec_id']]['rate'] = $h['kurz'];
					$data[$h['sazka_id']]['sloupec'][$h['sloupec_id']]['pnazev'] = $h['pnazev'];
				}
			}
		}

		return $data;
	}
	
	/**
	 * name: getTodayToomorowTimeinterval
	 * @param int $timeFilter Spacifies for which period we want the time frame. Takes values of:
	 * <ul>
	 * <li>static::TIME_FILTER_ONLY_TODAY</li>
	 * <li>static::TIME_FILTER_TODAY_AND_TOMOROW</li>
	 * </ul>
	 * @return string The sql code to be used in a where staement 
	 */
	public static function getTodayToomorowTimeInterval($timeFilter) {
		$start = date("Y-m-d H:i:s",time());
		
		$dbEndDayTime = Zend_Registry::get('ws')->Parameter->getGlobalparameter('offer.today.timeFrame.end');
		$dbEndDayTimeArr = explode(':', $dbEndDayTime);
		if (empty($dbEndDayTimeArr[1])) $dbEndDayTimeArr[1] = '00';
		if (empty($dbEndDayTimeArr[2])) $dbEndDayTimeArr[2] = '00';
			
		if (strpos($_SERVER["HTTP_REFERER"],'?all') !== false) {
			$timeWhere = "sz.platna_do >= '$start'";
			return $timeWhere;
		}
		if (strpos($_SERVER["HTTP_REFERER"],'today=1') !== false) {
			$timeFilter = self::TIME_FILTER_TODAY; //11
		}
		if (strpos($_SERVER["HTTP_REFERER"],'tomorow=1') !== false) {
			$timeFilter = self::TIME_FILTER_TOMOROW; //3
		}
		if (strpos($_SERVER["HTTP_REFERER"],'todayAndTomorow=1') !== false) {
			$timeFilter = self::TIME_FILTER_TODAY_AND_TOMOROW; //2
		}

		switch ($timeFilter) {
			case static::TIME_FILTER_ONLY_TODAY:
			case static::TIME_FILTER_TODAY: // 11
				$start = date("Y-m-d H:i:s",time());
				$end = date("Y-m-d H:i:s",mktime(
								 $dbEndDayTimeArr[0],
								 $dbEndDayTimeArr[1],
								 $dbEndDayTimeArr[2],
								 date('m', strtotime($start)),
								 date('d', strtotime($start))+1,
								 date('Y', strtotime($start))));
				break;
			case static::TIME_FILTER_TOMOROW: // 3
				$start = date("Y-m-d H:i:s",mktime(0,
												   0,
								                   0,
								                   date('m', time()),
								                   date('d', time())+1,
								                   date('Y', time())));
				//$start = strtotime($start);

				$end = date("Y-m-d H:i:s",mktime(
								 $dbEndDayTimeArr[0],
								 $dbEndDayTimeArr[1],
								 $dbEndDayTimeArr[2],
								 date('m', strtotime($start)),
								 date('d', strtotime($start))+1,
								 date('Y', strtotime($start))));
				break;
			case static::TIME_FILTER_TODAY_AND_TOMOROW:
				$start = date("Y-m-d H:i:s",time());
				$end = date("Y-m-d H:i:s",mktime(
								 $dbEndDayTimeArr[0],
								 $dbEndDayTimeArr[1],
								 $dbEndDayTimeArr[2],
								 date('m', strtotime($start)),
								 date('d', strtotime($start))+2,
								 date('Y', strtotime($start))));
				break;
			case static::TIME_FILTER_DAY_AFTER_TOMOROW:
				$start = date("Y-m-d H:i:s",time());
				$end = date("Y-m-d H:i:s",mktime(
								 $dbEndDayTimeArr[0],
								 $dbEndDayTimeArr[1],
								 $dbEndDayTimeArr[2],
								 date('m', strtotime($start)),
								 date('d', strtotime($start))+1,
								 date('Y', strtotime($start))));
				break;
		}

//echo "<br/>TT:".$timeFilter."<br/>";
//echo "start:".date("Y-m-d H:i:s", $start);			
//echo "start:".$start;
//echo "end:".$end;
	
		$timeWhere = "sz.platna_do BETWEEN '$start' AND '$end'";
//echo "<br/>***".$timeWhere."***<br/>";

		return $timeWhere;
	}
	
	public static function getWeekTimeInterval() {
		$start = It6_Date::dbNow();
		
		$dbEndDayTime = Zend_Registry::get('ws')->Parameter->getGlobalparameter('offer.today.timeFrame.end');
		$dbEndDayTimeArr = explode(':', $dbEndDayTime);
		if(empty($dbEndDayTimeArr[1])) {
			$dbEndDayTimeArr[1] = '00';
		}
		if(empty($dbEndDayTimeArr[2])) {
			$dbEndDayTimeArr[2] = '00';
		}
		$dbEndDateTime = It6_Date::dbNowAsDate(24 *3600).' '.$dbEndDayTimeArr[0].':'.$dbEndDayTimeArr[1].':'.$dbEndDayTimeArr[2];
		$endDayTime = It6_Date::fromDbAsTime($dbEndDateTime);
		$endDayTimeArr = explode(':', $endDayTime);
		$endTimeOffset = $endDayTimeArr[0] * 3600 + $endDayTimeArr[1] * 60 + $endDayTimeArr[2];
		
		$tm = localtime(time(), true);
		$end = mktime(
				23,
				59,
				59	 + $endTimeOffset,
				$tm['tm_mon'] + 1,
				$tm['tm_mday'] + 7,
				$tm['tm_year'] + 1900
		);
		$end = It6_Date::timestampToDb($end);
		
		$timeWhere = "sz.platna_do BETWEEN '$start' AND '$end'";
		return $timeWhere;
	}
	
	public static function getHourTimeInterval($timeFilter){
		$start = It6_Date::dbNow();
	
		switch ($timeFilter) {
			case static::TIME_FILTER_1_HOUR:
				$hours = 1;
				break;
			
			case static::TIME_FILTER_3_HOURS:
				$hours = 3;
				break;
				
			case static::TIME_FILTER_6_HOURS;
				$hours = 6;
				break;
				
			case static::TIME_FILTER_12_HOURS;
				$hours = 12;
				break;
		}
		$tm = localtime(time(), true);
		$end = mktime(
			$tm['tm_hour'] + $hours,
			$tm['tm_min'],
			$tm['tm_sec'],
			$tm['tm_mon'] + 1,
			$tm['tm_mday'],
			$tm['tm_year'] + 1900
		);
		
		$end = It6_Date::timestampToDb($end);
		
		$timeWhere = "sz.platna_do BETWEEN '$start' AND '$end'";
		return $timeWhere;
	}	
	
	public static function getMinutesTimeInterval($timeFilter){
		$start = It6_Date::dbNow();
	
		switch ($timeFilter) {
			case static::TIME_FILTER_10_MINUTES:
				$minutes = 10;
				break;
				
			case static::TIME_FILTER_20_MINUTES:
				$minutes = 20;
				break;
				
			case static::TIME_FILTER_30_MINUTES:
				$minutes = 30;
				break;
		}
		$tm = localtime(time(), true);
		$end = mktime(
				$tm['tm_hour'],
				$tm['tm_min'] + $minutes,
				$tm['tm_sec'],
				$tm['tm_mon'] + 1,
				$tm['tm_mday'],
				$tm['tm_year'] + 1900
		);
	
		$end = It6_Date::timestampToDb($end);
	
		$timeWhere = "sz.platna_do BETWEEN '$start' AND '$end'";

		return $timeWhere;
	}
	
	public static function getWeekendInterval($timeFilter) {
		$today = getdate();
		$tm = localtime(time(), true);
		
		$dbEndDayTime = Zend_Registry::get('ws')->Parameter->getGlobalparameter('offer.today.timeFrame.end');
		$dbEndDayTimeArr = explode(':', $dbEndDayTime);
		if(empty($dbEndDayTimeArr[1])) {
			$dbEndDayTimeArr[1] = '00';
		}
		if(empty($dbEndDayTimeArr[2])) {
			$dbEndDayTimeArr[2] = '00';
		}
		
		if ($today["wday"] == 0) {//nedeli nastavím jako sedmý den v týdnu
			$today["wday"] = 7;
		}
		
		if ($today["wday"] < 6) // jsem v pracovnim tydnu 
		{
			$daysToAdd = 7 - $today["wday"];
			
			$dbStartDateTime = It6_Date::dbNowAsDate(24 *3600).' '.$dbEndDayTimeArr[0].':'.$dbEndDayTimeArr[1].':'.$dbEndDayTimeArr[2];
			$startDayTime = It6_Date::fromDbAsTime($dbStartDateTime);
			$startDayTimeArr = explode(':', $startDayTime);
			$startTimeOffset = $startDayTimeArr[0] * 3600 + $startDayTimeArr[1] * 60 + $startDayTimeArr[2];
			
			$start = mktime(
					23,
					59,
					59	 + $startTimeOffset,
					$tm['tm_mon'] + 1,
					$tm['tm_mday'] + ($daysToAdd-2) ,
					$tm['tm_year'] + 1900
			);
			$start = It6_Date::timestampToDb($start);
		}
		else{
			$daysToAdd = 0;		
			$start = time();	
		}
		
		$dbEndDateTime = It6_Date::dbNowAsDate(24 *3600).' '.$dbEndDayTimeArr[0].':'.$dbEndDayTimeArr[1].':'.$dbEndDayTimeArr[2];
		$endDayTime = It6_Date::fromDbAsTime($dbEndDateTime);
		$endDayTimeArr = explode(':', $endDayTime);
		$endTimeOffset = $endDayTimeArr[0] * 3600 + $endDayTimeArr[1] * 60 + $endDayTimeArr[2];
		
		$end = mktime(
				23,
				59,
				59	 + $endTimeOffset,
				$tm['tm_mon'] + 1,
				$tm['tm_mday'] + $daysToAdd,
				$tm['tm_year'] + 1900
		);
		$end = It6_Date::timestampToDb($end);

		$timeWhere = "sz.platna_do BETWEEN '$start' AND '$end'";

		return $timeWhere;
	}
	
	public static function getCustomInterval($setDateTimeRange = false) {
		if ($setDateTimeRange) {
			self::setDateTimeRange();
		}
		$timeWhere = "";
		if (is_null(self::$dateFrom) && is_null(self::$dateTo)) {
			return $timeWhere;
		} else if (!is_null(self::$dateFrom) && !is_null(self::$dateTo)) {
			// both from and to
			$timeWhere = "sz.platna_do BETWEEN '" . It6_Date::toDb(self::$dateFrom) . "'
				    AND '" . It6_Date::toDb(self::$dateTo) . "'";
		} else if (!is_null(self::$dateFrom)) {
			// from (only)
			$timeWhere = "sz.platna_do >= '" . It6_Date::toDb(self::$dateFrom) . "'";
		} else if (!is_null(self::$dateTo)) {
			// to (only)
			$timeWhere = "sz.platna_do <= '" . It6_Date::toDb(self::$dateTo) . "'";
		}
		return $timeWhere;
	}
	
	public static function setDateTimeRange() {
		$minTime = strtotime('01.01.1970 00:00:00');
		$fromTime = isset($_REQUEST['dateFrom']) ? strtotime($_REQUEST['dateFrom']) : null;
		$toTime = isset($_REQUEST['dateTo']) && !empty($_REQUEST['dateTo']) ? strtotime($_REQUEST['dateTo']) : null;
		if (!is_null($fromTime) && !is_null($toTime) && $fromTime > $toTime) {
			$tmp = $_REQUEST['dateFrom'];
			$_REQUEST['dateFrom'] = $_REQUEST['dateTo'];
			$_REQUEST['dateTo'] = $tmp;
		}
		self::$dateFrom = ($fromTime > $minTime) ? $_REQUEST['dateFrom'] : null;
		self::$dateTo = ($toTime > $minTime) ? $_REQUEST['dateTo'] : null;
	}
	
	public static function isSupertip() {
		$select = Zend_Registry::get('db')
					->select('count(*)')
					->from('sazky')
					->join('udalost', 'sazky.udalost_id = udalost.udalost_id')
					->where('status = 0 and sport_id = 1056 and platna_do >= ?', It6_Date::dbNow());
		$result = $select->query()->fetchAll();
		
		return !empty($result[0]);
	}
}
