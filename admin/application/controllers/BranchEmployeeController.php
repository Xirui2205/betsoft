<?php

class BranchEmployeeController extends controllers_BranchAbstractController {

	var $viewSectionId = 185;
	var $ws;

	public function init() {
		parent::init();
		//bookmaker_role
		$this->roleId = 5;

		$this->branchId = $this->getRequest()->getPost('branchId');
		$this->employeeId = $this->getRequest()->getPost('employeeId');
	}

	public function indexAction() {
	}

	public function viewAction() {
		$this->_helper->layout->setLayout('empty');

		$this->view->employees = $this->ws->Branch->getEmployees( (integer)$this->branchId, true );

		$this->view->branchId = $this->branchId;

		if ($this->employeeId) {
			$this->view->employee = $this->ws->Admin->getById( (integer)$this->employeeId );
		}
	}

	public function getTimesheetAction() {
		$this->_helper->layout->setLayout('empty');

		$from = It6_Date::toDbAsDate($this->getRequest()->getPost('timesheetFrom'));
		$to = It6_Date::toDbAsDate($this->getRequest()->getPost('timesheetTo'));

		$this->view->timesheet = $this->ws->Admin->getTimesheet( (integer)$this->employeeId, $from, $to );
	}


}
