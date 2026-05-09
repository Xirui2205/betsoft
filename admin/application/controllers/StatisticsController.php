<?php

class StatisticsController extends It6_Controller_Abstract {

const STATISTICS_ID = 255;


public function  viewAction() {
	if ( isset($_REQUEST['CSV']) ) {
		$this->_helper->layout->disableLayout();
		header('Content-type: text/plain');
		header("Content-Disposition: attachment; filename=\"statistics.csv\"");
	}
	
	$ws = Zend_Registry::get('ws');
	
	$this->view->form = new Models_Form_StatisticsFilter(self::STATISTICS_ID);
	
	if ( !$this->view->form->isValid($this->getRequest()->getParams()) ) {
		//TODO add validation message
	}

	$values = $this->view->form->getValues();
	$this->view->form->populate($values);


	$group1 = $values['group1'];
	$group2 = $values['group2'];
	
	if ( $group2 == $group1 ) {
		$group2 = null;
	}

	$filter = array();
	$filter['timeFrom'] = empty($values['fromDate']) ? null : It6_Date::toDb($values['fromDate']);
	$filter['timeTo'] = empty($values['toDate']) ? null : It6_Date::toDb($values['toDate']); 

	if ( !empty($values['group1filter']) ) {
		
		if ( empty($this->view->form->group1s) )
			$filter[$group1] = $values['group1filter'];
		else {
			$filter[$group1] = array();

			foreach ( explode(';',$values['group1filter']) as $g ) {
				$tmp = array_flip($this->view->form->group1s);
				if ( !empty($tmp[$g]) )
					$filter[$group1][] = $tmp[$g];
				else 
					$filter[$group1][] = $g;
			}
		}
	}

	if ( !empty($values['group2filter']) ) {
		
		if ( empty($this->view->form->group2s) )
			$filter[$group2] = $values['group2filter'];
		else {
			$filter[$group2] = array();

			foreach ( explode(';',$values['group2filter']) as $g ) {
				$tmp = array_flip($this->view->form->group2s);
				if ( !empty($tmp[$g]) )
					$filter[$group2][] = $tmp[$g];
				else 
					$filter[$group2][] = $g;
			}
		}
	}

	$order = empty($_GET['order']) 
				? 'amount DESC'
				: strtr(key($_GET['order']), '_', ' ');

	$limit1 = empty($values['limit1']) ? null : $values['limit1'];
	$limit2 = empty($values['limit2']) ? null : $values['limit2'];
				
	if ( !empty($group1) ) {
		$this->view->group1 = $group1;
		$this->view->group2 = $group2;
		$this->view->statistics = $ws->Ticket->getStatistics(
			$this->view->group1,
			$this->view->group2,
			$filter, $order, $values['calcType'],
			$limit1, $limit2);
	}

}

} // StatisticsController
