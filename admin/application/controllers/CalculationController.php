<?php

class CalculationController extends It6_Controller_Abstract {

	public static $ACCOUNTS = array ('0' => '--Select--', "211" => "211","221" => "221","324" => "324","378" => "378", "379" => "379","548" => "548","602" => "602");
	public static $SUBACCOUNTS = array ('0' => '--Select--','999' => '999','998' => '998','997' => '997');

	const CALCULATION_TBODY_LAYOUT = 'calculation-tbody';
	const CALCULATION_THEAD_LAYOUT = 'calculation-thead';
	const CALCULATION_TBODY_LAYOUT_BRANCH_INTERNET = 'calculation-branch-internet-tbody';
	const CALCULATION_THEAD_LAYOUT_BRANCH_INTERNET = 'calculation-branch-internet-thead';
	const BRANCH_SECTION_ID = 349;
	const BRANCH_NET_SECTION_ID = 350;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array();



	public function branchAction() {
		$inData = $this->getRequest()->getParams();
		$extensions = array();

		//prepare wsForm data from $_GET
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		$accounts = static::$ACCOUNTS;
		
		$ws = Zend_Registry::get('ws');
		 
		// Use this in your model, view and controller files
		
		$contract_templates = It6_ArrayWrapper::toNativeArray($ws->ext($extensions)->ContractTemplate->getAll());	
		$contract_templates = Models_Utils::getCollection($contract_templates, 'templateId','name',array('' => '--Select--'));
		
		$stems = It6_ArrayWrapper::toNativeArray($ws->ext($extensions)->Stem->getAll());
		$stems = Models_Utils::getCollection($stems, 'stemId','name',array('' => '--Select--'));
		
		$filter = new It6_WsForm_Filter(array(
			array('From', 'fromDate', 'date', array(array('dateFrom'),'>=','?'), 'It6_Validate_Date',null,'start'),
			array('To', 'toDate', 'date', array(array('dateTo','<=','?')), 'It6_Validate_Date',null,'end'),
			array('Branch handle', 'id', 'text', array(array('handle', '=', '?')), null, null, null),
			array('Contract Type', 'templateId', 'select', array(array('templateId',  null)), 'Zend_Validate_Int', NULL, $contract_templates),
			array('Stem', 'stemId', 'select', array(array('stem_id',  null)), 'Zend_Validate_Int', NULL, $stems),
			array('Active contract', 'active', 'select', array(array('active', null)), 'Zend_Validate_Digit', NULL, array('' => '--Select--', 'y' => 'Yes', 'n' => 'No')),
			array('Calculation', 'calculation_net', 'select', array(array('calculation_net', null)), 'Zend_Validate_Digit', NULL, array('' => '--Select--', 'y' => 'Yes', 'n' => 'No')),
			array('Branch active', 'is_active', 'select', array(array('is_active', null)), 'Zend_Validate_Digit', NULL, array('' => '--Select--', 'y' => 'Yes', 'n' => 'No')),
		));

		//$this->filterData['calculation'] = 1;

		//$logger = new Zend_Log(new Zend_Log_Writer_Firebug());
		//$logger->info($inData);
		
		$filter->getExtension($this->filterData, $extensions);

		//$logger->info($filter);
		//create table
		$table = new It6_WsForm_Table(array(
			array('Branch handle','branchHandle'),
			array('Branch name','branchName'),
			array('Stem', 'stem'),
			array('Branch is active', 'branchIsActive'),
			array('Branch active contract', 'branchActive'),
			array('Contract type', 'contracttype'),
			array('Contracts', 'contracts'),
			array('Valid from', 'branchValidFrom'),
			array('Valid to', 'branchValidTo'),			
			array('Branch in','branchIn'),
			array('Branch out','branchOut'),
			array('Branch mp','branchMp'),
			array('Branch in-out+mp','branchInMinusOut'),
			array('Branch payout out','branchPayoutOut'),
			array('Provision','provision'),
			/*
			array('Branch profit','branchProfit'),
			array('Brnanch profit no mp','branchProfitNoMp'),
			*/
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);
		
		/*
		$this->view->form = new Models_Form_BranchProvisionsFilter(static::BRANCH_PROVISIONS_SECTION_ID);
		
		if ( !$this->view->form->isValid($this->getRequest()->getParams()) ) {
			//TODO add validation message
		}

		$values = $this->view->form->getValues();
		$this->view->form->populate($values);

		$this->view->provisions = $ws->Branch->getProvisions($values['fromDate'] + " 00:00:00", $values['toDate'] + " 23:59:59" );
		*/

		if ( !empty($_REQUEST['form-submited'])  && It6_Date::checkDateFormat( $inData["filter"]["fromDate"] ) && It6_Date::checkDateFormat( $inData["filter"]["toDate"] ) ) {
			$this->view->provisions = $items = It6_ArrayWrapper::toNativeArray($ws->ext($extensions)->Branch->getProvisions());
		}
		else {
			$this->view->provisions = $items = array();
			$this->view->notFilter = true;
		}

		//$logger = new Zend_Log(new Zend_Log_Writer_Firebug());
		// $logger->info($items);	


		if ( isset($_REQUEST['exportCSV']) ) {
			$provisions = It6_ArrayWrapper::toNativeArray($this->view->provisions);
			$branchHanldes = $this->getRequest()->getParam("handles"); //implode(";", $this->getRequest()->getParam("handles"));

			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"statistics.csv\"");

			$prov = array();
			$i = 0;
			foreach ( $provisions as $provision ) {
				if (in_array($provision['branchHandle'], $branchHanldes)) {
					$prov[$i] = $provision;
					$prov[$i]['branchHandle'] = It6_Models_Branch::formatHandleForExport($prov[$i]['branchHandle']);
					$i++;
				}
			}

			echo It6_Models_ExportHelper::assocArrayToCsv($prov);
		}

		if ( isset($_REQUEST['export'])	&& It6_Date::checkDateFormat( $inData["filter"]["fromDate"] ) && It6_Date::checkDateFormat( $inData["filter"]["toDate"] ) ) {
		 	$filterData = array(
		 			"branchHandle" 	=> implode(";", $this->getRequest()->getParam("handles")),
		 			"fromDate"		=> date("Y-m-d", strtotime($inData["filter"]["fromDate"])),
		 			"toDate"		=> date("Y-m-d", strtotime($inData["filter"]["toDate"])),
		 		);
			Models_Calculation::generateBranchCalculationPdf($filterData, $items);
		 }
		 elseif (isset($_REQUEST["exportKb"]) && It6_Date::checkDateFormat( $inData["filter"]["fromDate"] ) && It6_Date::checkDateFormat( $inData["filter"]["toDate"] ) ) {
		 	$filename = 'branch_' . str_replace (".", "-", $inData["filter"]["fromDate"]) . '_' .  str_replace (".", "-", $inData["filter"]["toDate"]) . '.ikm';
		 	$filterData = array(
		 			"branchHandle" 	=> implode(";", $this->getRequest()->getParam("handles")),
		 			"fromDate"		=> date("Y-m-d", strtotime($inData["filter"]["fromDate"])),
		 			"toDate"		=> date("Y-m-d", strtotime($inData["filter"]["toDate"])),
		 		);
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"$filename\"");

		 	echo Models_Calculation::generateBranchKbBankSequence($filterData, $items);
		}

