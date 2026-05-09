<?php

/*Live football*/

class Models_LiveBetting_Live1001 extends Models_LiveBetting_LiveAbstract{


	/**
	 * Vraci obecna  data
	 * @param int $event_id  ID live udalosti
	 * @return array
	 */

	public static function basicRender($event_id){

		$data = array();

		try{

			$select = Zend_Registry::get('db')->select()
			->from(
				array('a'=>'live_1001'),
				array(
					's.stav',
					'x.sport_id',
					'sstname'=>'TRANSLATE(CONCAT("live_state_",s.stav),'. $_SESSION['lang_id'] .')',
					'snazev'=>'TRANSLATE(x.nazev,'. $_SESSION['lang_id'] .')',
					'unazev'=>'TRANSLATE(u.nazev,'. $_SESSION['lang_id'] .')',
					's.away_team',
					's.home_team',
					's.minute',
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
					'a.note'
				))
			->join(array('s'=>'live_event'),'a.event_id=s.event_id')
			->join(array('u'=>'udalost'),'s.l_udalost_id=u.udalost_id')
			->join(array('x'=>'sport'),'s.l_sport_id=x.sport_id')
			->where('s.event_id=?',$event_id);
			$row = $select->query()->fetchAll();


			if(count($row) > 0){

				if(!isset($_SESSION['basicTime'][intval($_POST['n'])]))
					$data['html'] = 1;

				foreach($row[0] as $k=>$h){
					$data[$k] =  $h;
				}



				$data['info'] = self::basicInfo($event_id);



			}
			else
				return false;


			return $data;

		}  catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}

	}

}
