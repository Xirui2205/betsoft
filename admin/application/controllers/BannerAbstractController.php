<?php

class controllers_BannerAbstractController extends It6_Controller_Abstract {

	protected $ws;

	public function init() {
		$this->ws					= Zend_Registry::get('ws');

		parent::init();

		if(isset($this->indexSectionId))
			$this->view->indexSectionId = $this->indexSectionId;
		if(isset($this->insertSectionId))
			$this->view->insertSectionId = $this->insertSectionId;
		if(isset($this->updateSectionId))
			$this->view->updateSectionId = $this->updateSectionId;
	}
}
