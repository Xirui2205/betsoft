<?php

class UsersProvisionController extends It6_Controller_Abstract {

	const USERS_PROVISION_THEAD_LAYOUT = 'users-provision-thead';
	const USERS_PROVISION_TBODY_LAYOUT = 'users-provision-tbody';
	const USERS_PROVISION_SECTION_ID = 394;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array();

	public function provisionAction() {
		$inData = $this->getRequest()->getParams();
		$extensions = array();

		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		$filter = new It6_WsForm_Filter(array(
			array('From', 'fromDate', 'dateTime', array(array('dateFrom'),'>=','?'), 'It6_Validate_Date',null,'start'),
			array('To', 'toDate', 'dateTime', array(array('dateTo','<=','?')), 'It6_Validate_Date',null,'end'),
		));

		$filter->getExtension($this->filterData, $extensions);

		$table = new It6_WsForm_Table(array(
			array('Branch handle','handle'),
			array('Branch name','name'),
			array('Name','jmeno'),
			array('Surname','prijmeni'),
			array('Email','email'),
			array('Telefon','telefon'),
			array('Player count','playerCount'),
			array('Provision','provision'),
		));

		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);

		if ( !empty($_REQUEST['form-submited'])  && It6_Date::checkDateFormat( $inData["filter"]["fromDate"] ) ) { //&& It6_Date::checkDateFormat( $inData["filter"]["toDate"] ) ) {
			$this->view->provisions = $items = Models_Admin::getAdminProvision($inData, $filter);
		} else {
			$this->view->provisions = $items = array();
			$this->view->notFilter = true;
		}

		if ( isset($_REQUEST['exportCSV']) ) {
			$provisions = It6_ArrayWrapper::toNativeArray($this->view->provisions);
			$userIds = $this->getRequest()->getParam("userIds");

			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"user-provision.csv\"");

			$provision = array();
			$i = 0;
			foreach ($provisions as $userProvision) {
				if (in_array($userProvision['admin_id'], $userIds)) {
					$provision[$i] = $userProvision;
					$i++;
				}
			}

			echo It6_Models_ExportHelper::assocArrayToCsv($provision);
		}

		$this->view->filter = $filter->getLayout(null, $this->filterData);
		$this->view->tHead = $table->getTheadLayout(static::USERS_PROVISION_THEAD_LAYOUT, $this->orderData);
		$this->view->tBody = $table->getTbodyLayout(static::USERS_PROVISION_TBODY_LAYOUT, $items);
	}

}