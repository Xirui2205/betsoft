<?php

class Models_LiveBetting_Calendar{

	const TYPE_ONLINE = 1;
	const TYPE_COMMING = 2;
	const TYPE_BY_DATE = 3;
	const TYPE_BY_SPORT = 4;

	/**
	 * typ pole znovelych hodnot
	 * @access private
	 * @var array
	 */
	private static $openUrl = array();


	/**
	 * parametr
	 * @access private
	 * @var string
	 */
	private static $param;


	/**
	 * strnaka
	 * @access private
	 * @var int
	 */
	private static $page = 0;
	
	private static $pagination = null;

	/**
	 * Vraci bezici live sazky
	 * @return array
	 */

	public static function getOnLine($groupBySport=null){

		return self::getData(1, $groupBySport);

	}

	/**
	 * Vraci pripravovane live sazky
	 * @return array
	 */

	public static function getComming($groupBySport=null){

		return self::getData(2, $groupBySport);

	}

	/**
	 * Vraci data do kalendare
	 * @param int $type  type infromace calendar | sport
	 * @param string $param parametr
	 * @param int $page  stranka
	 * @return array
	 */

	public static function getCalendarData($type,$param,$page){

		self::$param = $param;
		self::$page = intval($page);

		if($type == 'calendar') return self::getData(3);
		if($type == 'sport')    return self::getData(4);

	}



	/**
	 * Strankovani
	 * @return array
	 */

	public static function pagging(){
		return Models_Helpers_Pagging::page(self::$pagination->total, PAGE_CALENDAR, self::$page);

	}


	/**
	 * Vraci sporty pro live sazky
	 * @return array
	 */

	public static function getSport(){

		$sport = array();

		try{

			$row = Zend_Registry::get('db')->select()->from(array('l'=>'sport'),array('sport_id','nazev'))
			->where('live=?',1)->query()->fetchAll();


			$sport = $row;

		}  catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}



