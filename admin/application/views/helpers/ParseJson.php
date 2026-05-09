<?php


class Zend_View_Helper_ParseJson extends Zend_View_Helper_Abstract {
	 
	public $view;

	public function parseJson($v, $iter = 0, $res = ''){
		if ($v!=NULL) {
			try {
				$vArr = Zend_Json::decode($v);
				return $this->view->parseArray($vArr, $iter);
			} catch(Exception $e) {
				
				return $this->view->parseArray($v, $iter);
			}
		} else return 'N/A ('. $v.')';
	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

}
