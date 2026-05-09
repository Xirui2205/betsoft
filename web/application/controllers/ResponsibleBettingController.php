<?php
class ResponsibleBettingController extends Zend_Controller_Action {

	public function indexAction(){
		Models_BasicRender::render($this->view,$this->_request);

		$this->view->result = false;

		if($_POST["submit"]){
			//sectu hodnoty z otazek
			$totalSum = 0;
			foreach($_POST as $key => $value) {
				if (is_numeric($value))
					$totalSum += $value;
			}
			$this->view->result = true;

			$this->view->totalSum = $totalSum;
		}

	}
}