		return $sport;
	}


	/**
	 * Vraci zakladni data o Live sazkach na zaklade dotazu
	 * @param int $type  type infromace  1=bezici zapasy
	 * @param boolean $groupBySport  return matches in 1D or in 2D array grouped by sports
	 * @return integer
	 */

	private static function getData($type, $groupBySport=null){
		try {
			$ws = Zend_Registry::get('ws');
			/*
			$select = Zend_Registry::get('db')->select()
				->from(
					array('l'=>'live_event'),
					array(
						'snazev'=>'s.nazev',
						'unazev'=>'u.nazev',
						'start_date_date'=>'DATE_FORMAT(start_date,"%d.%m.%Y")',
						'start_date_hour'=>'DATE_FORMAT(start_date,"%H:%i")',
						'event_id',
						'stav',
						'home_team',
						'away_team',
						'minute',
						'l_sport_id'
				))
				->join(
					array('u'=>'udalost'),
					'u.udalost_id=l_udalost_id'
				)
				->join(
					array('s'=>'sport'),
					's.sport_id=u.sport_id'
				);
			*/

			
			switch ( $type ) {
			case self::TYPE_ONLINE:
				$where = array('status NOT IN (?)' => array(
					'NOT_STARTED','END','UNFINISHED','CANCELED'));
				$order = array('start DESC');
				$extensions = array();
				break;
			case self::TYPE_COMMING:
				$where = array(
					'status = ?' => 'NOT_STARTED',
					'DATE(start) = ?' => It6_Date::dbNowAsDate());
				$order = array('start');
				$extensions = array();
				break;

			case self::TYPE_BY_DATE:
				switch ( self::$param ) {
				case 'today':
					$where = array('DATE(start) = ?' => It6_Date::dbNowAsDate());
					break;
				case 'tommorow':
					$where = array(
						'DATE(start) = DATE_ADD(?, INTERVAL 1 DAY)' => It6_Date::dbNowAsDate());
					break;
				case 'week':
					$where = array(
						'DATE(start) >= ?' => It6_Date::dbNowAsDate(),
						'DATE(start) <= DATE_ADD(?, INTERVAL 7 DAY)' => It6_Date::dbNowAsDate());
					break;
				default:
					$where = array('DATE(start) >= ?' => It6_Date::dbNowAsDate());
					break;
				}

				$order = array('start');
				self::$pagination = new It6_WsExtension_Client_Pagination(
						'pagination', PAGE_CALENDAR, self::$page * PAGE_CALENDAR);
				$extensions = array(self::$pagination);

				break;
			case self::TYPE_BY_SPORT:
				$where = array('DATE(start) >= ?' => It6_Date::dbNowAsDate());
				if ( 'all' != self::$param )
					$where['sport = ?'] = self::$param;

				$order = array('start');
				self::$pagination = new It6_WsExtension_Client_Pagination(
						'pagination', PAGE_CALENDAR, self::$page * PAGE_CALENDAR);
				$extensions = array(self::$pagination);
				break;
			default:
				throw new Exception("Unknown type '$type'");
			}

			$matches = $ws->ext($extensions)->MatchLive->getAllWhereOrder($where, $order);

			$ret = array();
			if ( $groupBySport )
				foreach ( $matches as $match )
					$ret[$match->sport][$match->id] = $match;
			else
				foreach ( $matches as $match )
					$ret[$match->id] = $match;

			//self::paginationCount = $matches['count']

			return $ret;
		}

		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}

	}


	/**
	 * Udeja k hokeji
	 *
	 * @param int $event_id
	 * @param int $stav
	 *
	 * @return array
	 */
	public static function Live1011($event_id,$stav){

		$bet = array();

		try{

			$row = Zend_Registry::get('db')->select()
				->from(
				array('live_1011'),
					array(
						'score_home',
						'score_away',
						'tretina_1_home',
						'tretina_1_away',
						'tretina_2_home',
						'tretina_2_away',
						'tretina_3_home',
						'tretina_3_away'
				))
				->where('event_id=?',$event_id)
				->query()->fetchAll();

			foreach($row as $h){
				$bet = $h;
				$bet['state'] = $h['score_home'].':'.$h['score_away'];
			}

		}  catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}


		return $bet;
	}


	/**
	 * Udeja k tenisu
	 *
	 * @param int $event_id
	 * @param int $stav
	 *
	 * @return array
	 */
	public static function Live1003($event_id,$stav){

		$bet = array();

		try{

			$row = Zend_Registry::get('db')->select()->from(array('live_1003'),array('score_home','score_away','set_1_home','set_1_away','set_2_home','set_2_away','set_3_home','set_3_away','set_4_home','set_4_away','set_5_home','set_5_away','court','set_num','first_service'))
			->where('event_id=?',$event_id)

			->query()->fetchAll();

			foreach($row as $h){

				$bet = $h;
				if( $stav == LIVE_1_SET) $bet['state'] = '1. set';
				else if( $stav == LIVE_2_SET) $bet['state'] = '2. set';
				else if( $stav == LIVE_3_SET) $bet['state'] = '3. set';
				else if( $stav == LIVE_4_SET) $bet['state'] = '4. set';
				else if( $stav == LIVE_5_SET) $bet['state'] = '5. set';
				else  $bet['state'] = '';

			}

		}  catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}


		return $bet;
	}


	/**
	 * Udeja k fotbalu
	 *
	 * @param int $event_id
	 * @param int $stav
	 *
	 * @return array
	 */
	public static function Live1001($event_id,$stav){

		$bet = array();

		try{

			$row = Zend_Registry::get('db')->select()->from(array('live_1001'),array('yellow_card_away','yellow_card_home','red_card_away','red_card_home','second_half_away','second_half_home','first_half_away','first_half_home','score_away','score_home'))
			->where('event_id=?',$event_id)

			->query()->fetchAll();

			foreach($row as $h){

				$bet = $h;
				$bet['state'] = $h['score_home'].':'.$h['score_away'];

			}

		}  catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}


		return $bet;
	}


}
