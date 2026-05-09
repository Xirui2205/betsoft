<?php
class ListingsController extends It6_Controller_Abstract {

const DAY_BOOK_SECTION_ID = 212;
const DAY_BOOK_SECTION_CSV_ID = 222;
const BALANCING_SECTION_ID = 272;
const BRANCH_PROVISIONS_SECTION_ID = 213;
const CASH_OVERVIEW_SECTION_ID = 296;

const DAYBOOK_TBODY_LAYOUT = 'daybook-tbody';
const PROVISIONS_TBODY_LAYOUT = 'provisions-tbody';
const CASH_OVERVIEW_TBODY_LAYOUT = 'cash-overview-tbody';
const CASH_OVERVIEW_TBODY_LAYOUT_SUM = 'cash-overview-tbody-sum';
const CASH_OVERVIEW_THEAD_LAYOUT_SUM = 'cash-overview-thead-sum';

const DEFAULT_MODE = 1;
const CALLCENTRUM_MODE = 2;

public static $ACCOUNTS = array ('0' => '--Select--', "211" => "211","221" => "221","324" => "324","378" => "378", "379" => "379","548" => "548","602" => "602");
public static $SUBACCOUNTS = array ('0' => '--Select--','999' => '999','998' => '998','997' => '997');

private $filterData		= array();
private $paginatorData	= array('recsPerPage' => 30);
private $orderData		= array();

private $mode = self::DEFAULT_MODE;

public function init() {
	parent::init();

	if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_CALLCENTRUM)){
		$this->mode = self::CALLCENTRUM_MODE;
		$this->view->showExport = false;
	} else {
		$this->view->showExport = true;
	}
}

public function dayBookAccountingAction() {
	if (!empty($_POST['export']))
		$this->_forward('day-book-accounting-csv');
	else
		$this->dayBookAccountingCommon(self::DAY_BOOK_SECTION_ID, true);
}

public function dayBookAccountingCsvAction() {
	$values = $this->dayBookAccountingCommon(self::DAY_BOOK_SECTION_CSV_ID, false, true);

	/* $csvFileName = array();
	if ( !empty($values['filter']['accounts']) )
		$csvFileName[] = implode($values['accounts'],'-');

	$csvFileName[] = strtr($values['filter']['fromDate'],':./ ','----');
	$csvFileName[] .= strtr($values['filter']['toDate'],':./ ','----');

	$csvFileName = implode($csvFileName,'_');

	$this->_helper->layout->disableLayout();
	$this->_helper->viewRenderer->setNoRender();

	header('Content-type: text/plain');
	header("Content-Disposition: attachment; filename=\"$csvFileName.csv\"");

	echo Models_DaybookExport::export($this->view->dayBook); */
}

