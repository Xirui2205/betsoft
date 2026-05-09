<?php

class Anketa {
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
	 * Konstruktor
	 *
	 *Pokud neni identifikator spojeni predan vytvori se nove spojeni
	 *
	 * @param int $section id aktualni sekce
	 * @param PEAR::DB $dbGame objekt spojeni s databazi
	 */
	public function __construct($section = 0, $_database = null) {

		$this->section = $section;

		if ($_database == null) {
			if ($database = Ntw_Db_Manager :: getConnection('game')) {
				self :: setDatabase($database);
			} else {
				throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8', "admin_ex_db");
			}
		} else {
			self :: setDatabase($database);
		}
		self :: setConfig(new Zend_Config_Ini('config/application.ini', Ntf_Environment :: getCurrent()));

		self :: setCache(Zend_Cache :: factory('Core', 'File', self :: getConfig()->cache->frontend->toArray(), self :: getConfig()->cache->backend->toArray()));

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
		} else {
			$this->indexAction();
		}
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
		/*
		$writer = new Zend_Log_Writer_Stream('/tmp//saveoption.log');
		$logger = new Zend_Log($writer);
		$logger->info(print_R($_REQUEST, true));
		*/
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
		$writer = new Zend_Log_Writer_Stream('/tmp//addoption.log');
		$logger = new Zend_Log($writer);
		$logger->info(print_R($_REQUEST, true));
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

	public function graphdatasourceAction() {
		$db = self :: getDatabase();
		$dataSelect = $db->select()->from(array (
			'piv' => 'public_inquiry_vote'
		), array (
			'pio.id',
			'count(piv.id) as votes'
		))->joinLeft(array (
			'pio' => 'public_inquiry_option'
		), 'piv.option_id=pio.id', array ())->where('piv.question_id=?', $_REQUEST['questionId'])->group('piv.option_id');

		$data = $dataSelect->query()->fetchAll();
		foreach ($data as $i => $row) {
			$rowModified = array (
				$row['id'],
				$row['votes']
			);
			$data[$i] = $rowModified;
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