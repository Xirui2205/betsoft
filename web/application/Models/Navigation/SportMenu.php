<?php


final class Models_Navigation_SportMenu extends Models_Navigation_MenuAbstract{

	/**
	 * pole, ktere obsahuje vysledne menu
	 * @access private
	 * @var array
	 */
	private $finalMenu = array();


	/**
	 * cache url adress
	 * @access private
	 * @var array
	 */
	private $cacheUrl;

	/**
	 * id oblasti ktera patri k dane IP
	 * @access private
	 * @var int
	 */
	private $oblastIP = false;

	/**
	 * id oblasti ktera patri k dane IP
	 * @access private
	 * @var int
	 */
	private $openURL = array('sport'=>0,'oblast'=>0,'udalost'=>0);

	/**
	 * Current page path in menu (sport/region/event)
	 * @var string
	 */
	private $openPath = '';

	/**
	 * menu je otevrene nebo zavrene
	 * @access public
	 * @var bool
	 */
	public $isOpen = false;

	public function __construct(){

		$this->openUrl();


	}

	/**
	 * Menu je otevrene
	 *
	 * @return void
	 */

	public function isOpen(){


		$this->isOpen = true;

	}

	/**
	 * Vypocita menu
	 *
	 * @return void
	 */

	private function builtMenu($timeFilter = null){

		

		$menuAr = $this->getActiveMenu($_SESSION['lang_id'],$timeFilter);

		$firstSport = 0;

		$isoCode = new IP;
		//TODO: do it better
		//$oblastId = $isoCode->getCountryId();
		$oblastId = 3; // country ID for CZ (NOT area ID, WTF?!)

		if(!empty($menuAr)) foreach($menuAr as $data){

			if(!isset($this->finalMenu[$data['sport_id']])){
				if ($this->openURL['sport'] == $data['sport_id']) {
					$open = 1;
					$this->isOpen();
				}
				else
					$open = 0;
				$this->finalMenu[$data['sport_id']] = array(
					'text' => Help::Html($data['snazev']),
					'pocet' => 0,
					'open' => $open,
				);
			}

			$this->finalMenu[$data['sport_id']]['pocet'] += $data['upocet'];

			if(!isset($this->finalMenu[$data['sport_id']]['oblast'][$data['oblast_id']])){
				$this->finalMenu[$data['sport_id']]['oblast'][$data['oblast_id']] = array(
					'text' => Help::Html($data['onazev']),
					'iso' => $data['iso'],
					'pocet' => 0,
					'open' => ($this->openURL['oblast'] == $data['oblast_id'] ? 1 : 0),
					'first' => ($oblastId && $data['oblast_id'] == $oblastId ? 1 : 0),
					'udalost' => array(),
				);
			}

			$this->finalMenu[$data['sport_id']]['oblast'][$data['oblast_id']]['pocet'] += $data['upocet'];


			$this->finalMenu[$data['sport_id']]['oblast'][$data['oblast_id']]['udalost'][$data['udalost_id']] = array(
				'text' => Help::Html($data['unazev']),
				'pocet' => $data['upocet'],
				'zvyrazneni' => $data['uzvyrazneni'],
				'open' => ($this->openURL['udalost'] == $data['udalost_id'] ? 1 : 0),
			);
		}
	}

	/**
	 * Podle nastavenych parametru otevira prislusne menu
	 *
	 * @return void
	 */

	private function openUrl(){

		$params = Zend_Registry::get('params');

		$this->openPath = '';

		if(isset($params[3]) ){

			if (!empty($params[3]))
				$this->openPath .= $params[3];

			try{

				$select = Zend_Registry::get('db')->select()->from('seo_url',array('event_id'))
				->where('url=?','/'. Help::Slash($params[3]) .'/')
				->where('lang_id=?',$_SESSION['lang_id'])
				->where('type=?',1);

				$stm  = $select->query();
				$row = $stm->fetchAll();

				if(count($row) > 0){

					$this->openURL['sport'] = $row[0]['event_id'];

					if(isset($params[4]) ){

						if (!empty($params[4]))
								$this->openPath .= "/{$params[4]}";

						$select = Zend_Registry::get('db')->select()->from('seo_url',array('event_id'))
						->where('url=?','/' .Help::Slash($params[4]) .'/')
						->where('lang_id=?',$_SESSION['lang_id'])
						->where('type=?',2);

						$stm  = $select->query();
						$row = $stm->fetchAll();

						if (count($row) > 0) {
							$this->openURL['oblast'] = $row[0]['event_id'];
							if (isset($params[5])) {

								if (!empty($params[5]))
									$this->openPath .= "/{$params[5]}";
	
								$this->openURL['oblast'] = $row[0]['event_id'];
	
								$select = Zend_Registry::get('db')->select()->from('seo_url',array('event_id'))
								->join(
									array('u' => 'udalost'),
									'u.udalost_id = event_id',
									null
								)
								->where('url=?','/'. Help::Slash($params[5]) .'/')
								->where('lang_id=?',$_SESSION['lang_id'])
								->where('type=?',3)
								->where('sport_id = ?',$this->openURL['sport'])
								->where('oblast_id = ?',$this->openURL['oblast']);

	
								$stm  = $select->query();
								$row = $stm->fetchAll();
	
								if (count($row) > 0)
									$this->openURL['udalost'] = $row[0]['event_id'];
							}
						}

					}

				}



			} catch ( Exception $e ) {

				echo $e;

			}

		}


	}


