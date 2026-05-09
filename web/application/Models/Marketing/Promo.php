<?php


class Models_Marketing_Promo {

	/*
	 * Vyvedeni caching controly ven aby se dala zapinat/vypinat centralne
	 */
	private static function init_caching() {
			$frontendOptions = array(
				'lifetime' => PROMO_CACHE_LIFETIME,
				'automatic_serialization' => true
			);
			$backendOptions = $GLOBALS['ZEND_CACHE_BACKEND_OPTIONS'];

			if(defined(CACHING) && CACHING == 'off') {
					$frontendOptions['caching'] = false;
			}

			$cache = Zend_Cache::factory('Core','File',$frontendOptions,$backendOptions);
			return $cache;
	}


//commented out by Martin 9.4.2011. This functionality is now handled by ws.
	/**
	 * Aktualni nejlepsi tikety
	 * @return void
	 */
/*
	public static function monthTicket() {
		try{
			$db = Zend_Registry::get('db');
			$select = $db->select()->from(
				array('a'=>'vyherci_sazky'),
				array(
					't.zalozen',
					't.ticket_id',
					't.rate',
					'xcastka'=>'a.castka',
					'b.nick'))
			->join(array('b'=>'uzivatel'),'a.user_id=b.user_id')
			->join(array('t'=>'ticket'),'t.ticket_id=a.ticket_id')
			->where('a.full_win=1')
			->where('Month(a.datum) = Month(?)', It6_Date::dbNow())
			->where('b.anonymous <> ?', true)
			->order('t.rate_real DESC')->limit(3);
			$row2 = $select->query()->fetchAll();//echo $select->__toString();

			foreach($row2 as $h){
				$klic = $h['ticket_id'];

				$data[$klic]['rate_real'] = $h['rate'];
				$data[$klic]['xcastka'] = $h['xcastka'];
				$data[$klic]['nick'] = $h['nick'];
				$data[$klic]['en_ticket_id'] = It6_Models_Ticket::getReadableId($h['ticket_id'], $db);

				$data[$klic]['detail'] = Models_MyAccount_Ticket::detailTicket($data[$klic]['en_ticket_id']);
			}

			if ( !is_array($data) || count($data) == 0 ) {
				$data = array();
			}

			return $data;
		}

		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
	}
*/



