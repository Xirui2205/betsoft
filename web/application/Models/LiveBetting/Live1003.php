<?php

/*Live tenniss*/

class Models_LiveBetting_Live1003 extends Models_LiveBetting_LiveAbstract{


	/**
	 * Vraci obecna  data
	 * @param int $event_id  ID live udalosti
	 * @return array
	 */

	public static function basicRender($event_id){

		$data = array();


		try{

			$select = Zend_Registry::get('db')
				->select()->from(
					array('a'=>'live_1003'),
					array(
						'unazev'=>'TRANSLATE(u.nazev,'.$_SESSION['lang_id'] .')',
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
						'court'=>'TRANSLATE(court,'. $_SESSION['lang_id'].')',
						'set_num',
						'first_service',
						'tiebreak',
						'a.note'))
				->join(
					array('s'=>'live_event'),
					'a.event_id=s.event_id')
				->join(
					array('u'=>'udalost'),
					's.l_udalost_id=u.udalost_id')
				->join(
					array('x'=>'sport'),
					's.l_sport_id=x.sport_id')
				->where('s.event_id=?',$event_id);

			$row = $select->query()->fetchAll();


			if(count($row) > 0){

				if(!isset($_SESSION['basicTime'][intval($_POST['n'])])) $data['html'] = 1;

				foreach($row[0] as $k=>$h){
					$data[$k] =  $h;
				}


				if($data['tiebreak'] == 1 || $data['tiebreak'] == '1')
					$data['tiebreak'] = 'T';
				else
					$data['tiebreak'] = '';


				$data['service']='';
				$service = 1;
				$setTotal = 1;

				if($data['stav']==14)
					$setTotal = 1;
				if($data['stav']==15)
					$setTotal = 2;
				if($data['stav']==16)
					$setTotal = 3;
				if($data['stav']==17)
					$setTotal = 4;
				if($data['stav']==18)
					$setTotal = 5;


				$gameTotal = $data['set_'.$setTotal.'_home']+$data['set_'.$setTotal.'_away']+1;
				$pointTotal = ($data['score_home']*1) + ($data['score_away']*1);

				if($data['tiebreak']=='T')
					$tiebreak = true;
				else
					$tiebreak = false;


				if($setTotal % 2 == 1){

					if($gameTotal % 2 == 1)
						$service = 1;
					else
						$service = 2;


					if($tiebreak){
						if($gameTotal % 2 == 1){

							if((($pointTotal + ($pointTotal % 2))/2)%2 == 1)
								$service = 1;
							else
								$service = 2;

						}
						else{
							if((($pointTotal + ($pointTotal % 2))/2)%2 == 0)
								$service = 2;
							else
								$service = 1;

						}
					}
				}



				if($setTotal % 2 == 0){

					if($gameTotal % 2 == 1)
						$service = 2;
					else
						$service = 1;


					if($tiebreak){
						if($gameTotal % 2 == 1){

							if((($pointTotal + ($pointTotal % 2))/2)%2 == 1)
								$service = 2;
							else
								$service = 1;

						}
						else{

							if((($pointTotal + ($pointTotal % 2))/2)%2 == 0)
								$service = 1;
							else
								$service = 2;

						}
					}
				}


				if(($service==1 && $data['first_service']=='home') || ($service==2 && $data['first_service']=='away'))
					$data['service'] = 'home';
				else
					$data['service'] = 'away';


				$data['info'] = self::basicInfo($event_id);


			}
			else return false;


			return $data;

		}  catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}

	}

}
