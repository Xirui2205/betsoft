<?php

abstract class Models_LiveBetting_LiveAbstract{


	/**
	 * Vraci obecna  data
	 * @param int $event_id  ID live udalosti
	 * @return array
	 */

	public static function basicRender($event_id){}

	/**
	 * Vraci sazky
	 * @param int $event_id  ID live udalosti
	 * @return array
	 */

	public static function betRender($event_id){


		try{

			$select = Zend_Registry::get('db')->select()
				->from(
					array('sz'=>'sazka_pohled'),
					array(
						"aktualizace1"=>'UNIX_TIMESTAMP(s.aktualizace)',
						"aktualizace2"=>'UNIX_TIMESTAMP(s.aktualizace_sazka)',
						'sz.sazka_id',
						'sz.jednoducha',
						'home_team'=>'lx.home_team',
						'away_team'=>'lx.away_team',
						'mtext'=>'CONCAT(lx.home_team," - ",lx.away_team)',
						'tnazev'=>'TRANSLATE(t.nazev,'. $_SESSION['lang_id'] .')',
						'pnazev'=>'TRANSLATE(sz.nazev,'. $_SESSION['lang_id'] .')',
						'sz.sloupec_id',
						'sz.kurz',
						's.close',
						"platna_od"=>'UNIX_TIMESTAMP(sz.platna_od)',
						"platna_do"=>'UNIX_TIMESTAMP(sz.platna_do)',
						's.no_comb',
						'sz.status',
						'sz.platny_od',
						'platna_do_time'=>'DATE_FORMAT(sz.platna_do,"%Y-%m-%d %H:%i:%s")',
						'ptext'=>'TRANSLATE(p.text,'. $_SESSION['lang_id'] .')',
						'sz.info'))
				->join(array(
					's'=>'live_sazka'),
					's.sazka_id=sz.sazka_id')
				->join(array(
					'lx'=>'live_event'),
					's.event_id=lx.event_id')
				->join(array(
					't'=>'typ'),
					'sz.typ_id=t.typ_id',
					array('t.poradi'))
				->join(array(
					'p'=>'podtyp'),
					'sz.podtyp_id=p.podtyp_id')
				->where('s.event_id=?',$event_id)
				->where('sz.platny_od=(
					SELECT d.platny_od FROM sazka_kurz d WHERE d.sazka_id=sz.sazka_id ORDER BY d.platny_od DESC LIMIT 1)')
			->order(array(
				't.poradi',
				'sz.platna_do',
				'sz.sazka_id',
				'sz.poradi_sloupec'));


			$row  = $select->query()->fetchAll();

			if(count($row) > 0){

				$bet = array();
				foreach($row as $h){

					#doslo ke zmene?#
//why this conditin? isnt the frequency set by javascript request? commented out by Martin on 2.6. 2010
					//if( $h['aktualizace2'] > ($_SESSION['betTime'][intval($_POST['n'])] - LIVE_LACK)){

						if(!isset($bet[$h['sazka_id']])){
							$bet[$h['sazka_id']]['jednoducha'] = $h['jednoducha'];
							$bet[$h['sazka_id']]['no_comb'] = $h['no_comb'];
							$bet[$h['sazka_id']]['tnazev'] = $h['tnazev'];
							$bet[$h['sazka_id']]['status'] = $h['status'];
							$bet[$h['sazka_id']]['close'] = $h['close'];
							$bet[$h['sazka_id']]['platna_od'] = $h['platna_od'];
							$bet[$h['sazka_id']]['platna_do'] = $h['platna_do'];
							$bet[$h['sazka_id']]['platna_do_time'] = $h['platna_do_time'];
							$bet[$h['sazka_id']]['mtext'] = $h['mtext'];
							$bet[$h['sazka_id']]['team1'] = $h['home_team'];
							$bet[$h['sazka_id']]['team2'] = $h['away_team'];
							$bet[$h['sazka_id']]['info'] = $h['info'];
							$bet[$h['sazka_id']]['ptext'] = $h['ptext'];
						}

						$bet[$h['sazka_id']]['sloupec'][] = array('pnazev'=>$h['pnazev'],'sloupec_id'=>$h['sloupec_id'],'kurz'=>$h['kurz'],'move'=>self::rateMove($h['sazka_id'],$h['kurz'],$h['sloupec_id'],$h['platny_od']));
					//}

				}

				return $bet;

			}else return false;


		} catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}


	}


	/**
	 * Vraci info k zapasu
	 * @param int $sazka_id  ID sazky
	 * @param int $rate  kurz
	 * @param int $sloupec_id  ID sloupce
	 * @param date $platny  platnost kurzu od
	 * @return array
	 */

	protected static function rateMove($sazka_id,$rate,$sloupec_id,$platny){


		try{

			$select = Zend_Registry::get('db')->select()->from(array('sz'=>'sazka_pohled'),array('sz.kurz'))

			->where('sz.sazka_id=?',$sazka_id)
			->where('sz.sloupec_id=?',$sloupec_id)
			->where('sz.platny_od<?',$platny )
			->order(array('sz.platny_od desc'))
			->limit(1);


			$stm  = $select->query();
			$row = $stm->fetchAll();

			if(count($row) > 0){

				if($row[0]['kurz'] > $rate) return -1;
				else if($row[0]['kurz'] < $rate) return 1;
				else return 0;

			}else return 0;

		} catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}

	}


	/**
	 * Vraci info k zapasu
	 * @param int $event_id  ID live udalosti
	 * @return array
	 */

	protected static function basicInfo($event_id){

		$data = array();

		try{

			$row2 = Zend_Registry::get('db')->select()
			->from(
				array('a'=>'live_info'),
				array('text'=>'TRANSLATE(text,'. $_SESSION['lang_id'] .')','time','book_text'))
			->where('a.event_id=?',$event_id)
			->order(array('time','id'))
			->query()->fetchAll();

			if(count($row2) > 0){

				foreach($row2 as $k=>$h){
					$data[$k] =  $h;
				}
			}


		}  catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}

		return $data;

	}


}
