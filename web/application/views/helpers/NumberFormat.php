<?php


class Zend_View_Helper_NumberFormat extends Zend_View_Helper_Abstract {
	 
	public $view;

	 
	public function NumberFormat($number) {

		switch ($_SESSION['lang']) {
		case 'cs':
		case 'sk':					
			return number_format($number, 0, ',', ' ');
		case 'en':
			return number_format($number, 0, '.', '.');
		default:
			return $number;
		}
	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}


}