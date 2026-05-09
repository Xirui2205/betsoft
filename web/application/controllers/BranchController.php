<?php

class BranchController extends Zend_Controller_Action {

	public function indexAction() {
		Models_BasicRender::render($this->view,$this->_request);
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);

		Models_Branch::prepareView($this->view, $this->getRequest(), false);
	}
	
	
	public function allBranchesAction() {
		Models_BasicRender::render($this->view,$this->_request);
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);

		Models_Branch::prepareView($this->view, $this->getRequest(), false);
	}
}
