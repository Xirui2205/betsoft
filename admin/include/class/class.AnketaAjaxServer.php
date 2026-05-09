<?php


class AnketaAjaxServer {
	/**
	 * Database connection.
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 * @access protected
	 * @static
	 */
	protected static $_database;

	/**
	 * Config.
	 *
	 * @var Zend_Config
	 * @access protected
	 * @static
	 */
	protected static $_config;

	/**
	 * Config.
	 *
	 * @var Zend_Cache_Core
	 * @access protected
	 * @static
	 */
	protected static $_cache;

	/**
	 * Ntf translate object.
	 *
	 * @var Ntf_Translate
	 * @access protected
	 */
	protected $_translator;

	/**
	 * pravo zmeny v sekci
	 * @access private
	 * @var int
	 */
	private $update = 0;

	/**
	 * pravo vymazani v sekci
	 * @access private
	 * @var int
	 */
	private $delete = 0;

	/**
	 * content
	 * @access private
	 * @var string
	 */
	private $content;

	/**
	 * aktualni sekce
	 * @access private
	 * @var int
	 */
	private $section;

	/**
	 * Konstruktorexportdatavotesexportdatavotes
	 *
	 *Pokud neni identifikator spojeni predan vytvori se nove spojeni
	 *
	 * @param int $section id aktualni sekce
	 * @param PEAR::DB $dbGame objekt spojeni s databazi
	 */
	public function __construct($section = 0, $_database = null) {

		$this->section = $section;
		self :: setConfig(new Zend_Config_Ini('config/application.ini', Ntf_Environment :: getCurrent()));
		Ntw_Db_Manager :: setConfig(self::getConfig()->database);
		self :: setCache(Zend_Cache :: factory('Core', 'File', self :: getConfig()->cache->frontend->toArray(), self :: getConfig()->cache->backend->toArray()));
		if ($_database == null) {
			if ($database = Ntw_Db_Manager :: getConnection('game')) {
				self :: setDatabase($database);
			} else {
				throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8', "admin_ex_db");
			}
		} else {
			self :: setDatabase($database);
		}

		Ntf_Translate :: setCache(self :: getCache());
		Ntf_Translate_Adapter_Db :: setDatabase(Ntw_Db_Manager :: getConnection('game'));
		$this->setTranslator(new Ntf_Translate('Ntf_Translate_Adapter_Db', null, 'en'));
	}

	/**
	 * Sets database connection.
	 *
	 * @param Zend_Db_Adapter_Abstract $database
	 * @return void
	 * @access public
	 * @static
	 */
	public static function setDatabase(Zend_Db_Adapter_Abstract $database) {
		self :: $_database = $database;
	}

	/**
	 * Gets database connection.
	 *
	 * @return Zend_Db_Adapter_Abstract
	 * @access public
	 * @static
	 */
	public static function getDatabase() {
		return self :: $_database;
	}

	/**
	 * Retrieve translator object
	 *
	 * @return Ntf_Translate|null
	 * @access public
	 */
	public function getTranslator() {
		return $this->_translator;
	}

	/**
	 * Sets config.
	 *
	 * @param Zend_Config $_config
	 * @return void
	 * @access public
	 * @static
	 */
	public static function setConfig(Zend_Config $_config) {
		self :: $_config = $_config;
	}

	/**
	 * Gets config.
	 *
	 * @return Zend_Config
	 * @access public
	 * @static
	 */
	public static function getConfig() {
		return self :: $_config;
	}

	/**
	 * Sets cache.
	 *
	 * @param Zend_Cache_Core $_cache
	 * @return void
	 * @access public
	 * @static
	 */
	public static function setCache(Zend_Cache_Core $_cache) {
		self :: $_cache = $_cache;
	}

	/**
	 * Gets config.
	 *
	 * @return Zend_Cache_Core
	 * @access public
	 * @static
	 */
	public static function getCache() {
		return self :: $_cache;
	}

	/**
	 * Nastaveni prav k sekci
	 * @param int $update pravo zapisu
	 * @param int $delete pravo smazani
	 * @return void
	 */
	public function setPrivileges($update, $delete) {
		$this->update = $update;
		$this->delete = $delete;
	}

	/**
	 * Sets translator for this class.
	 *
	 * @param  Ntf_Translate|Ntf_Translate_Adapter|null $translator
	 * @return Ntf_Template
	 */
	public function setTranslator($translator = null) {
		$this->_translator = $translator;
		return $this;
	}