protected function dayBookAccountingCommon($section, $pagination = true, $asCsv = false) {
	$ws = Zend_Registry::get('ws');
	
	$inData			= $this->getRequest()->getParams();
	$extensions		= array();

	//prepare wsForm data from $_GET
	if(!empty($inData['filter']))
		$this->filterData = $inData['filter'];
	if(!empty($inData['paginator']))
		$this->paginatorData = $inData['paginator'];
	if(!empty($inData['order']))
		$this->orderData = array_keys($inData['order']);

	
	
	$accounts = static::$ACCOUNTS;
	
	$subaccounts = static::$SUBACCOUNTS;
	
	$descs = $ws->TransactionType->getAllOrder(array('name'));
	$descs = It6_ArrayWrapper::toAssocArray($descs,'name','%name%',array(0 => '--Select--'));
	$filter = new It6_WsForm_Filter(array(
		array('Id', 'id', 'text', array(array('id', '=', '?')), null, null, null),
		array('Ticket handle', 'ticketHandle', 'text', array(array('ticketHandle', '=', '?')),  'Zend_Validate_Int', null, null),
		array('Accounts', 'account', 'select', array(array('account', '=', '?')), null, null, $accounts),
		array('Subaccounts', 'subaccount', 'select', array(array('subaccount', '=', '?')), null, null, $subaccounts),
		array('Description', 'description', 'select', array(array('description', '=', '?')), null, null, $descs),
		array('Variable symbol', 'varSymbol', 'text', array(array('varSymbol', '=', '?')),  'Zend_Validate_Int', null, null),
		array('Branch handle', 'branchHandle', 'text', array(array('branchHandle', '=', '?')),  'Zend_Validate_Int', null, null),
		array('user_handle', 'userHandle', 'text', array(array('userHandle', '=', '?')), new It6_Validate_NineDigitHandle(), null, null),
		array('Ticket id', 'ticketId', 'text', array(array('ticketId', '=', '?')),  'Zend_Validate_Int', null, null),
		array('Ticket handle', 'ticketHandle', 'text', array(array('ticketHandle', '=', '?')),  'Zend_Validate_Int', null, null),
		array('From', 'fromDate', 'date', array(array('date','>=','?')), 'It6_Validate_Date',null,'start'),
		array('To', 'toDate', 'date', array(array('date','<=','?')), 'It6_Validate_Date',null,'end'),
		array('Minimal amount', 'minAmount', 'text', array(array(array('ABS(?)' => 'amount'), '>=', '?')), null, null, null),
		array('Maximal amount', 'maxAmount', 'text', array(array(array('ABS(?)' => 'amount'), '<=', '?')), null, null, null),
		array('Amount', 'amount', 'text', array(array(array('ABS(?)' => 'amount'), '=', '?')), null, null, null),
		
	));
	$filter->getExtension($this->filterData, $extensions);

	$filter->getFilterForm()->getElement('submit')
		->setAttrib('onClick',"this.form.action='?section=212'; return true;");
	
	if ( $pagination && !empty($inData['filter']) ) {
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);
	}


	//create table
	$table = new It6_WsForm_Table(array(
		array('id','id'),
		array('date','dateChange'),
		array('Date change','dateChange'),
		array('Order','order'),
		array('Account','account'),
		array('Amount','amount'),
		array('Description','description'),
		array('Variable symbol','ticketId'),
		array('Host id','hostId'),
		array('user_handle','userHandle'),
		array('Ticket id','ticketId'),
		array('Ticket handle','ticketHandle'),
		array('Currency code','currencyCode'),
		array('Branch handle', 'branchHandle'),
		array('Branch name', 'branchName'),
	));
	$table->getColumnsExtension($extensions);
	$table->getOrderExtension($this->orderData, $extensions);

	if ($asCsv) {
		$csvFileName = 'denik-' . strftime('%Y-%m-%d-%H-%M-%S');
		$filtered = false;
		if (!empty($inData['filter'])) {
			foreach ($inData['filter'] as $key => $value) {
				if (!empty($value)) {
					$filtered = true;
					break;
				}
			}
		}
		if($filtered) {
			$csvFileName .= '-vyber';
			if ( !empty($inData['filter']['account']) )
				$csvFileName .= '-ucet-' . intval($inData['filter']['account']);
			if ( !empty($inData['filter']['fromDate']) )
				$csvFileName .= '-od-' . strtr($inData['filter']['fromDate'], ':./ ', '----');
			if ( !empty($inData['filter']['toDate']) )
				$csvFileName .= '-do-' . strtr($inData['filter']['toDate'], ':./ ', '----');
			$csvFileName .= '.csv';
		}
		else
			$csvFileName .= '-vse.csv';
		// not possible in true remote call, it would have to be proxied
		$ws->ext($extensions)->Transaction->exportDayBook($csvFileName);
		exit();
	}
	else {
		if ( !empty($_POST['sum']) ) {
			$ret = $ws->ext($extensions)->Transaction->getDayBook(true);
			$this->view->dayBook = $ret[0];
			$this->view->sum = $ret[1];
			$items = $ret[0];
		}
		else {
			if ( !empty($inData['filter']) ) {
				$this->view->dayBook = $items = $ws->ext($extensions)->Transaction->getDayBook(false);
			}
			else {
				$this->view->dayBook = $items = array();
				$this->view->notFilter = true;
			}
			
		}
	}
	$items = It6_ArrayWrapper::toNativeArray($items);

	$this->view->filter = $filter->getLayout(null, $this->filterData);
	if ( empty($_POST['sum']) && $pagination && !empty($inData['filter']) )
		$this->view->paginator = $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
	$this->view->tHead = $table->getTheadLayout(null,$this->orderData);
	$this->view->tBody = $table->getTbodyLayout(static::DAYBOOK_TBODY_LAYOUT, $items);

	return $_POST;

}

