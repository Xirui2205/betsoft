<?php

class ReportController extends It6_Controller_Abstract {

	const INDEX_SECTION_ID = 393;

	public function indexAction() {

		$inData			= $this->getRequest()->getParams();

		$extensions		= array();
        		
		$this->view->indexSectionId = self::INDEX_SECTION_ID;

		//prepare wsForm data from $_POST
		$this->filterData = empty($inData['filter']) ? null : $inData['filter'];
        
		$reports = array(
				It6_Models_Reports::REPORT_NAME_USER_ACCOUNTS => i18n::tr("user_accounts"),
				It6_Models_Reports::REPORT_NAME_HOST_ACCOUNTS => i18n::tr("host_accounts")
			);

		$filterCfg = array(
            array(i18n::tr('date_from'), 'dateFrom', 'date', array(array('from', '>=', '?')), 'It6_Validate_Date', null, 'start'),
            array(i18n::tr('date_to'), 'dateTo', 'date', array(array('to', '<', '?')), 'It6_Validate_Date', null, 'to'),
			array(i18n::tr('report_type'), 'report_type', 'select', array(array('report_type', '=', '?')), null, null, $reports),
		);
		
		$filter = new It6_WsForm_Filter($filterCfg);
        
		$filter->getExtension($this->filterData, $extensions);

		//create table
		$tableCfg = array(
            array('item_name', 'itemName'),
            array('amount', 'amount'),
			array('correction', 'correction'),
			array('total', 'total')	
		);
		
		$table = new It6_WsForm_Table($tableCfg);
		$table->getColumnsExtension($extensions);

		if ( !empty($inData) ) {
			$lines = It6_Models_Reports::getReport($this->filterData);
		} else {
			$lines = array();
		}
		               
		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->tHead		= $table->getTheadLayout();
        $this->view->tBody      = $table->getTbodyLayout("report-tbody", $lines);
	}

}