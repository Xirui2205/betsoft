<?php

class IndexController extends It6_Controller_Abstract {

public function indexAction() {
	try{
		$this->view->content = '<div>This is new Zend MVC page</div>';
	}
	catch(exHandler $e){
		$this->view->error = $e->produceAllError();
	}
}

}