public function branchProvisionsAccountingAction() {

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
		array(i18n::tr('branch_handle'), 'id', 'text', array(array('handle', '=', '?')), null, null, null),
		array('Contract Type', 'templateId', 'select', array(array('templateId', null)), 'Zend_Validate_Int', NULL, $contract_templates),
		//array('valid from from', 'validFromFromDate', 'date', array(array('branchValidFromFrom'),null,'?'), 'It6_Validate_Date',null,'start'),
		//array('Valid from to', 'validFromtoDate', 'date', array(array('branchValidFromTo',null,'?')), 'It6_Validate_Date',null,'end'),
		//array('Valid to from', 'validToFromDate', 'date', array(array('branchValidToFrom'),null,'?'), 'It6_Validate_Date',null,'start'),
		//array('Valid to to', 'validToToDate', 'date', array(array('branchValidToTo', null,'?')), 'It6_Validate_Date',null,'end'),
		array('Stem', 'stemId', 'select', array(array('stem_id', null)), 'Zend_Validate_Int', NULL, $stems),
		array('Branch active', 'is_active', 'select', array(array('is_active', null)), 'Zend_Validate_Digit', NULL, array('' => '--Select--', 'y' => 'Yes', 'n' => 'No')),
		//array(i18n::tr('branch_handle'), 'branchHandle', array('text' => function($data){return explode(";", $data);}), array(array('handle', 'IN (?)'))),
	));
	$filter->getExtension($this->filterData, $extensions);

	$logger = new Zend_Log(new Zend_Log_Writer_Firebug());
	$logger->info($this->filterData);

	//create table
	$table = new It6_WsForm_Table(array(
		array('Branch handle','branchHandle'),
		array('Branch name','branchName'),
		array('Stem', 'stem'),
		array('Branch active', 'branchActive'),
		array('Contract type', 'contracttype'),
		array('Contracts', 'contracts'),
		array('Valid from', 'branchValidFrom'),
		array('Valid to', 'branchValidTo'),
		array('Branch in','branchIn'),
		array('Branch out','branchOut'),
		array('Branch mp','branchMp'),
		array('Branch in-out+mp','branchInMinusOut'),
		array('Branch payout out','branchPayoutOut'),
		array('Internet balance in','internetBalanceIn'),
		array('Internet balance out','internetBalanceOut'),
		array('Internet balance','internetBalance'),
		/* array('Branch profit','branchProfit'),
		array('Brnanch profit no mp','branchProfitNoMp'), */
	));
	$table->getColumnsExtension($extensions);
	$table->getOrderExtension($this->orderData, $extensions);

	/* $this->view->form = new Models_Form_BranchProvisionsFilter(static::BRANCH_PROVISIONS_SECTION_ID);

	if ( !$this->view->form->isValid($this->getRequest()->getParams()) ) {
		//TODO add validation message
	}

	$values = $this->view->form->getValues();
	$this->view->form->populate($values);

	$this->view->provisions = $ws->Branch->getProvisions($values['fromDate'] + " 00:00:00", $values['toDate'] + " 23:59:59" ); */

	if ( !empty($_REQUEST['form-submited']) ) {
		$this->view->provisions = $items = $ws->ext($extensions)->Branch->getProvisions();
	} else {
		$this->view->provisions = $items = array();
		$this->view->notFilter = true;
	}

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
	}

	$this->view->filter = $filter->getLayout(null, $this->filterData);
	$this->view->tHead = $table->getTheadLayout(null,$this->orderData);
	$this->view->tBody = $table->getTbodyLayout(static::PROVISIONS_TBODY_LAYOUT, $items);
}