	/**
	 * Vraci vystup do tridy main
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * Nastavuje vystup do tridy main
	 * @return string
	 */
	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	/**
	 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
	 * @return void
	 */
	public function runAction() {
		if (array_key_exists('action', $_REQUEST)) {
			$action = $_REQUEST['action'];
			$methodAction = $action . 'Action';
			if (method_exists($this, $methodAction)) {
				$this-> $methodAction ();
			}
		}
	}

	public function statsAction() {
		$tpl = new Ntf_Template('_tpl/publicinquiry/stats.tpl.html', $this->getTranslator());
		$tpl->setDisableTranslator(false);
		$this->setContent($tpl->getOutputContent());
	}

	public function saveinquiryAction() {
		$db = self :: getDatabase();
		$id = $_REQUEST['id'];
		$data = array (
			'label' => $_REQUEST['label'],
			'is_active' => $_REQUEST['is_active']
		);

		$db->beginTransaction();
		if ($data['is_active'] == 1) {
			$db->update('public_inquiry', array (
				'is_active' => 0
			));
		}
		$db->update('public_inquiry', $data, 'id=' . $id);
		$db->commit();
	}

	public function savequestionAction() {
		$db = self :: getDatabase();
		$id = $_REQUEST['id'];
		$data = array (
			'label' => $_REQUEST['label'],
			'is_active' => $_REQUEST['is_active']
		);
		$db->update('public_inquiry_question', $data, 'id=' . $id);
	}

	public function saveoptionAction() {
		$db = self :: getDatabase();
		$id = $_REQUEST['id'];
		$data = array (
			'question_id' => $_REQUEST['questionId'],
			'label' => $_REQUEST['label']
		);
		$db->update('public_inquiry_option', $data, 'id=' . $id);
	}

	public function addinquiryAction() {
		$db = self :: getDatabase();
		$data = array (
			'label' => $_REQUEST['label'],
			'is_active' => $_REQUEST['is_active']
		);

		$db->beginTransaction();
		if ($data['is_active'] == 1) {
			$db->update('public_inquiry', array (
				'is_active' => 0
			));
		}
		$db->insert('public_inquiry', $data);
		$db->commit();
	}

	public function addquestionAction() {
		$db = self :: getDatabase();
		$data = array (
			'inquiry_id' => $_REQUEST['inquiryId'],
			'label' => $_REQUEST['label'],
			'is_active' => $_REQUEST['is_active']
		);
		$db->insert('public_inquiry_question', $data);
	}

	public function addoptionAction() {
		$db = self :: getDatabase();
		$data = array (
			'question_id' => $_REQUEST['questionId'],
			'label' => $_REQUEST['label']
		);
		$db->insert('public_inquiry_option', $data);
	}

	public function deleteinquiryAction() {
		$db = self :: getDatabase();
		$id = $_REQUEST['id'];
		$db->delete('public_inquiry', 'id=' . $id);
	}

	public function deletequestionAction() {
		$db = self :: getDatabase();
		$id = $_REQUEST['id'];
		$db->delete('public_inquiry_question', 'id=' . $id);
	}

	public function deleteoptionAction() {
		$db = self :: getDatabase();
		$id = $_REQUEST['id'];
		$db->delete('public_inquiry_option', 'id=' . $id);
	}

	public function exportdatavotesAction() {
		$this->griddatavotesAction();
		$data=Zend_Json::decode($this->getContent());
		header("Expires: 0");
		header("Cache-control: private");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Description: File Transfer");
		header("Content-Type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=voters.xls");
		
		foreach($data['rows'] as $row){
			print implode(',', $row['cell'])."\n";
			
		}
		exit;
	}

