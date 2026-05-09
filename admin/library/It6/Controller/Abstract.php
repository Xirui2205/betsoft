<?php

class It6_Controller_Abstract extends Zend_Controller_Action {

	protected $section = null;
	protected $menu = null;
	protected $breadCrumb = null;
	protected $userId = null;
	protected $resourceId = null;
	protected $usecase = 'read';
	protected $jsIncludes = null;
	
	
	
	public function init() {
		$db = Zend_Registry::get('zdb_admin');
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		$row = $db->select()->from('sekce', array('sekce_id', 'acl_resource_id'))
			->where('sekce_id=?',Zend_Registry::get('section'))
			->query()
			->fetch();

		if ($row) {
			$this->section = $row['sekce_id'];
			$this->resourceId = $row['acl_resource_id'];
		}

		$acl = Zend_Registry::get('acl');
		if (!$acl->isResourceAllowed($this->resourceId, $this->usecase, true)) {
			$this->getRequest()->setControllerName('index');
			$this->getRequest()->setActionName('access-denied');
		}
		$this->initLayout();
	}

	protected function initMenu() {
		$db = DbUtil::connectAdminDb();
		$this->menu = new Menu($db, $this->section);
	}

	protected function initLayout() {
		$this->initMenu();
		$layout = Zend_Layout::getMvcInstance();
		if ($this->getRequest()->isXmlHttpRequest()) $layout->setLayout('no-menu');
		$layout->menu = $this->menu->getMenu();
		$layout->breadCrumb = $this->menu->getBreadCrumb($this->section);
		$user = It6_Session_Admin::getUserData();
		$this->userId = $user['id'];
		$layout->user = "{$user['firstName']} {$user['surname']} ({$user['username']})";
		$layout->host = HOST;
		$layout->jshost = JSHOST;
		$layout->protocol = PROTOCOL;
		$layout->title = $this->menu->getName($this->section);
		$layout->errorMessage = '';
		$layout->errorClass = '';
		$layout->jsIncludes = new It6_JsFiles();
		$this->jsIncludes = &$layout->jsIncludes;
	}

	protected function prepareChildSectionList() {
		$this->view->childSections = $this->menu->getChildSections();
	}

	public function accessDeniedAction() {
		$this->view->content = UiUtil::printErrors(
			I18n::tr('Access denied. UID: {1}, SECTION: {2}, RESOURCE: {3}, USECASE: {4}',
				$this->userId,
				$this->section,
				$this->resourceId,
				$this->usecase)
		);
	}

	public function registerJsInclude($name, $register = true) {
		$this->jsIncludes->$name = ($register ? true : false);
	}
}
