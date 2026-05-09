<?php

class Promo extends AbstractSection{
	
	const MODE_CURRENT	= 'current';
	const MODE_COMING	= 'coming';
	const MODE_PAST		= 'past';
	const MODE_ALL		= 'all';
	
	const PROMO_TITLE_MAX_LENGTH	= 90;
	const PROMO_TEXT_MAX_LENGTH		= 250;
	
	const PROMO_CALENDAR_SCOPE		= 7; //in days
	const PROMO_CALNDER_PX_PER_HOUR	= 6;
	
	/**
	 * database object
	 * @access private
	 * @var object
	 */
	private  $dbGame;

	/**
	 * instance of LanguageTabs
	 * @access private
	 * @var object
	 */
	private $langTabs;

	/**
	 * conversion between controller that shows promo and controller where it is defined
	 * @access private
	 * @var array
	 */
	 
	private $promoControllerMaping = array();
	/**
	 * Zend Controller for all old style admin sections
	 * @access private
	 * @var object
	 */
	private $controller;
	
	/**
	 * If non-default layou is to be used, its name will be stored in this var.
	 * @var null | string
	 */
	protected $layout;



	/**
	* Konstruktor
	*
	* Pokud neni identifikator spojeni predan vytvori se nove spojeni
	*
	* @param int $section id aktualni sekce
	* @param PEAR::DB $dbGame objekt spojeni s databazi
	*/
	public function __construct($section = 0, $dbGame, $controller){
		parent::__construct($section);
		$this->controller = $controller;

		$this->controller->registerJsInclude('charCounter');
		$this->controller->registerJsInclude('promoAdministration');

		$this->langTabs = new LanguageTabs(null, $this->dbGame);

		if($dbGame == null)
			$this->dbGame = DbUtil::connectWebDb();
		else
			$this->dbGame = $dbGame;

		$this->promoControllerMaping = array (
			99	=> 1,
			162	=> 44,
			298 => 0,
		);
	}



	/**
	 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
	 * @return void
	 */
	public function runAction(){
		if(isset($_POST['save'])) {
			if($this->update)
				$this->EditPromo();
			else
				$this->vrat .= UiUtil::printErrors('Insufficient Privilegies '.$result);
		}
		else if(isset($_POST['delete'])) {
			if($this->delete)
				$this->DeletePromo();
			else
				$this->vrat .= UiUtil::printErrors('Insufficient Privilegies '.$result);
		}


		if(array_key_exists($this->section, $this->promoControllerMaping))
			$controllerId = $this->promoControllerMaping[$this->section];
		else
			$controllerId = null;

		if(isset($_GET['new-promo']))
			$this->NewPromo($controllerId);
		else if(isset($_GET['mode']))
			$this->ListPromos($controllerId);
		else if($this->section == 321) {
			$this->previewPromo();
		}
		else
			$this->PromoCalendar($controllerId);


		$this->dbGame->disconnect();
	}



