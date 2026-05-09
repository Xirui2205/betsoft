<?php

class AclController extends It6_Controller_Abstract {

public function indexAction() {
	$this->view->trees = It6_Models_AclTree::getDataAll();
	//Zend_Registry::get('acl')::
	$this->prepareChildSectionList();
}

} // class AclController