	/**
	 * Nastavi URL adresy
	 *
	 * @return void
	 */

	private function setURL($timeFilter = null){

		$postfix = Models_Helpers_Panels::setUrlFilterParams($timeFilter);

		/*
		foreach($this->finalMenu as $sport_id=>$data_sp){

			$s_url = $this->getUrl($sport_id,1);
			$this->finalMenu[$sport_id]['url'] = $s_url.$postfix;;

			if ( 1 == $this->finalMenu[$sport_id]['open'] ) {
				foreach($data_sp['oblast'] as $oblast_id=>$data_ob){
					
					if($data_ob['first'] == 1) $this->oblastIP = $oblast_id;
	
					$o_url = $this->getUrl($oblast_id,2);
					$this->finalMenu[$sport_id]['oblast'][$oblast_id]['url'] = $s_url.$o_url.$postfix;
	
					if ( 1 == $this->finalMenu[$sport_id]['oblast'][$oblast_id]['open'] ) {
						foreach($data_ob['udalost'] as $udalost_id=>$data_ud){
							
							$u_url = $this->getUrl($udalost_id,3);
							$this->finalMenu[$sport_id]['oblast'][$oblast_id]['udalost'][$udalost_id]['url'] = $s_url.$o_url.$u_url.$postfix;
							
						}
					}
	
	
				}
			}
		*/
		$ids = array(1 => array(), 2 => array(), 3 => array());
		foreach($this->finalMenu as $sport_id=>$data_sp){
			$ids[1][$sport_id] = '';
			if ( 1 == $this->finalMenu[$sport_id]['open'] ) {
				foreach($data_sp['oblast'] as $oblast_id=>$data_ob){
					$ids[2][$oblast_id] = '';
					if ( 1 == $this->finalMenu[$sport_id]['oblast'][$oblast_id]['open'] ) {
						foreach($data_ob['udalost'] as $udalost_id=>$data_ud){
							$ids[3][$udalost_id] = '';
						}
					}
				}
			}
		}
		$this->getUrls($ids);
		foreach($this->finalMenu as $sportId=> &$sport){
			$sportUrl = $ids[1][$sportId];
			$sport['url'] = $sportUrl . $postfix;
			if ( 1 == $sport['open'] ) {
				foreach($sport['oblast'] as $areaId => &$area){
					if ($area['first'] == 1)
						$this->oblastIP = $areaId;
					$areaUrl = $ids[2][$areaId];
					$area['url'] = $sportUrl . $areaUrl . $postfix;
	
					if ( 1 == $area['open'] ) {
						foreach($area['udalost'] as $eventId => &$event){
							$eventUrl = $ids[3][$eventId];
							$event['url'] = $sportUrl .  $areaUrl . $eventUrl . $postfix;
						}
					}
				}
			}
		}
	}


	/**
	 * Vraci  menu s ohledem na pocet aktivnich sazek
	 *
	 * @param int $id   ID sport/udalost/oblast
	 * @param int $type   typ (1:sport;2:oblast;3:udalost;4:druh;5:hry;6:tymy)
	 * @return string
	 */
	public function getUrl($id,$type=null){

		if($type == null)
			return '';

		if(isset($this->cacheUrl[$id][$type]))
			return $this->cacheUrl[$id][$type];

		try{
			$row = Zend_Registry::get('db')->select()
				->from('seo_url',array('url'))
				->where('event_id=?',$id)
				->where('lang_id=?',$_SESSION['lang_id'])
				->where('type=?',$type)
				->query()->fetch();


			$row['url'] = mb_substr($row['url'],1,mb_strlen($row['url']));
			$this->cacheUrl[$id][$type] = $row['url'];

			return $row['url'];

		}

		catch ( Exception $e ) {
			echo $e;
		}
	}

	/**
	 * Vraci menu s ohledem na pocet aktivnich sazek
	 * @param array $ids array (type => List of (ID => URL) pairs) which will be updated inplace (only if found in DB)
	 */
	public function getUrls(array &$ids){
		$sqlPairs = array();
		foreach ($ids as $type => $typeIds) {
			$_type = intval($type);
			foreach ($typeIds as $id => $url)
				$sqlPairs[] = "($_type," . intval($id) . ')';
		}
		if (!empty($sqlPairs)) {
			try{
				$res = Zend_Registry::get('db')->select()
					->from('seo_url',array('id' => 'event_id', 'type', 'url'))
					->where('(type,event_id) IN (' . implode(',', $sqlPairs) . ')')
					->where('lang_id=?', $_SESSION['lang_id'])
					->query();
				while ($row = $res->fetch())
					$ids[ $row['type'] ][ $row['id'] ] = mb_substr($row['url'], 1);
			}
			catch ( Exception $e ) {
				//echo $e;
			}
		}
	}