public function balancingAction($noHost = false) {

	$ws = Zend_Registry::get('ws');
	$this->view->form = new Models_Form_BalancingFilter(static::BALANCING_SECTION_ID, $noHost);

	if ( !$this->view->form->isValid($this->getRequest()->getParams()) ) {
		//TODO add validation message
	}

	$values = $this->view->form->getValues();
	$this->view->form->populate($values);
	if (isset($values['distinguishBranchesAndInternet'])) {
		$this->view->distinguishBranchesAndInternet = $values['distinguishBranchesAndInternet'];
	}

	if ( $noHost || !empty($values['hosts']) || !empty($values['branchId']) || !empty($values['branchHandle'])) {
		if ( !empty($_REQUEST['filter']) ) {
			$this->view->balancing = $ws->Host->getBalancing(
				$noHost ? null : $values['hosts'], $values['dateStart'], $values['dateTo'], $values['branchId'], $values["branchHandle"]
			);
			if (isset($values['distinguishBranchesAndInternet']) && $values['distinguishBranchesAndInternet'] == 1) {
				$this->view->netBalancing = $ws->Host->getBalancing(
					It6_Models_Host::ID_INTERNET, $values['dateStart'], $values['dateTo'], $values['branchId'], $values["branchHandle"]
				);
			}

			// livka
			$this->view->liveBalancing = $ws->Host->getLiveBalancing(null, $values['dateStart'], $values['dateTo']);

			// sum
			$this->view->ticketCountTotal = $this->view->balancing->ticketCount + $this->view->liveBalancing->count;
			$this->view->ticketAmountTotal = $this->view->balancing->ticketAmount + $this->view->liveBalancing->amount;
			$this->view->stornoTicketCountTotal = $this->view->balancing->stornoTicketCount + $this->view->liveBalancing->countCancel;
			$this->view->stornoTicketAmountTotal = $this->view->balancing->stornoTicketAmount + $this->view->liveBalancing->amountCancel;
			$this->view->notStornoTicketCountTotal = $this->view->ticketCountTotal - $this->view->stornoTicketCountTotal;
			$this->view->notStornoTicketAmountTotal = $this->view->ticketAmountTotal - $this->view->stornoTicketAmountTotal;
			$this->view->collectTicketCountTotal = $this->view->balancing->collectTicketCountReal + $this->view->liveBalancing->countPaid;
			$this->view->collectTicketAmountTotal = $this->view->balancing->collectTicketAmountReal + $this->view->liveBalancing->amountPaid;
			$this->view->winRatioCashflowTotal = $this->countWinRatioCashflowTotal($this->view->collectTicketAmountTotal, $this->view->ticketAmountTotal);
			$this->view->activeUsersTotal = $this->view->balancing->activeUsers + $this->view->liveBalancing->activeUsers;

		} else {
			$this->view->balancing = array();
			$this->view->notFilter = true;
		}

		if ( !$noHost ) $this->view->inOut = $ws->Host->getInOut($values['hosts'], $values['branchId'], $values['branchHandle']);

		if (!empty($values['branchId']) || !empty($values['branchHandle'])) {
			$name = $ws->Branch->getNameByIdOrHandle($values['branchId'], $values['branchHandle']);
			if (!empty($name)) $this->view->feedbackMsg = UiUtil::printMessages( $name->name );
			else $this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr( 'branch_not_exist' )));
		}
	}
}

private function countWinRatioCashflowTotal($collectTicketAmountTotal, $ticketAmountTotal) {
    if ( $ticketAmountTotal == 0 ) {
        return 0;
    } else {
        return round((($ticketAmountTotal - $collectTicketAmountTotal) / $ticketAmountTotal * 100), 2);
    }
}

public function globalBalancingAction() {
	static::balancingAction(true);
}

