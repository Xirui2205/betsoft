<?php


class Obsah extends AbstractSection{


/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private $dbGame;

private $promoControllerList;

private $promoControllerListNames;

	/**
	 * instance of LanguageTabs
	 * @access private
	 * @var object
	 */
	private $langTabs;


	/**
	* Konstruktor
	*
	*Pokud neni identifikator spojeni predan vytvori se nove spojeni
	*
	* @param int $section id aktualni sekce
	* @param PEAR::DB $dbGame objekt spojeni s databazi
	*/
	public function __construct($section = 0, $dbGame = null){
		parent::__construct($section);

		$this->langTabs = new LanguageTabs(null, $this->dbGame);

		if($dbGame == null)
			$this->dbGame = DbUtil::connectWebDb();
		else
			$this->dbGame = $dbGame;


		$this->promoControllerList = array (
		  161=>44,
		);
		$this->promoControllerListNames = array (
		  161=>'Superhomepage'
		);
	}



	/**
	 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
	 * @return void
	 */
	public function runAction() {

		if(isset($_POST['update_content'])) {
			if($this->update)
				$this->UpdateContent();
			else
				$this->vrat .= UiUtil::printErrors('Insufficient Privilegies ');
		}


		$this->ShowContent();

		$this->dbGame->disconnect();
	}



	/**
	 * Updates content in db
	 * @return void
	 */
	private function UpdateContent() {
		try {
			$data = array(
				'title'			=> $_POST['title'],
				'image_name'	=> $_POST['image_name'],
				'url'			=> $_POST['url'],
				'text'			=> $_POST['text'],
				'target'		=> $_POST['target']
			);

			$where['controller_id = ?']	= $_POST['controller_id'];
			$where['location_id = ?']	= $_POST['location_id'];
			$where['lang_id = ?']		= $_POST['lang_id'];

			Zend_Registry::get('db')->update('banner', $data, $where);
			$this->vrat .= UiUtil::printMessages('banners-updated');

			It6_Log::info(
				'Banner with id '.$_POST['banner_id'].'was updated.',
				It6_Log::TAG_ADMIN_OPERATION
			);
		}

		catch(exception $e) {
			throw new Exception($e);
			$this->vrat .= UiUtil::printErrors('update-error');
		}

	}



	/**
	 * Shows the content forms
	 * @return void
	 */
	private function ShowContent(){
		$banners		= array();
		$tabData		= array();
		$controllerId	= $this->promoControllerList[$this->section];

		$res = Zend_Registry::get('db')->select()
			->from(array('b' => 'banner'))
			->join(
				array('j' => 'jazyky'),
				'j.lang_id = b.lang_id',
				array('iso')
			)
			->join(
				array('cc' => 'controller_convert'),
				'b.controller_id = cc.c_id AND b.lang_id = cc.lang_id',
				array('controllerName' => 'text')
			)
			->where('b.controller_id=?', $controllerId)
			->query()->fetchAll();


		foreach($res as $banner) {
			$banners[$banner['iso']][] = $banner;
		}



		$this->vrat .= '<h3>'.I18n::tr('Banners').'</h3>';
		$this->vrat .= $this->langTabs->getOutputLinks();
		$this->vrat .= '<div class="tab-container">';

		foreach ($this->langTabs->getLanguageIsoValues() as $iso) {
			if(!empty($banners[$iso])) {
				$data = array(
					'banners'		=> $banners[$iso],
					'sectionId'		=> $this->section
				);
				$tabData[$iso] = Utils::processTemplate('Template/Obsah/main.phtml', $data, true);
			}
		}

		$this->vrat .= $this->langTabs->getOutputTabs($tabData);
		$this->vrat .= $this->langTabs->getOutputJs();
	}
}