	/**
	 * Superseded by It6_Models_Sportbook::getAllLangsUrls(), subject to be replaced in the future refactoring
	 */
	public function getAllLangUrlParamsPart($id,$type=null){
		$displayedLangIds = array_keys(Models_Helpers_Lang::getDisplayedLangs());
/*		if($type == null)
			return '';
*/
/*		if(isset($this->cacheUrl[$id][$type]))
			return $this->cacheUrl[$id][$type];
*/
		try{
			$row = Zend_Registry::get('db')->select()
				->from('seo_url',array('url','lang_id'))
				->where('event_id=?',$id)
				->where('lang_id IN (?)',$displayedLangIds)
				->where('type=?',$type)
				->query()->fetchAll();

			$lang = array();
			foreach ($row as $k => $v)
				$lang[$v['lang_id']] = mb_substr($v['url'],1,mb_strlen($v['url']));
				
	//		$this->cacheUrl[$id][$type] = $lang[$v['lang_id']];

			return $lang;
		}

		catch ( Exception $e ) {
			echo $e;
		}
	}

	public function getAllLangUrlParams(Array $urlParts){
		$displayedLangs = Models_Helpers_Lang::getDisplayedLangs();

		$lang = array();
		$out = array();
		foreach ($displayedLangs as $id => $abbr) {
			$out[$abbr] = '';
		}
		
		foreach ($urlParts as $k => $v) {
			$lang[] = $this->getAllLangUrlParamsPart($k,$v);
		}
		
		foreach ($displayedLangs as $id => $abbr) {
			foreach ($lang as $k => $v) {
				if (!empty($v))
					$out[$abbr] .= $v[$id];
			}
		}
		return $out;
	}
	/**
	 * Vraci  vybrany sport/oblast/udalost
	 *
	 * @return array
	 */

	public function getOpenUrl(){

		return $this->openURL;

	}

	/**
	 * Current page path in menu (sport/region/event)
	 * @return string
	 */
	public function getOpenPath() {
		return $this->openPath;
	}

	/**
	 * Vraci  menu
	 *
	 * @return array
	 */

	public function getMenu($timeFilter = null){

		$this->builtMenu($timeFilter);

		$this->setURL($timeFilter);

		return $this->finalMenu;

	}


	public function getQuickOfferCount($query) {
		$now = It6_Date::dbNow();
		
		$select = Zend_Registry::get('db')->select()
		->from(
			array('s'=>'sport'),
			array('count'=>'count(*)'))
		->join(array('u'=>'udalost'),'s.sport_id=u.sport_id')
		->join(array('o'=>'oblast'),'u.oblast_id=o.oblast_id')
		->join(
				array('sz'=>'sazky'),
				'sz.udalost_id=u.udalost_id AND sz.live = 0 AND sz.status = 0 AND sz.risk_limit > sz.risk_limit_balance')
		->join(array('t'=>'typ'),'sz.typ_id=t.typ_id')
		->where('s.zobrazeno=?',1)
		->where('sz.platna_od<=?',$now )
		->where('sz.platna_do>=?',$now )
		->where('sz.live = 0')
		->where('sz.status=0')
		->where('sz.risk_limit > sz.risk_limit_balance')
		->where('u.zobrazeno=?',1)
		->where('t.zobrazeno=?',1)
		->where('u.platne_od<=?',$now)
		->where('u.platne_do>=?',$now);
                
		$timeWhere = Models_Markets_MarketData::getTodayToomorowTimeInterval($timeFilter);
		$select = $select->where($timeWhere);

		//$select = $select->where($query);
                
		$row  = $select->query()->fetch();
                
		return $row['count'];
	}

	public function getAllCount() {
		return $this->getQuickOfferCount('1');
	}
	
	public function getTodayCount() {
		$sqlPartial = Models_Markets_MarketData::getTodayToomorowTimeInterval(Models_Markets_MarketData::TIME_FILTER_ONLY_TODAY);
		return $this->getQuickOfferCount($sqlPartial);
	}

	public function getTodayAndTomorowCount() {
		$sqlPartial = Models_Markets_MarketData::getTodayToomorowTimeInterval(Models_Markets_MarketData::TIME_FILTER_TODAY_AND_TOMOROW);
		return $this->getQuickOfferCount($sqlPartial);
	}

	public function getTodaySportCount($sportId) {
		return $this->getQuickOfferCount(
			'DATE(sz.platna_do)=DATE(\'' . It6_Date::dbNow()  . '\') AND s.sport_id=' . $sportId
		);
	}

	public function getTodayFotbalCount() {
		return $this->getTodaySportCount(1001);
	}

	public function getTodayTenisCount() {
		return $this->getTodaySportCount(1003);
	}	
	
	/**
	 * Vraci  id oblasti ktera se shoduje s IP
	 *
	 * @return int
	 */

	public function getOblastIP(){

		return $this->oblastIP;

	}
}