	/**
	 * Novinky
	 * @return void
	 */
	public static function news() {
		try {
			$cache = self::init_caching();

			if ( !$data = $cache->load( 'news_' . $_SESSION['lang_id'] ) ) {
				$select = Zend_Registry::get( 'db' )
				->select()
				->from(
					array( 'a' => 'novinky'),
					array('a.novinka_id', 'a.platna_od', 'a.hp_image', 'a.promo_image' )
				)
				->join(
					array( 'u' => 'novinky_jazyk'),
					'u.novinka_id = a.novinka_id',
					array('u.anotace', 'u.nadpis', 'u.data')
				)
				->where( 'u.lang_id = ?', $_SESSION['lang_id'] )
				->where( 'a.zobrazeno = 1')
				->where( 'u.zobrazeno_jazyk = 1' )
				->where( 'a.platna_od <= now()' )
				->where( 'a.platna_do >= now()' )
				->order(array('a.platna_od desc' ));

				$row2 = $select->query()->fetchAll();

				foreach ( $row2 as $h ) {
					$klic = $h['novinka_id'];

					$data[$klic]['anotace'] = $h['anotace'];
					$data[$klic]['nadpis'] = $h['nadpis'];
					$data[$klic]['data'] = $h['data'];
					$data[$klic]['hp_image'] = $h['hp_image'];
					$data[$klic]['promo_image'] = $h['promo_image'];
					$data[$klic]['platna_od'] = $h['platna_od'];
				}

				$cache->save($data, 'news_'.$_SESSION['lang_id']);
			}

			if(empty($data))
				$data = array();

			return $data;
		}

		catch( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
	}



	/**
	 * Return an array of promos for the given controller_id and language
	 * @return array
	 */
	public static function getPromos($controllerId, $langId){
		try{
			$cache = self::init_caching();

			$promoIdPreview = null;
			if (isset($_GET['promoId']) && isset($_GET['k'])
					&& $_GET['k'] == sha1('57b'.$_GET['promoId'].' )(*)'.$_GET['promoId'].'^s%L') ) {
				$promoIdPreview = intval($_GET['promoId']);
			}
			
			if(!$promo = $cache->load('promo_'.$langId)) {
				
				$promoFields = array('promo_id','img_name','url','sazka_id','menu_id','target','text','title','without_text');
				
				if (is_null($promoIdPreview)) {
					$res = Zend_Registry::get('db')->select()
					->from(
						array('p'=>'promo'),
						$promoFields
					)
					->join(
						array('chp'=>'controller_has_promo'),
						'chp.promo_id = p.promo_id',
						array()
					)
					->where('p.lang_id = ?', $langId)
					->where('chp.controller_id = ?', $controllerId)
					->where('p.platne_od <= ?', It6_Date::dbNow())
					->where('p.platne_do >= ?', It6_Date::dbNow())
					->order(array('chp.priority'))
					->query()->fetchAll();
				} else {
					// promo preview
					$res = Zend_Registry::get('db')->select()
					->from(
						array('p'=>'promo'),
						$promoFields
					)
					->where('p.promo_id = ?', $promoIdPreview)
					->where('p.lang_id = ?', $langId)
					->query()->fetchAll();
				}
				
				if(empty($res)) // load default promos
					$res = Zend_Registry::get('db')->select()
					->from(
						array('p'=>'promo'),
						$promoFields
					)
					->join(
						array('chp'=>'controller_has_promo'),
						'chp.promo_id = p.promo_id',
						array()
					)
					->where('p.lang_id = ?', $langId)
					->where('chp.controller_id = 0')
					->order(array('chp.priority'))
					->query()->fetchAll();

				$ws = Zend_Registry::get('ws');
				
				foreach($res as $row){
					if (mb_strlen($row['url']) > 0)
						$promo[$row['promo_id']]['url'] = $row['url'];
					if(intval($row['sazka_id']) != 0) {
						
						//get bet info
						
						$bet_id = $row['sazka_id'];
						$db = Zend_Registry::get('db');
						$langId = $_SESSION['lang_id'];
						$now = It6_Date::dbNow();
				
						try {

							$registry = Zend_Registry::getInstance();
							//pri volani getPromos z JSON Serveru nedochazi k zavolani pluginu SetCcontroller
							if (isset($registry["c_id"]))
								Models_Markets_MarketData::loadExceptions();
							
							$select = $db->select()
							->from(
								array('sz'=>'sazky'), 
								array(
										'sz.sazka_id',
										'IF(sz.parent_id IS NULL, sz.sazka_id, sz.parent_id) as main_bet_id',
										'sk.sloupec_id',
										'sz.text',
										'sz.typ_id',
										'nazev_typu_sazky' => 't.nazev',
										'podtyp_sloupce_preklad' => 'p.text',
										'sz.jednoducha',
										'sz.ako',
										'sz.podtyp_id',
										'podtyp_sloupce_nazev' => 'ps.nazev',
										'sk.kurz',
										'platna_od'=>'sz.platna_od',
										'platna_do'=>'sz.platna_do',
									)
								)
							->joinLeft(
									array('sk'=>'sazka_kurz_aktualni'),
									'sk.sazka_id = sz.sazka_id', null
							)
							->joinLeft(
								array('ps'=>'podtyp_sloupce'),
								'ps.sloupec_id = sk.sloupec_id', null
							)
							->joinLeft(
								array('t'=>'typ'),
								't.typ_id = sz.typ_id', null
							)
							->join(
								array('p' => 'preklady'),
								'p.index_pole = t.nazev AND p.lang_id=' . intval($langId),
								null 
							)
							->where('sz.sazka_id = '.$bet_id)
							->where('sz.platna_od<=?',$now )
							->where('sz.platna_do >= ?',$now)
							->where('sz.live=0')
							->where('sz.status=0')
							->where('sz.risk_limit > sz.risk_limit_balance')
							->order('ps.poradi');
								
							$bet_data = $select->query()->fetchAll();
							
						} catch ( Exception $e ) {
							Models_Exception_Handler::handle($e);
						}
						$promo[$row['promo_id']]['bets']['data'] = $bet_data;
						
					}
					if(intval($row['menu_id']) != 0)
						$promo[$row['promo_id']]['menu'] = $row['menu_id'];

					$promo[$row['promo_id']]['target']	= $row['target'];
					$promo[$row['promo_id']]['text']	= preg_replace("/[\\n\\r]+/", "<br />", $row['text']);
					$promo[$row['promo_id']]['title']	= $row['title'];
					$promo[$row['promo_id']]['img']		= $row['img_name'];
					$promo[$row['promo_id']]['without_text'] = $row['without_text'];
					
					
				}

				if(empty($promo))
					$promo = array();
			}
			
			return $promo;
		}

		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
	}


	
	/**
	 * Return an array of banners for the given controller_id and language
	 * @return array
	 */
	public static function getBanners($controllerId, $langId) {
		$banners = Zend_Registry::get('ws')->Banner->getByControllerAndLanguage(1, $_SESSION['lang_id'], true);
		$banners = It6_ArrayWrapper::toAssocLikeArray($banners, 'locationId');

		if(!isset($banners[1]))
			$banners[1] = array();
		if(!isset($banners[2]))
			$banners[2] = array();

		return $banners;
	}
}