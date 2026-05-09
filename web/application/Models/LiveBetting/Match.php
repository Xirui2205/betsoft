<?php

class Models_LiveBetting_Match{




	/**
	 * parametr
	 * @access private
	 * @var string
	 */
	private static $param;


	/**
	 *  o jaky sport jde
	 * @access private
	 * @var array
	 */
	private static $globalInfo;

	/**
	 * Vraci data do kalendare
	 * @param int $type  type 1 basic info 2 odds
	 * @return array
	 */

	public static function renderLiveMatch($type){

		//time logic here is to set time delay before users can see admin's updates in live match
		if(!isset($_SESSION['basicTime']) )
			$_SESSION['basicTime'] = array();
		if(!isset($_SESSION['betTime']) )
			$_SESSION['betTime'] = array();

		if(isset($_SESSION['basicTime'][intval($_POST['n'])]))
			$_SESSION['basicTime'][intval($_POST['n'])]	= time();
		if(isset($_SESSION['betTime'][intval($_POST['n'])]))
			$_SESSION['betTime'][intval($_POST['n'])]	= time();

		self::$globalInfo = self::getGlobalInfo();

		if(!self::$globalInfo)
			return array('html'=>I18m::trans('noexist_livematch'));



		if($type == 1
			&& (!isset($_SESSION['basicTime'][intval($_POST['n'])])
				|| self::$globalInfo['aktualizace'] < ($_SESSION['basicTime'][intval($_POST['n'])] - LIVE_LACK)
		)) {
			$tmp = self::getBasicData(self::$globalInfo['l_sport_id']);
			$tmp['lastNote'] = array_pop($tmp['info']);
			return $tmp;
		}

		else if($type == 2
			&& (!isset($_SESSION['betTime'][intval($_POST['n'])])
				|| self::$globalInfo['aktualizace'] < ($_SESSION['betTime'][intval($_POST['n'])] - LIVE_LACK)
		)) {
			return self::getBetData(self::$globalInfo['l_sport_id']);
		}

	}

	/**
	 * Vraci sazkove prilezitosti
	 * @param int $sport_id  ID sportu
	 * @return integer
	 */

	private static function getBetData($sport_id) {
		eval("\$ret = Models_LiveBetting_Live".$sport_id."::betRender(". intval($_POST['n']) .");");

		if(!isset($_SESSION['betData'][intval($_POST['n'])]))
			$_SESSION['betData'][intval($_POST['n'])]	= 0;

		if($ret)
			return $ret;
		else{
			return array('html'=>I18n::trans('no_livematch'));
		}
	}


	/**
	 * Vraci obecna  data
	 * @param int $sport_id  ID sportu
	 * @return integer
	 */

	private static function getBasicData($sport_id){
		if(file_exists('live'.$sport_id).'.php') {
			eval("\$ret = Models_LiveBetting_Live".$sport_id."::basicRender(". intval($_POST['n']) .");");

			if(!isset($_SESSION['basicTime'][intval($_POST['n'])]))
				$_SESSION['basicTime'][intval($_POST['n'])]	= 0;

			if($ret)
				return $ret;
			else
				return array('html'=>I18n::trans('noexist_livematch'));
		}
	}



	/**
	 * Vraci zakladni data o jaky sport jde
	 * @return integer
	 */

	private static function getGlobalInfo(){

		if(!isset($_POST['n']) || !ctype_digit($_POST['n']))
			return false;

		try{
			$select = Zend_Registry::get('db')->select()
			->from(
				array('l'=>'live_event'),
				array('l_sport_id',"aktualizace"=>'UNIX_TIMESTAMP(aktualizace)'))
			->where('event_id=?',$_POST['n']);

			$row = $select->query()->fetchAll();

			if(count($row) > 0)
				return $row[0];
			else
				return false;


		}

		catch(Exception $e){
			Models_Exception_Handler::handle($e);
		}
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

			$select = Zend_Registry::get('db')->select()
				->from(
					array('live_1001'),
					array(
						'yellow_card_away',
						'yellow_card_home',
						'red_card_away',
						'red_card_home',
						'second_half_away',
						'second_half_home',
						'first_half_away',
						'first_half_home',
						'score_away',
						'score_home',
						'note'
					))
				->where('event_id=?',$event_id);

			$row=$select->query()->fetchAll();

			foreach($row as $h){
				$bet = $h;
				$bet['state'] = $h['score_home'].':'.$h['score_away'];
			}

		}

		catch(Exception $e){
			Models_Exception_Handler::handle($e);
		}

		return $bet;
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
			$select = Zend_Registry::get('db')->select()
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
						'tretina_3_away',
						'note'
					))
				->where('event_id=?',$event_id);

			$row = $select->query()->fetchAll();

			foreach($row as $h){
				$bet = $h;
				$bet['state'] = $h['score_home'].':'.$h['score_away'];
			}

		}

		catch(Exception $e){
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
			$select = Zend_Registry::get('db')->select()
				->from(
					array('live_1003'),
					array(
						'score_home',
						'score_away',
						'set_1_home',
						'set_1_away',
						'set_2_home',
						'set_2_away',
						'set_3_home',
						'set_3_away',
						'set_4_home',
						'set_4_away',
						'set_5_home',
						'set_5_away',
						'court',
						'set_num',
						'first_service',
						'note'
					))
				->where('event_id=?',$event_id);

			$row = $select->query()->fetchAll();

			foreach($row as $h){
				$bet = $h;
				$bet['state'] = $h['score_home'].':'.$h['score_away'];
			}

		}

		catch(Exception $e){
			Models_Exception_Handler::handle($e);
		}

		return $bet;
	}


}