		$this->view->filter = $filter->getLayout(null, $this->filterData);
		$this->view->tHead = $table->getTheadLayout(static::CALCULATION_THEAD_LAYOUT, $this->orderData);
		$this->view->tBody = $table->getTbodyLayout(static::CALCULATION_TBODY_LAYOUT, $items);
	}

	public function branchInternetAction() {
		$inData = $this->getRequest()->getParams();
		$extensions = array();

		//prepare wsForm data from $_GET
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		$accounts = static::$ACCOUNTS;
		
		$ws = Zend_Registry::get('ws');
		 
		// Use this in your model, view and controller files		
		$contract_templates = It6_ArrayWrapper::toNativeArray($ws->ext($extensions)->ContractTemplate->getAll());	
		$contract_templates = Models_Utils::getCollection($contract_templates, 'templateId','name',array('' => '--Select--'));
		
		$stems = It6_ArrayWrapper::toNativeArray($ws->ext($extensions)->Stem->getAll());
		$stems = Models_Utils::getCollection($stems, 'stemId','name',array('' => '--Select--'));

		$filter = new It6_WsForm_Filter(array(
			array('From', 'fromDate', 'date', array(array('dateFrom'),'>=','?'), 'It6_Validate_Date',null,'start'),
			array('To', 'toDate', 'date', array(array('dateTo','<=','?')), 'It6_Validate_Date',null,'end'),
			array('Branch handle', 'id', 'text', array(array('handle', '=', '?')), null, null, null),
			array('Contract Type', 'templateId', 'select', array(array('templateId',  null)), 'Zend_Validate_Int', NULL, $contract_templates),
			array('Stem', 'stemId', 'select', array(array('stem_id',  null)), 'Zend_Validate_Int', NULL, $stems),
			array('Active contract', 'active', 'select', array(array('active', null)), 'Zend_Validate_Digit', NULL, array('' => '--Select--', 'y' => 'Yes', 'n' => 'No')),
			array('Calculation', 'calculation_net', 'select', array(array('calculation_net', null)), 'Zend_Validate_Digit', NULL, array('' => '--Select--', 'y' => 'Yes', 'n' => 'No')),
			array('Branch active', 'is_active', 'select', array(array('is_active', null)), 'Zend_Validate_Digit', NULL, array('' => '--Select--', 'y' => 'Yes', 'n' => 'No')),
		));

		$filter->getExtension($this->filterData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array('Branch handle','branchHandle'),
			array('Branch name','branchName'),
			array('Stem', 'stem'),
			array('Branch is active', 'branchIsActive'),
			array('Branch active contract', 'branchActive'),
			array('Contract type', 'contracttype'),
			array('Contracts', 'contracts'),
			array('Valid from', 'branchValidFrom'),
			array('Valid to', 'branchValidTo'),			
			array('Internet balance in','internetBalanceIn'),
			array('Internet balance out','internetBalanceOut'),
			array('Internet balance','internetBalance'),
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);
		

		if ( !empty($_REQUEST['form-submited'])  && It6_Date::checkDateFormat( $inData["filter"]["fromDate"] ) && It6_Date::checkDateFormat( $inData["filter"]["toDate"] ) ) {
			$this->view->provisions = $items = It6_ArrayWrapper::toNativeArray($ws->ext($extensions)->Branch->getProvisions());
		}
		else {
			$this->view->provisions = $items = array();
			$this->view->notFilter = true;
		}

		if ( isset($_REQUEST['exportCSV']) ) {
			$provisions = It6_ArrayWrapper::toNativeArray($this->view->provisions);
			$branchHanldes = $this->getRequest()->getParam("handles");

			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"statistics.csv\"");

			$prov = array();
			$i = 0;
			foreach ( $provisions as $provision ) {
				if (in_array($provision['branchHandle'], $branchHanldes)) {
					$prov[$i] = $provision;
					$prov[$i]['branchHandle'] = It6_Models_Branch::formatHandleForExport($prov[$i]['branchHandle']);
					$i++;
				}
			}

			echo It6_Models_ExportHelper::assocArrayToCsv($prov);
		}

		if ( isset($_REQUEST['export']) && It6_Date::checkDateFormat( $inData["filter"]["fromDate"] ) && It6_Date::checkDateFormat( $inData["filter"]["toDate"] ) ) {
		 	$filterData = array(
		 			"branchHandle" 	=> implode(";", $this->getRequest()->getParam("handles")),
		 			"fromDate"		=> date("Y-m-d", strtotime($inData["filter"]["fromDate"])),
		 			"toDate"		=> date("Y-m-d", strtotime($inData["filter"]["toDate"])),
		 		);
			Models_Calculation::generateInternetCalculationPdf($filterData, $items);
		 }
		 elseif (isset($_REQUEST["exportKb"]) && It6_Date::checkDateFormat( $inData["filter"]["fromDate"] ) && It6_Date::checkDateFormat( $inData["filter"]["toDate"] ) ) {
		 	$filename = 'internet_' . str_replace (".", "-", $inData["filter"]["fromDate"]) . '_' .  str_replace (".", "-", $inData["filter"]["toDate"]) . '.ikm';
		 	$filterData = array(
		 			"branchHandle" 	=> implode(";", $this->getRequest()->getParam("handles")),
		 			"fromDate"		=> date("Y-m-d", strtotime($inData["filter"]["fromDate"])),
		 			"toDate"		=> date("Y-m-d", strtotime($inData["filter"]["toDate"])),
		 		);
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"$filename\"");

		 	echo Models_Calculation::generateInternetKbBankSequence($filterData, $items);
		}

		/*
		if ( isset($_REQUEST['export']) ) {
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=\"statistics.csv\"");

			$provisions = It6_ArrayWrapper::toNativeArray($this->view->provisions);
			
			for ( $i = 0; $i < count($provisions); ++$i ) {
				$provisions[$i]['branchHandle'] = It6_Models_Branch::formatHandleForExport($provisions[$i]['branchHandle']);
			}

			echo It6_Models_ExportHelper::assocArrayToCsv($provisions);
		}*/
			
		$this->view->filter = $filter->getLayout(null, $this->filterData);
		$this->view->tHead = $table->getTheadLayout(static::CALCULATION_THEAD_LAYOUT_BRANCH_INTERNET, $this->orderData);
		$this->view->tBody = $table->getTbodyLayout(static::CALCULATION_TBODY_LAYOUT_BRANCH_INTERNET, $items);
	}
}