	/**
	 * Update/Insert Promo info
	 * @return void
	 */
	private function EditPromo(){ 
		try{
			if(!empty($_POST['promo_id']))
				$fieldData['promoId'] = $_POST['promo_id'];
			else
				$fieldData['promoId'] = null;
			if(!empty($_POST['show']))
				$fieldData['show'] = $_POST['show'];
			$fieldData['langId']		= $_POST['lang_id'];
			$fieldData['imgName']		= $_POST['img_name'];
			$fieldData['url']			= $_POST['url'];
			$fieldData['validFrom']		= $_POST['platne_od'];
			$fieldData['validUntil']	= $_POST['platne_do'];
			$fieldData['title']			= $_POST['title'];
			$fieldData['withoutText']   = isset($_POST['without_text']) ? 1 : 0;
			$fieldData['alias']			= $_POST['bet_alias'];
			$fieldData['betId']			= $_POST['sazka_id'];
			$fieldData['menuId']		= $_POST['menu_id'];
			$fieldData['target']		= $_POST['promo_okno'];
			$fieldData['text']			= $_POST['text'];
			$fieldData['buttonText']	= $_POST['button_text'];
			$fieldData['priority']		= $_POST['priority'];

			$this->dbGame->autoCommit(false);
			
			if(
				empty($fieldData['langId'])
				|| empty($fieldData['imgName'])
				|| empty($fieldData['validFrom'])
				|| empty($fieldData['validUntil'])
				|| empty($fieldData['title'])
			) {
				$this->vrat .= UiUtil::printErrors('missing_fields');
				if(empty($fieldData['promoId']))
					$this->newPromo(null, $fieldData);
			}
			else {
				if(empty($fieldData['promoId'])) {
					$sql = $this->dbGame->prepare("SELECT MAX(promo_id) as latestPromo FROM `promo`");
					$res = $this->dbGame->execute($sql, array());
					DbUtil::testResult($res);
					$latestPromo = $res->fetchRow();
					if(!empty($latestPromo['latestPromo']))
						$promoId = $latestPromo['latestPromo'] + 1;
					else
						$promoId = 1;
				}
				else
					$promoId = $fieldData['promoId'];

				$sql = $this->dbGame->prepare("
					REPLACE INTO promo (lang_id, promo_id, img_name, url, alias, sazka_id, menu_id, target, text, platne_od, platne_do, title,	button_text, without_text)
					VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)
				");
				$data = array(
					$fieldData['langId'],
					$promoId,
					$fieldData['imgName'],
					$fieldData['url'],
					$fieldData['alias'],
					$fieldData['betId'],
					$fieldData['menuId'],
					$fieldData['target'],
					$fieldData['text'],
					It6_Date::toDb($fieldData['validFrom']),
					It6_Date::toDb($fieldData['validUntil']),
					$fieldData['title'],
					$fieldData['buttonText'],
					$fieldData['withoutText']
				);
				$res = $this->dbGame->execute($sql, $data);
				DbUtil::testResult($res);


				$sql = $this->dbGame->prepare("
					DELETE
					FROM controller_has_promo
					WHERE promo_id = ?
				");
				$res = $this->dbGame->execute($sql, array($promoId));
				DbUtil::testResult($res);


				$sql = $this->dbGame->prepare("
					INSERT INTO controller_has_promo (
						controller_id,
						promo_id,
						lang_id,
						priority
					)
					VALUES(?,?,?,?)
				");

				if(!empty($fieldData['show'])) {
					foreach($fieldData['show'] as $controllerId => $value) {
						$data = array(
							$controllerId,
							$promoId,
							$fieldData['langId'],
							$fieldData['priority'],
						);
						$res = $this->dbGame->execute($sql, $data);
						DbUtil::testResult($res);
					}
				}

				$this->dbGame->commit();
				$this->dbGame->autoCommit(true);
				
				if(strlen($fieldData['title']) > self::PROMO_TITLE_MAX_LENGTH)
					$this->vrat .= UiUtil::printWarnings('max_title_length_reached');
				if(strlen($fieldData['text']) > self::PROMO_TEXT_MAX_LENGTH)
					$this->vrat .= UiUtil::printWarnings('max_text_length_reached');
				
				$this->vrat .= UiUtil::printMessages('banners-updated');
				It6_GlobalCache_Invalidator::Banner_update();
			}
		}

		catch(exception $e) {
			$this->dbGame->rollback();
			$this->vrat .= UiUtil::printErrors('update-error');
			throw new Exception($e);
		}

	}




	private function ListPromos($controllerId = null) {
		$mode = !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : self::MODE_CURRENT;

		switch ( $mode ) {
			case self::MODE_CURRENT:
				$modeWhere = 'platne_od < \''.It6_Date::dbNow() . '\' 
								AND platne_do > \''.It6_Date::dbNow() . '\'';
				$modeOrder = 'platne_do ASC';
				break;
			case self::MODE_COMING:
				$modeWhere = 'platne_od >= \''.It6_Date::dbNow() . '\'';
				$modeOrder = 'platne_od ASC';
				break;
			case self::MODE_PAST:
				$modeWhere = 'platne_do <= \''.It6_Date::dbNow() . '\'';
				$modeOrder = 'platne_do DESC';
				break;
			case self::MODE_ALL:
			default:
				$mode = self::MODE_ALL;
				$modeWhere = 1;
				$modeOrder = 'platne_do DESC';
				break;
		}

		$fields = 'p.*, j.iso AS lang_iso, chp.controller_id, chp.priority';
		$promosRaw = $this->getPromos($controllerId, $fields, $modeWhere, $modeOrder);


		$promos = array();
		foreach($promosRaw as $promoRaw) {
			if(!empty($promoRaw['lang_iso'])) {
				if(empty($promoRaw['priority']))
					$promoRaw['priority'] = '';

				if(!isset($promoRaw['controllers'])) {
					$sql1 = $this->dbGame->prepare("
						SELECT GROUP_CONCAT(controller_id) AS controllers
						FROM controller_has_promo
						WHERE promo_id = ?
					");
					$res1 = $this->dbGame->execute($sql1, array($promoRaw['promo_id']));
					DbUtil::testResult($res1);
					$promoRaw['controllers'] = array();
					while($promoRaw1 = $res1->fetchRow()) {
						if(count($promoRaw1['controllers']) > 0)
							$promoRaw['controllers'] = explode(',', $promoRaw1['controllers']);
					}
				}
				else
					$promoRaw['controllers'] = explode(',', $promoRaw['controllers']);

				$promos[$promoRaw['lang_iso']][] = $promoRaw;
			}
		}



		$this->vrat .= '<h3>'.I18n::tr('Promos').'</h3>';
		$this->vrat .= $this->langTabs->getOutputLinks();
		$this->vrat .= '<div class="tab-container">';

		$tabData = array();
		$data = array(
			'sectionId'				=> $this->section,
			'controllers'			=> $this->getPromoControllers(),
			'promoTitleMaxLength'	=> self::PROMO_TITLE_MAX_LENGTH,
			'promoTextMaxLength'	=> self::PROMO_TEXT_MAX_LENGTH,
			'sectionId'				=> $this->section,
			'mode'					=> $mode,
		);
		foreach ($this->langTabs->getLanguageIsoValues() as $iso) {
			if(!empty($promos[$iso]))
				$data['promos'] = $promos[$iso];
			else
				$data['promos'] = array();
			
			$data['langIso'] = $iso;
			$tabData[$iso] = Utils::processTemplate('Template/Promo/list-promos.phtml', $data, true);
		}

		$this->vrat .= $this->langTabs->getOutputTabs($tabData);
		$this->vrat .= $this->langTabs->getOutputJs();
		$this->vrat .= '</div>';
	}



	private function NewPromo($controllerId=null, $fieldData=array()) {
		$this->vrat .= '<h3>'.I18n::tr('New Promo').'</h3>';
		$this->vrat .= '<div class="tab-container">';

		$data = array(
			'controllers'			=> $this->getPromoControllers(),
			'languages'				=> $this->langTabs->getLanguages(),
			'sectionId'				=> $this->section,
			'fieldData'				=> $fieldData,
			'promoTitleMaxLength'	=> self::PROMO_TITLE_MAX_LENGTH,
			'promoTextMaxLength'	=> self::PROMO_TEXT_MAX_LENGTH,
			'preSelController'		=> $controllerId,
		);
		$this->vrat .= Utils::processTemplate('Template/Promo/new-promo.phtml', $data, true);
		$this->vrat .= '</div>';
	}



	private function DeletePromo() {

		try{
			$this->dbGame->autoCommit(false);

			$sql = $this->dbGame->prepare("
				DELETE FROM promo
				WHERE promo_id = ?
			");
			$res = $this->dbGame->execute($sql, array($_POST['promo_id']));
			DbUtil::testResult($res);

			$sql = $this->dbGame->prepare("
				DELETE FROM controller_has_promo
				WHERE promo_id = ?
			");
			$res = $this->dbGame->execute($sql, array($_POST['promo_id']));
			DbUtil::testResult($res);

			$this->dbGame->commit();
			$this->dbGame->autoCommit(true);
			$this->vrat .= UiUtil::printMessages('promo-deleted');
		}

		catch(exception $e) {
			$this->dbGame->rollback();
			$this->vrat .= UiUtil::printErrors('delete-error');
			throw new Exception($e);
		}
	}



	private function getPromoControllers() {
		//FIXME: variable not escaped, but I cant figure out how to do it clenaly - Martin
		$sql = $this->dbGame->prepare("
			SELECT c_id, text
			FROM controller_convert
			WHERE c_id IN (".implode(',',$this->promoControllerMaping).")
			GROUP BY c_id
			ORDER BY c_id;
		");
		$res = $this->dbGame->execute($sql, array());
		DbUtil::testResult($res);

		$controllers = array();
		while ($row = $res->fetchRow()) {
			$controllers[] = $row;
		}

		$controllers[] = array(
			'c_id' => 0, 
			'text' => 'Default'
		);

		return $controllers;
	}
	
	
	
	private function PromoCalendar($controllerId) {
		$where				= "
			platne_do >= '".It6_Date::dbNow()."'
			AND platne_od < '".It6_Date::timestampToDb(It6_Date::nowAsTimestamp() + self::PROMO_CALENDAR_SCOPE * 24 * 3600)."'
		";
		$order				= 'platne_do ASC';
		$fields				= 'p.platne_od, p.platne_do, p.title, j.iso AS lang_iso';
		$promosRaw			= $this->getPromos($controllerId, $fields, $where, $order);
		$lastMdnghtTmpSt	= It6_Date::toTimestamp(It6_Date::nowAsDate());
		$calenderEndTmpSt	= $lastMdnghtTmpSt + (self::PROMO_CALENDAR_SCOPE * 3600 * 24);
		$promos				= array();

		foreach($promosRaw as $promoRaw) {
			$promoRaw['validFromTmpSt']	= It6_Date::fromDbAsTimestamp($promoRaw['platne_od']);
			$promoRaw['validToTmpSt']	= It6_Date::fromDbAsTimestamp($promoRaw['platne_do']);
			
			
			if($promoRaw['validFromTmpSt'] < $lastMdnghtTmpSt) {
				$offsetSecs = 0;
				$promoRaw['validFromOverflow'] = true;
			}
			else {
				$offsetSecs = $promoRaw['validFromTmpSt'] - $lastMdnghtTmpSt;
				$promoRaw['validFromOverflow'] = false;
			}

			if($promoRaw['validToTmpSt'] > $calenderEndTmpSt) {
				$widthSecs = $calenderEndTmpSt - max($lastMdnghtTmpSt, $promoRaw['validFromTmpSt']);
				$promoRaw['validToOverflow'] = true;
			}
			else {
				$widthSecs = $promoRaw['validToTmpSt'] - max($lastMdnghtTmpSt, $promoRaw['validFromTmpSt']);
				$promoRaw['validToOverflow'] = false;
			}

			$promoRaw['offset']	= $offsetSecs / 3600 * self::PROMO_CALNDER_PX_PER_HOUR;
			$promoRaw['width']	= $widthSecs/ 3600 * self::PROMO_CALNDER_PX_PER_HOUR;
			
			$promos[$promoRaw['lang_iso']][] = $promoRaw;
		}


		$this->vrat .= '<h3>'.I18n::tr('Promo Calendar').'</h3>';
		$this->vrat .= $this->langTabs->getOutputLinks();
		$this->vrat .= '<div class="tab-container">';

		$tabData = array();
		foreach ($this->langTabs->getLanguageIsoValues() as $iso) {
			$data = array(
				'sectionId'=> $this->section,
				'promos'				=> isset($promos[$iso]) ? $promos[$iso] : null,
				'calendarScope'			=> self::PROMO_CALENDAR_SCOPE,
				'pxPerHour'				=> self::PROMO_CALNDER_PX_PER_HOUR,
			);
			$tabData[$iso] = Utils::processTemplate('Template/Promo/promo-calendar.phtml', $data, true);
		}

		$this->vrat .= $this->langTabs->getOutputTabs($tabData);
		$this->vrat .= $this->langTabs->getOutputJs();
		$this->vrat .= '</div>';
	}



	public function getPromos($controllerId=null, $fields, $where, $order) {
		if($controllerId !== null)
			$where .= ' AND chp.controller_id = '.$controllerId;
		else
			$where .= ' AND chp.promo_id IS NULL';
		
		$sql = $this->dbGame->prepare("
			SELECT $fields
			FROM promo AS p
			JOIN jazyky AS j
				ON p.lang_id = j.lang_id
			LEFT JOIN controller_has_promo AS chp
				ON chp.promo_id = p.promo_id
			WHERE $where
			GROUP BY p.promo_id
			ORDER BY $order
		");
		$res = $this->dbGame->execute($sql, array());
		DbUtil::testResult($res);
		
		$promosRaw= array();
		while($row = $res->fetchRow()) {
			$promosRaw[] = $row;
		}
		
		return $promosRaw;
	}



	public function previewPromo() {
		$this->layout = 'catalog';
		$this->vrat = Utils::processTemplate('Template/Promo/preview-promo.phtml', array('promo'=>array()), true);;
	}
}