	public function graphdatasourceAction() {
		$db = self :: getDatabase();
		$dataSelect = $db->select()->from(array (
			'piv' => 'public_inquiry_vote'
		), array (
			'pio.label',
			'count(piv.id) as votes'
		))->joinLeft(array (
			'pio' => 'public_inquiry_option'
		), 'piv.option_id=pio.id', array ())
		->where('piv.question_id=?', $_REQUEST['questionId'])
		->group('piv.option_id');

		if (array_key_exists('nick', $_REQUEST) && strlen($_REQUEST['nick']) > 0) {
			$dataSelect->where('u.nick like ?', $_REQUEST['nick']);
		}
		if (array_key_exists('answer', $_REQUEST) && preg_match('/^[0-9]+$/', $_REQUEST['answer'])) {
			$dataSelect->where('pio.id=?', $_REQUEST['answer']);
		}
		if (array_key_exists('ageTo', $_REQUEST) && preg_match('/^[0-9]+$/', $_REQUEST['ageTo'])) {
			$dataSelect->where('piv.age<=? and piv.age is not null', $_REQUEST['ageTo']);
		}
		if (array_key_exists('ageFrom', $_REQUEST) && preg_match('/^[0-9]+$/', $_REQUEST['ageFrom'])) {
			$dataSelect->where('piv.age>=? and piv.age is not null', $_REQUEST['ageFrom']);
		}
		if (array_key_exists('country', $_REQUEST) && preg_match('/^[0-9]+$/', $_REQUEST['country'])) {
			$dataSelect->where('piv.country_id=?', $_REQUEST['country']);
		}
		if (array_key_exists('gender', $_REQUEST) && strlen($_REQUEST['gender']) > 0) {
			$dataSelect->where('piv.gender=?', $_REQUEST['gender']);
		}
		if (array_key_exists('timeFrom', $_REQUEST) && strlen($_REQUEST['timeFrom']) > 0) {
			$dataSelect->where('piv.timestamp>=?', $_REQUEST['timeFrom'].' 00:00:00');
		}
		if (array_key_exists('timeTo', $_REQUEST) && strlen($_REQUEST['timeTo']) > 0) {
			$dataSelect->where('piv.timestamp<=?', $_REQUEST['timeTo'].' 23:59:59');
		}

		$data = $dataSelect->query()->fetchAll();
		foreach ($data as $key => $value) {
			$data[$key]['label'] = $this->getTranslator()->translate('##txt:' . $value['label'] . '##');
		}
		$this->setContent(Zend_Json :: encode($data));
	}

	public function inquiryAction() {
		$tpl = new Ntf_Template('_tpl/publicinquiry/inquiry.tpl.html', $this->getTranslator());
		$tpl->setDisableTranslator(false);

		if (array_key_exists('id', $_REQUEST)) {
			$db = self :: getDatabase();
			$tpl->assign('id', $_REQUEST['id']);
			$items = $db->select()->from(array (
				'pi' => 'public_inquiry'
			))->where('pi.id=?', $_REQUEST['id'])->query()->fetchAll();
			if (count($items) == 1) {

				$tpl->assign('ID', $items[0]['id']);
				$tpl->assign('Label', $items[0]['label']);
				$tpl->assign('CreateDate', $items[0]['create_date']);
				$tpl->assign(($items[0]['is_active'] == 1) ? 'ActiveYes' : 'ActiveNo', 'checked="true"');

				$questions = $db->select()->from(array (
					'piq' => 'public_inquiry_question'
				))->where('piq.inquiry_id=?', $items[0]['id'])->query()->fetchAll();
			}
		}
		$db = self :: getDatabase();
		$coutries = $db->select()->from(array (
			'z' => 'zeme'
		), array (
			'z.zeme_id as id',
			'p.text'
		))->joinLeft(array (
			'p' => 'preklady'
		), 'z.nazev=p.index_pole', array ())->where('p.lang_id=2')->order('p.text asc')->query()->fetchAll();
		foreach ($coutries as $key => $value) {
			$tpl->newBlock('Country');
			$tpl->assign('ID', $value['id']);
			$tpl->assign('Label', $value['text']);
			$tpl->gotoBlock("_ROOT");
		}
		for ($i = 18; $i <= 99; $i++) {
			$tpl->newBlock('AgeFrom');
			$tpl->assign('ID', $i);
			$tpl->assign('Label', $i);
			$tpl->gotoBlock("_ROOT");

			$tpl->newBlock('AgeTo');
			$tpl->assign('ID', $i);
			$tpl->assign('Label', $i);
			$tpl->gotoBlock("_ROOT");

		}
		$this->setContent($tpl->getOutputContent());
	}

