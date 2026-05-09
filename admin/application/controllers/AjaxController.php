<?php
class AjaxController extends It6_Controller_Abstract{

	public function getRateAction(){
		$this->_helper->layout->setLayout('empty');
		$this->view->otherRate = Models_Ratio::getOtherRateByRatio($_GET["ratio"], $_GET["rate"], $_GET["rateNumber"]);
	}

}