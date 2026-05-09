<?php


class Zend_View_Helper_Lang extends Zend_View_Helper_Abstract {
	 
	public $view;

	 
	public function lang($key) {

		$languages = Zend_Registry::get('languages');
		switch ($key) {
		case 'currentIso':
			return $_SESSION['lang'];
		case 'currentFlagId':
			return $languages[$_SESSION['lang']]['flag_oblast_id'];
		case 'currentText':
			return $languages[$_SESSION['lang']]['text'];
		case 'all':
			return Zend_Registry::get('languages');
		default:
			return '';
		}
	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}


}