	public function griddatainquiryAction() {
		$db = self :: getDatabase();
		$gridSelect = $db->select()->from(array (
			'pi' => 'public_inquiry'
		), array (
			'pi.id',
			'pi.label',
			'count(piv.id)+0 as voted',
			'pi.create_date',
			'pi.is_active'
		))->joinLeft(array (
			'piq' => 'public_inquiry_question'
		), 'pi.id=piq.inquiry_id', array ())->joinLeft(array (
			'piv' => 'public_inquiry_vote'
		), 'piq.id=piv.question_id', array ())->group('pi.id');

		if (array_key_exists('sidx', $_REQUEST) && array_key_exists('sord', $_REQUEST)) {
			$gridSelect->order($_REQUEST['sidx'] . ' ' . $_REQUEST['sord']);
		}

		$Paginator = Zend_Paginator :: factory($gridSelect);

		if (array_key_exists('rows', $_REQUEST)) {
			$Paginator->setItemCountPerPage($_REQUEST['rows']);
		} else {
			$Paginator->setItemCountPerPage(14);
		}

		if (array_key_exists('page', $_REQUEST)) {
			$Paginator->setCurrentPageNumber($_REQUEST['page']);
		} else {
			$Paginator->setCurrentPageNumber(1);
		}

		$result = new stdClass();
		$result->page = $Paginator->getPages()->current;
		$result->total = $Paginator->getPages()->pageCount;
		$result->records = $Paginator->getPages()->totalItemCount;
		$result->rows = array ();

		foreach ($Paginator->getCurrentItems() as $n) {
			$row = new stdClass();
			$row->id = $n['id'];
			$row->cell = array_values($n);
			$result->rows[] = $row;
		}
		$this->setContent(Zend_Json :: encode($result));
	}

	public function griddataquestionsAction() {

		$db = self :: getDatabase();

		$gridSelect = $db->select()->from(array (
			'piq' => 'public_inquiry_question'
		), array (
			'piq.id',
			'piq.label',
			'piq.create_date',
			'piq.is_active'
		));

		if (array_key_exists('inquiryId', $_REQUEST) && $_REQUEST['inquiryId'] > 0) {
			$gridSelect->where('piq.inquiry_id=?', $_REQUEST['inquiryId']);
		}
		if (array_key_exists('sidx', $_REQUEST) && array_key_exists('sord', $_REQUEST)) {
			$gridSelect->order($_REQUEST['sidx'] . ' ' . $_REQUEST['sord']);
		}

		$Paginator = Zend_Paginator :: factory($gridSelect);

		if (array_key_exists('rows', $_REQUEST)) {
			$Paginator->setItemCountPerPage($_REQUEST['rows']);
		} else {
			$Paginator->setItemCountPerPage(14);
		}

		if (array_key_exists('page', $_REQUEST)) {
			$Paginator->setCurrentPageNumber($_REQUEST['page']);
		} else {
			$Paginator->setCurrentPageNumber(1);
		}

		$result = new stdClass();
		$result->page = $Paginator->getPages()->current;
		$result->total = $Paginator->getPages()->pageCount;
		$result->records = $Paginator->getPages()->totalItemCount;
		$result->rows = array ();

		foreach ($Paginator->getCurrentItems() as $n) {
			$row = new stdClass();
			$row->id = $n['id'];
			$row->cell = array_values($n);
			$result->rows[] = $row;
		}
		$this->setContent(Zend_Json :: encode($result));
	}

	public function griddataoptionsAction() {

		$db = self :: getDatabase();

		$gridSelect = $db->select()->from(array (
			'pio' => 'public_inquiry_option'
		), array (
			'pio.id',
			'pio.label',
			'count(piv.id) as voted',
			'pio.create_date'
		))->joinLeft(array (
			'piv' => 'public_inquiry_vote'
		), 'pio.id=piv.option_id', array ())->group('pio.id');

		if (array_key_exists('questionId', $_REQUEST)) {
			$gridSelect->where('pio.question_id=?', $_REQUEST['questionId']);
		}
		if (array_key_exists('sidx', $_REQUEST) && array_key_exists('sord', $_REQUEST)) {
			$gridSelect->order($_REQUEST['sidx'] . ' ' . $_REQUEST['sord']);
		}

		$Paginator = Zend_Paginator :: factory($gridSelect);

		if (array_key_exists('rows', $_REQUEST)) {
			$Paginator->setItemCountPerPage($_REQUEST['rows']);
		} else {
			$Paginator->setItemCountPerPage(14);
		}

		if (array_key_exists('page', $_REQUEST)) {
			$Paginator->setCurrentPageNumber($_REQUEST['page']);
		} else {
			$Paginator->setCurrentPageNumber(1);
		}

		$result = new stdClass();
		$result->page = $Paginator->getPages()->current;
		$result->total = $Paginator->getPages()->pageCount;
		$result->records = $Paginator->getPages()->totalItemCount;
		$result->rows = array ();

		foreach ($Paginator->getCurrentItems() as $n) {
			$row = new stdClass();
			$row->id = $n['id'];
			$row->cell = array_values($n);
			$result->rows[] = $row;
		}
		$this->setContent(Zend_Json :: encode($result));
	}