public function cashOverviewAction() {
	$ws = Zend_Registry::get('ws');

	$inData = $this->getRequest()->getParams();
	$extensions = array();

	$this->filterData['branchIsActive'] = 1;
	//prepare wsForm data from $_GET
	if(!empty($inData['filter']))
		$this->filterData = $inData['filter'];
	if(!empty($inData['paginator']))
		$this->paginatorData = $inData['paginator'];
	if(!empty($inData['order']))
		$this->orderData = array_keys($inData['order']);
	
	$filter = new It6_WsForm_Filter(array(
		array('Branch handle', 'branchHandle', 'text', array(array('branchHandle', '=', '?')), null, null, null),
		array(i18n::tr('isActive'), 'branchIsActive', 'checkbox', array(array('branchIsActive', '=')))
	));
	$filter->getExtension($this->filterData, $extensions);

	$filter->getFilterForm()->getElement('submit')
		->setAttrib('onClick',"this.form.action='?section=".self::CASH_OVERVIEW_SECTION_ID."; return true;");

	// create pagination
	// $paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
	// $paginator->getExtension($this->paginatorData, $extensions);

	//create table
	$tableSum = new It6_WsForm_Table(array(
		array('Branch handle','branchHandle'),
		array('Branch name','branchName'),
		array('Host name','hostName'),
		array('Cash Sum','balanceSum'),
		array('On the way deposit Sum','onTheWayDeposit'),
		array('On the way withdraw Sum','onTheWayWithdraw'),
		array('Win tickets cash Sum','winTicketsCash'),
		array('User withdraws cash Sum','userWithdrawsCash'),
		array('Total collect Sum','totalCollect'),
		array('Recommended withdraw Sum','recommendedWithdraw'),
		array('Recommended deposit Sum','recommendedDeposit'),
	));
	$tableSum->getColumnsExtension($extensions);

	//create table
	$table = new It6_WsForm_Table(array(
		array('Branch handle','branchHandle'),
		array('Branch name','branchName'),
		array('Host name','hostName'),
		array('Cash','balance'),
		array('On the way deposit','onTheWayDeposit'),
		array('On the way withdraw','onTheWayWithdraw'),
		array('Win tickets cash','winTicketsCash'),
		array('User withdraws cash','userWithdrawsCash'),
		array('Total collect','totalCollect'),
		array('Recommended withdraw','recommendedWithdraw'),
		array('Recommended deposit','recommendedDeposit'),
	));
	$table->getColumnsExtension($extensions);
	$table->getOrderExtension($this->orderData, $extensions);

	$this->view->hosts = $items = $ws->ext($extensions)->Host->getAllInOutsAll();

	$arraySum = $ws->ext($extensions)->Host->getAllSums();

	if ( isset($_REQUEST['export']) && $this->mode != self::CALLCENTRUM_MODE) {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		header('Content-type: text/plain');
		header("Content-Disposition: attachment; filename=\"statistics.csv\"");

		$hosts = It6_ArrayWrapper::toNativeArray($this->view->hosts);

		$tableDef = array(
			'branchHandle',
			'branchName',
			'hostName',
			'balance',
			'onTheWayDeposit',
			'onTheWayWithdraw',
			'winTicketsCash',
			'userWithdrawsCash',
			'totalCollect',
			'recommendedWithdraw',
			'recommendedDeposit',
		);

		for ( $i = 0; $i < count($hosts); ++$i ) {
			$hosts[$i]['branchHandle'] = It6_Models_Branch::formatHandleForExport($hosts[$i]['branchHandle']);
			foreach ($hosts[$i] as $k => $v) {
				if ( !in_array($k,$tableDef) ) {
					unset($hosts[$i][$k]);
				}
			}
		}
		echo It6_Models_ExportHelper::assocArrayToCsv($hosts);
	}

	$items = It6_ArrayWrapper::toNativeArray($items);
	$arraySum = It6_ArrayWrapper::toNativeArray($arraySum);

	$this->view->filter = $filter->getLayout(null, $this->filterData);
	//$this->view->paginator = $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
	$this->view->tHead = $table->getTheadLayout(null, $this->orderData);
	$this->view->tBody = $table->getTbodyLayout(static::CASH_OVERVIEW_TBODY_LAYOUT, $items);
	$this->view->tHeadSum = $tableSum->getTheadLayout(static::CASH_OVERVIEW_THEAD_LAYOUT_SUM, null);
	$this->view->tBodySum = $tableSum->getTbodyLayout(static::CASH_OVERVIEW_TBODY_LAYOUT_SUM, $arraySum);
}
}