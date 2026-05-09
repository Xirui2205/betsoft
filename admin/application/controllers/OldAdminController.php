<?php

class OldAdminController extends It6_Controller_Abstract {

	public function init() {
		$this->section = Zend_Registry::get('section');
		$db = Zend_Registry::get('zdb_admin');
		$row = $db->select()->from('sekce', array('sekce_id', 'acl_resource_id'))
			->where('sekce_id=?', $this->section)
			->query()
			->fetch();
		if (empty($row))
			$denied = true;
		else {
			$this->resourceId = $row['acl_resource_id'];
			$acl = Zend_Registry::get('acl');
			$denied = !$acl->isResourceAllowed($row['acl_resource_id'], $this->usecase, true);
		}
		if ($denied) {
			$this->getRequest()->setControllerName('index');
			$this->getRequest()->setActionName('access-denied');
		}
		$this->initLayout();
	}

	public function indexAction() {
		try{
			/*
			ob_start();
			Main::getInstance();
			$this->view->content = ob_get_contents();
			ob_end_clean();
			*/
			$main = Main::getInstance($this);
			$this->view->content = $main->getContent();
			$layout = Zend_Layout::getMvcInstance();
			$layoutName = $main->getLayout();
			if(!empty($layoutName)) {
				$layout->setLayout($layoutName);
			}

			$error = $main->getError();
			if (!empty($error)) {
				$layout->errorMessage = $error;
				$layout->errorClass = 'error';
			}
		}
		catch(exHandler $e){
			$this->view->error = $e->produceAllError();
		}
	}

}