	public function griddatavotesAction() {

		$db = self :: getDatabase();
		$gridSelect = $db->select()->from(array (
			'piv' => 'public_inquiry_vote'
		), array (
			'pio.id',
			'pio.label',
			'u.nick',
			'piv.age as age',
			'piv.gender as gender',
			'p.text as country',
			'piv.timestamp as time'
			
		))->joinLeft(array (
			'pio' => 'public_inquiry_option'
		), 'piv.option_id=pio.id', array ())->joinLeft(array (
			'u' => 'uzivatel'
		), 'piv.user_id=u.user_id', array ())->joinLeft(array (
			'z' => 'zeme'
		), 'piv.country_id=z.zeme_id', array ())->joinLeft(array (
			'p' => 'preklady'
		), 'z.nazev=p.index_pole', array ())->where('p.lang_id=2 or p.lang_id is null');

		if (array_key_exists('questionId', $_REQUEST)) {
			$gridSelect->where('pio.question_id=?', $_REQUEST['questionId']);
		}
		if (array_key_exists('nick', $_REQUEST) && strlen($_REQUEST['nick']) > 0) {
			$gridSelect->where('u.nick like ?', $_REQUEST['nick']);
		}
		if (array_key_exists('answer', $_REQUEST) && preg_match('/^[0-9]+$/', $_REQUEST['answer'])) {
			$gridSelect->where('pio.id=?', $_REQUEST['answer']);
		}
		if (array_key_exists('ageTo', $_REQUEST) && preg_match('/^[0-9]+$/', $_REQUEST['ageTo'])) {
			$gridSelect->where('piv.age<=? and piv.age is not null', $_REQUEST['ageTo']);
		}
		if (array_key_exists('ageFrom', $_REQUEST) && preg_match('/^[0-9]+$/', $_REQUEST['ageFrom'])) {
			$gridSelect->where('piv.age>=? and piv.age is not null', $_REQUEST['ageFrom']);
		}
		if (array_key_exists('country', $_REQUEST) && preg_match('/^[0-9]+$/', $_REQUEST['country'])) {
			$gridSelect->where('z.zeme_id=?', $_REQUEST['country']);
		}
		if (array_key_exists('gender', $_REQUEST) && strlen($_REQUEST['gender']) > 0) {
			$gridSelect->where('piv.gender=?', $_REQUEST['gender']);
		}
		if (array_key_exists('timeFrom', $_REQUEST) && strlen($_REQUEST['timeFrom']) > 0) {
			$gridSelect->where('piv.timestamp>=?', $_REQUEST['timeFrom'].' 00:00:00');
		}
		if (array_key_exists('timeTo', $_REQUEST) && strlen($_REQUEST['timeTo']) > 0) {
			$gridSelect->where('piv.timestamp<=?', $_REQUEST['timeTo'].' 23:59:59');
		}

		if (array_key_exists('sidx', $_REQUEST) && array_key_exists('sord', $_REQUEST)) {
			$gridSelect->order($_REQUEST['sidx'] . ' ' . $_REQUEST['sord']);
		}

		$Paginator = Zend_Paginator :: factory($gridSelect);

		if (array_key_exists('rows', $_REQUEST)) {
			$Paginator->setItemCountPerPage($_REQUEST['rows']);
		} else {
			$Paginator->setItemCountPerPage(14);
		}

		if (array_key_exists('page', $_REQUEST)) {
			$Paginator->setCurrentPageNumber($_REQUEST['page']);
		} else {
			$Paginator->setCurrentPageNumber(1);
		}

		$result = new stdClass();
		$result->page = $Paginator->getPages()->current;
		$result->total = $Paginator->getPages()->pageCount;
		$result->records = $Paginator->getPages()->totalItemCount;
		$result->rows = array ();

		foreach ($Paginator->getCurrentItems() as $n) {
			$row = new stdClass();
			$row->id = $n['id'];
			$row->cell = array_values($n);
			$result->rows[] = $row;
		}
		$this->setContent(Zend_Json :: encode($result));
	}

	public function gridAction() {
		$tpl = new Ntf_Template('_tpl/publicinquiry/grid.tpl.html', $this->getTranslator());
		$tpl->setDisableTranslator(false);
		$this->setContent($tpl->getOutputContent());
	}

	public function indexAction() {
		$tpl = new Ntf_Template('_tpl/publicinquiry/index.tpl.html', $this->getTranslator());
		$tpl->setDisableTranslator(false);
		$this->setContent($tpl->getOutputContent());
	}
}