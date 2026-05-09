<?php

class FinanceController extends It6_Controller_Abstract {

const TRANSACTIONS_ID = 224;
const POINT_TRANSACTIONS_ID = 271;
const CONFIRMATIONS_ID = 225;
const PREDEPOSITS_ID = 253;
const MANUAL_DEPOSIT_ID = 226;
const MANUAL_WITHDRAW_ID = 244;
const MANUAL_BALANCE_ID = 245;
const MANUAL_POINTS_ID = 246;
const BRANCH_FUNDING_ID = 252;
const CONFIRMATIONS_EXPORT_ID = 279;

const CONFIRM_TRANSACTION_ID = "confirmTransactionId";
const CANCEL_TRANSACTION_ID = "cancelTransactionId";

const CONFIRM_SELECTED = "confirm_selected";
const CANCEL_SELECTED = "cancel_selected";
const SELECTED = "select";

const MAKE_DEPOSIT_TRANSACTION_ID = "depositTransactionId";
const DEPOSIT_SELECTED = "deposit_selected";

const FUND_HOST_ID = "fundHostId";
const FUND_ALL = "fund_all";
const FUND_SELECTED = "fund_selected";
const CONFIRM_FUNDS = "confirm_funds";
const CANCEL_FUNDS = "cancel_funds";

const PARAMETER_BRANCH_FUND_RESERVE = "branch.fundReserve";
const PARAMETER_BRANCH_MINIMAL_DEPOSIT = "branch.minimalDeposit";
const PARAMETER_BRANCH_MINIMAL_WITHDRAW = "branch.minimalWithdraw";

const DEPOSIT_TRANSACTION_NAME = 'branch.deposit';
const WITHDRAW_TRANSACTION_NAME = 'branch.withdraw';


private static $TRANSACTION_STATUSES = array (
	Webservice_Transaction::STATUS_OK => 'ok',
	Webservice_Transaction::STATUS_PENDING => 'pending',
	Webservice_Transaction::STATUS_CANCELED => 'canceled',
	Webservice_Transaction::STATUS_PRE_DEPOSIT => 'pre-deposit',
	Webservice_Transaction::STATUS_NO_DEPOSIT => 'no-deposit',
);


private $filterData		= array();
private $paginatorData	= array('recsPerPage' => 30);
private $orderData		= array('transactionId ASC');


var $orderColumns = array(
	'time' => array('order'=>'asc', 'active' => true),
	'status' => array( 'status'=>'asc', 'active' => false),
	'userNick' => array( 'userNick'=>'asc', 'active' => false),
	'typeName' => array( 'typeName'=>'asc', 'active' => false),
);



	public function init() {
		parent::init();
		$this->jsIncludes->commonAjax = true;

		//ordering columns
		$this->col = $this->getRequest()->getPost('column');
		$ord = $this->getRequest()->getPost('order');
		if ( $this->getRequest()->getPost('ajax') ) {
			$this->_helper->layout->setLayout('empty');
		}
		if ( !empty($col) ) {
			$order = $col.' '.$ord;
			$this->order = array($order);
		}
		else
			$this->order = array();

		$this->view->order = $this->order;
		$this->orderColumns[$this->col]['order'] = $ord;
	}


public function transactionsAction() {
	$this->view->orderButton = Models_Utils::getOrderButtons($this->orderColumns, self::TRANSACTIONS_ID, $this->col);

	$ws = Zend_Registry::get('ws');
	$db = Zend_Registry::get('db');
	$inData = $this->getRequest()->getPost();
	if (empty($inData))
		$inData = $this->getRequest()->getQuery();
	$export = (!empty($inData['export']));

	// TODO: use ACL
	//$acl = Zend_Registry::get('acl');
	//if ($acl->isResourceAllowed()) {
	if (!empty($_FILES['importBankKb']) && is_uploaded_file($_FILES['importBankKb']['tmp_name'])) {
		$log = array();
		$import = new It6_Bank_Batch_Import_Kb_Best();
		$import->importFile($_FILES['importBankKb']['tmp_name'], $db, $log, strftime('%Y-%m-%d-%H-%M-%S-') . $_FILES['importBankKb']['name']);
		$this->view->importLog = $log;
	}
	//}

	//prepare wsForm data from $_POST
	if (!empty($inData['filter'])) {
		$this->filterData = $inData['filter'];
	}

	// vyhra tiketu vybrana
	if (isset($inData['filter']['branchTicketCollect'])) {
		if ($inData['filter']['branchTicketCollect'][0] == 1) {
			unset ($this->filterData['branchTicketCollect']);
        }
		if ($inData['filter']['branchTicketCollect'][0] == 2) {
		    $this->filterData['typeId'][] = 17;
		    $this->filterData['status'][] = 'ok';
		}
		if ($inData['filter']['branchTicketCollect'][0] == 3) {
		    $this->filterData['typeId'][] = 17;
		}
    }
    
    //vyhra tiketu propadla
	if (isset($inData['filter']['otherTicketForfeit'])) {
		if ($inData['filter']['otherTicketForfeit'][0] == 1) {
			unset ($this->filterData['otherTicketForfeit']);
        }
		if ($inData['filter']['otherTicketForfeit'][0] == 2) {
		    $this->filterData['typeId'][] = 28;
		    $this->filterData['status'][] = 'ok';
		}
		if ($inData['filter']['otherTicketForfeit'][0] == 3) {
		    $this->filterData['typeId'][] = 28;
		}
    }

	if(!empty($inData['paginator']))
		$this->paginatorData = $inData['paginator'];
	if(!empty($inData['order']))
		$this->orderData = array_keys($inData['order']);

	//create filter
	$transactionTypes = Models_Utils::getSelectOptions($ws->TransactionType->getAll(), 'transactionTypeId', 'name');

	asort($transactionTypes);

    $question	= array(1 => 'nevybrano', 2 => 'ano', 3 => 'ne');
	$filter = new It6_WsForm_Filter(array(
		array('transaction_id', 'transactionId', 'text', array(array('transactionId', '=')), 'Zend_Validate_Int'),
		array('transaction_handle', 'transactionHandle', 'text', array(array('handle', '=')), 'Zend_Validate_Int'),
		array('user_id', 'userId', 'text', array(array('userId', '=')), 'Zend_Validate_Int'),
		array('user_handle', 'userHandle', 'text', array(array('userHandle', '=')), 'It6_Validate_NineDigitHandle'),
		array('User Nick', 'userNick', 'text', array(array('userNick', '='))),
		array('branch_handle', 'branchHandle', 'text', array(array('branchHandle', '='))),
		array('branch_id', 'branchId', 'text', array(array('branchId', '=')), 'Zend_Validate_Int'),
		array('host_id', 'hostId', 'text', array(array('hostId', '=')), 'Zend_Validate_Int'),
		array('Ticket Id', 'ticketId', 'text', array(array('ticketId', '=')), 'Zend_Validate_Int'),
		array('Ticket Handle', 'ticketHandle', 'text', array(array('ticketHandle', '=')), 'It6_Validate_NineDigitHandle'),
		array('Type', 'typeId', 'multiselect', array(array('typeId', '=')), null, null, $transactionTypes, array('typeSubFilter', 'OR')),
		array('Status', 'status', 'multiselect', array(array('status', '=')), null, null, self::$TRANSACTION_STATUSES, array('statusSubFilter', 'OR')),
		array('Date From', 'fromDate', 'date', array(array('time','>=', '?')), 'It6_Validate_Date',null,'start'),
		array('Date To', 'toDate', 'date', array(array('time','<=','?')), 'It6_Validate_Date',null,'end'),
		array('okTime From', 'fromokTIme', 'date', array(array('okTime','>=', '?')), 'It6_Validate_Date',null,'start'),
		array('okTime To', 'tookTIme', 'date', array(array('okTime','<=','?')), 'It6_Validate_Date',null,'end'),
		array('cancelTime From', 'fromcancelTime', 'date', array(array('cancelTime','>=', '?')), 'It6_Validate_Date',null,'start'),
		array('cancelTime To', 'tocancelTime', 'date', array(array('cancelTime','<=','?')), 'It6_Validate_Date',null,'end'),
		array('depositTime From', 'fromdepositTime', 'date', array(array('depositTime','>=', '?')), 'It6_Validate_Date',null,'start'),
		array('depositTime To', 'todepositTime', 'date', array(array('depositTime','<=','?')), 'It6_Validate_Date',null,'end'),
		array('Value', 'value', 'text', array(array('value', '='))),
		array('Abs value', 'absValue', 'text', array(array(array('ABS(?)' => 'value'), '='))),
	));

	$filter->getExtension($this->filterData, $extensions);

	//create pagination
	if (!$export) {
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);
	}

	//create table
	$columns = array(
		array('Id', 'transactionId'),
		array('Handle', 'handle'),
		array('Time', 'time'),
		array('OK Time', 'okTime'),
		array('Type Id', 'typeId'),
		array('Type Name', 'typeName'),
		array('Status', 'status'),
		array('Value', 'value'),
		array('Currency', 'currencyCode'),
		array('user_id', 'userId'),
		array('user_handle', 'userHandle'),
		array('User Nick', 'userNick'),
		array('Ticket Id', 'ticketId'),
		array('ticket_handle', 'ticketHandle'),
		array('branch_handle', 'branchHandle'),
		array('branch_id', 'branchId'),
		array('host_id', 'hostId'),
		array('branch_name', 'branchName'),
		array('admin_create_id', 'createAdminId'),
		array('admin_create', 'createAdmin'),
		array('admin_confirm_id', 'confirmAdminId'),
		array('admin_confirm', 'confirmAdmin'),
		array('admin_deposit_id', 'depositAdminId'),
		array('admin_deposit', 'depositAdmin'),
		array('Notes', 'notes')
	);
	if ($export) {
		$columns = array_merge($columns, array(
			array('cancel_time', 'cancelTime'),
			array('deposit_time', 'depositTime'),
			array('canceled_transaction_id', 'canceledTransactionId'),
			array('balance', 'balance'),
			array('fee_transaction_id', 'feeTransactionId'),
			array('account_type', 'accountType'),
			array('from', 'from'),
			//array('from_sub', 'tt.from_sub'),
			array('to', 'to'),
			//array('to_sub', 'tt.to_sub'),
			array('export_date', 'exportDate'),
		));
	}
	$table = new It6_WsForm_Table($columns);
	$table->getColumnsExtension($extensions);
	$table->getOrderExtension($this->orderData, $extensions);

	//get transaction list by WS
	if ($export) {
		// this wouldn't be possible with real remote call
		$ws->ext($extensions)->Transaction->export(strftime('transakce-%Y-%m-%d-%H-%M-%S.csv'));
		exit;
	}
	else {
		if ( count($inData) > 1 ) {
			$transactions = $ws->ext($extensions)->Transaction->getAll();
		}
		else {
			$transactions = array();
			$this->view->notFilter = true;
		}
		
	}

	$transactions = It6_ArrayWrapper::toNativeArray($transactions);

	foreach ( $transactions as &$transaction ) {
		$transaction['time'] = It6_Date::fromDb($transaction['time']);
		$transaction['okTime'] = It6_Date::fromDb($transaction['okTime']);
		if (It6_Models_Admin::ID_INTERNET == $transaction['createAdminId'])
			$transaction['createAdmin'] = 'Internet';
		if (It6_Models_Admin::ID_INTERNET == $transaction['confirmAdminId'])
			$transaction['confirmAdmin'] = 'Internet';
		if (It6_Models_Admin::ID_INTERNET == $transaction['depositAdminId'])
			$transaction['depositAdmin'] = 'Internet';
		$transaction['typeName'] = ($transaction["typeName"] == Webservice_TransactionType::NAME_USER_BONUS_ENTRY && !empty($transaction["notes"])) ? $transaction["typeName"] . "<br />(" . $transaction["notes"] . ")" :  $transaction['typeName'];
	}

	$this->view->filter		= $filter->getLayout(null, $this->filterData);
	if (!empty($transactions)) {
		$this->view->paginator = $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
	}
	$this->view->tHead		= $table->getTheadLayout(null, $this->orderData);
	$this->view->tBody		= $table->getTbodyLayout(null, $transactions);

	$this->view->transactionsSectionId = self::TRANSACTIONS_ID;
}

public function pointTransactionsAction() {
	$ws = Zend_Registry::get('ws');

	$this->view->form = new Models_Form_PointTransactionsFilter(self::POINT_TRANSACTIONS_ID);

	if ( !$this->view->form->isValid($this->getRequest()->getParams()) ) {
		//TODO add validation message
	}

	$values = $this->view->form->getValues();
	$this->view->form->populate($values);

	$where = array();
	if ( !empty($values['userHandle']) )
		$where['userHandle = ?'] = $values['userHandle'];
	if ( !empty($values['typeId']) )
		$where['typeId = ?'] = $values['typeId'];
	if ( !empty($values['value']) )
		$where['ABS(value) > ?'] = $values['value'];
	if ( !empty($values['fromDate']) )
		$where['time >= ?'] = It6_Date::toDb($values['fromDate']);
	if ( !empty($values['toDate']) )
		$where['time <= ?'] = It6_Date::toDb($values['toDate']);

	if ( !empty($_REQUEST['filter']) ) {
		$this->view->transactions = $ws->PointsTransaction->getAllWhereOrder($where, $this->order);
	}
	else {
		$this->view->transactions = array();
		$this->view->notFilter = true;
	}
}

public function confirmationsAction() {
	$ws = Zend_Registry::get('ws');

	$confirmTransaction = $this->getRequest()->getParam(self::CONFIRM_TRANSACTION_ID);
	$cancelTransaction = $this->getRequest()->getParam(self::CANCEL_TRANSACTION_ID);
	$confirmSelected = $this->getRequest()->getParam(self::CONFIRM_SELECTED);
	$cancelSelected = $this->getRequest()->getParam(self::CANCEL_SELECTED);

	if ( !empty($confirmTransaction) ) {
		try {
			$confirmTransactionId = key($confirmTransaction);
			$transaction = $ws->Transaction->getById($confirmTransactionId);
			$ws->Transaction->confirm($confirmTransactionId);
			$this->view->result = UiUtil::printMessages(i18n::tr('Transaction confirmed.'));
			It6_Log::info(
				"Transaction '%transactionId%' confirmed.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'transactionId' => $confirmTransactionId,
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));
		} catch ( Exception $e ) {
			It6_Log::warn(
				"Transaction '%transactionId%' confirmation failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				$transaction);
			It6_Log::err($e);
			$this->view->result = UiUtil::printErrors(i18n::tr('Transaction confirm failed'));
		}

	}
	if ( !empty($cancelTransaction) ) {
		try {
			$cancelTransactionId = key($cancelTransaction);
			$transaction = $ws->Transaction->getById($cancelTransactionId);
			$ws->Transaction->cancel($cancelTransactionId);
			$this->view->result = UiUtil::printMessages(i18n::tr('Transaction cancelled.'));
			It6_Log::info(
				"Transaction '%transactionId%' canceled.",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'transactionId' => $cancelTransactionId,
						'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
						'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
						'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));
		} catch ( Exception $e ) {
			$this->view->result = UiUtil::printErrors(i18n::tr('Transaction cancelation failed'));
			It6_Log::warn(
				"Transaction '%transactionId%' cancelation failed.",
					It6_Log::TAG_ADMIN_OPERATION,
					$transaction);
			It6_Log::err($e);
		}

	}
	if ( !empty($confirmSelected) ) {
		$selected = $this->getRequest()->getParam(self::SELECTED);
		$this->view->result = '';
		$n = 0;
		foreach ( $selected as $item ) {
			try {
				$transaction = $ws->Transaction->getById($item);
				$ws->Transaction->confirm($item);
				++$n;
				It6_Log::info(
					"Transaction '%transactionId%' confirmed.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'transactionId' => $item,
							'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
							'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
							'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));
			} catch ( Exception $e ) {
				It6_Log::warn(
					"Transaction '%transactionId%' confirmation failed.",
						It6_Log::TAG_ADMIN_OPERATION,
						$transaction);
				It6_Log::err($e);
				$this->view->result .= UiUtil::printErrors(i18n::tr('Transaction confirm failed: ') . $item);
			}
		}

		if ( !empty($this->view->result) )
			$this->view->result .= UiUtil::printWarnings(i18n::tr('Some confirmations failed.'));

		$this->view->result .= UiUtil::printMessages(i18n::tr('Confirmed transactions: ') . $n);

	}
	else if ( !empty($cancelSelected) ) {
		$selected = $this->getRequest()->getParam(self::SELECTED);
		$this->view->result = '';
		$n = 0;
		foreach ( $selected as $item ) {
			try {
				$transaction = $ws->Transaction->getById($item);
				$ws->Transaction->cancel($item);
				++$n;
				It6_Log::info(
					"Transaction '%transactionId%' confirmation canceled.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'transactionId' => $item,
							'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
							'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
							'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));
			} catch ( Exception $e ) {
				It6_Log::warn(
					"Transaction '%transactionId%' confirmation cancelation failed.",
						It6_Log::TAG_ADMIN_OPERATION,
						$transaction);
				It6_Log::err($e);
				$this->view->result .= UiUtil::printErrors(i18n::tr('Transaction cancel failed: ') . $item);
			}
		}

		if ( !empty($this->view->result) )
			$this->view->result .= UiUtil::printWarnings(i18n::tr('Some cancelations failed.'));

		$this->view->result .= UiUtil::printMessages(i18n::tr('Canceled transactions: ') . $n);
	}

	$showTypeFilter = $this->getRequest()->getParam('showTypeFilter');
	$typeIds = $this->getRequest()->getParam('typeId');
	$this->view->form = new Models_Form_ConfirmationsFilter(self::CONFIRMATIONS_ID, $showTypeFilter, $typeIds);

	if ( !$this->view->form->isValid($this->getRequest()->getParams()) )
		$this->view->result = UiUtil::printErrors(i18n::tr('Validation error'));

	$values = $this->view->form->getValues();
	$this->view->form->populate($values);

	$where = array("status = 'pending'");
	if ( !empty($values['userHandle']) )
		$where['userHandle = ?'] = $values['userHandle'];
	if ( !empty($values['hostId']) )
		$where['hostId = ?'] = $values['hostId'];
	if ( !empty($values['typeId']) )
		$where['typeId IN (?)'] = $values['typeId'];
	if ( !empty($values['value']) )
		$where['ABS(value) = ?'] = $values['value'];
	if ( !empty($values['fromDate']) )
		$where['DATE(time) >= DATE(?)'] = It6_Date::toDbAsDate($values['fromDate']);
	if ( !empty($values['toDate']) )
		$where['DATE(time) <= DATE(?)'] = It6_Date::toDbAsDate($values['toDate']);

	if ( !empty($_REQUEST['export']) ) {
		$transactions = $ws->Transaction->getAllWhereOrder($where, $this->order);
		echo It6_Models_ExportHelper::assocArrayToCsv($transactions);
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		header('Content-type: text/plain');
		header("Content-Disposition: attachment; filename=\"confirmations.csv\"");
		return;
	}

	if(!empty($typeIds)) {
		$order = $this->getRequest()->getParam('order');
		if(!empty($order)) {
			$order = key($order);
			$order = array(str_replace('_', ' ', $order));
		}
		else
			$order = array();

		$this->view->transactions = $ws->Transaction->getAllWhereOrder($where, $order);
	}
	else
		$this->view->transactions = false;
}

public function manualDepositAction() {
	Models_Form_ManualDeposit::render($this->getRequest(), $this->view);
}

public function manualWithdrawAction() {
	Models_Form_ManualWithdraw::render($this->getRequest(), $this->view);
}

public function manualBalanceAction() {
	Models_Form_ManualBalance::render($this->getRequest(), $this->view);
}

public function manualPointsAction() {
	Models_Form_ManualPoints::render($this->getRequest(), $this->view);
}

public function voucherManualPointsAction() {
	Models_Form_ManualVoucherPoints::render($this->getRequest(), $this->view);
}

function cancelTransactionAction() {
	Models_Form_CancelTransaction::render($this->getRequest(), $this->view);
}

public function branchFundingAction() {
	$ws = Zend_Registry::get('ws');

	$inData = $this->getRequest()->getPost();

	$this->view->filterForm = new Models_Form_BranchFundingFilter(self::BRANCH_FUNDING_ID);
	if(empty($inData)) {
		$inData['inNeedOfFunds'] = '1';
		$inData['notInNeedOfFunds'] = '1';
		$inData['active'] = '1';
	}
	
	$this->view->filterForm->populate($inData);



	$fundHostId = $this->getRequest()->getPost(self::FUND_HOST_ID);
	$fundAll = $this->getRequest()->getPost(self::FUND_ALL);
	$fundSelected = $this->getRequest()->getPost(self::FUND_SELECTED);
	$confirmFunds = $this->getRequest()->getPost(self::CONFIRM_FUNDS);
	$cancelFunds = $this->getRequest()->getPost(self::CANCEL_FUNDS);

	if ( !empty($fundHostId) ) {
		try {
			$deposit = $this->getRequest()->getPost('deposit');
			$withdraw = $this->getRequest()->getPost('withdraw');

			if ( !empty($deposit) && !is_numeric($deposit)) {
				throw new Exception('Invalid deposit');
			}

			if ( !empty($withdraw) && !is_numeric($withdraw)) {
				throw new Exception('Invalid deposit');
			}

			if ( !empty($deposit) && !empty($withdraw)) {
				throw new Exception('Just one of the fields: deposit, withdraw should be filled.');
			}
			$centralCurrency = $ws->Currency->getSystemId();
			$transaction = array(
				'hostId' => $fundHostId,
				'currencyId' => $centralCurrency);

			if ( is_numeric($deposit) && !empty($deposit) ) {
				$transaction['typeName'] = self::DEPOSIT_TRANSACTION_NAME;
				$transaction['value'] = $deposit;
			}
			if ( is_numeric($withdraw) && !empty($withdraw) ) {
				$transaction['typeName'] = self::WITHDRAW_TRANSACTION_NAME;
				$transaction['value'] = -$withdraw;
			}

			$transactionId = $ws->Transaction->make($transaction);

			$this->view->message = UiUtil::printMessages('Transaction commited successfuly.');

			if ( $transaction['typeName'] == self::WITHDRAW_TRANSACTION_NAME)
				It6_Log::info(
					"Withdraw of the host '%hostId% with amount '%amount%'",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'hostId' => $fundHostId,
						'amount' => -$transaction['value'],
						'transactionId' => $transactionId
					));
			else
				It6_Log::info(
					"Deposit of the host '%hostId% with amount '%amount%'",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'hostId' => $fundHostId,
						'amount' => $transaction['value'],
						'transactionId' => $transactionId));

		} catch( Exception $e ) {
			if ( $transaction['typeName'] == self::WITHDRAW_TRANSACTION_NAME)
				It6_Log::warn(
					"Withdraw of the host '%hostId%' with amount '%amount%' failed.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('hostId' => $fundHostId,'amount' => -$transaction['value']));
			else
				It6_Log::warn(
					"Deposit of the host '%hostId%' with amount '%amount%' failed.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('hostId' => $fundHostId, 'amount' => $transaction['value']));

			It6_Log::err($e);

			$this->view->message = UiUtil::printErrors($e->getMessage());
		}

	}

	if ( !empty($fundAll) || !empty($fundSelected) ) {
		$this->view->confirmation = true;
		$deposits = $this->getRequest()->getPost('deposit');
		$withdraws = $this->getRequest()->getPost('withdraw');

		if ( !empty($fundSelected) ) {
			$selected_hosts = $this->getRequest()->getPost('selected_hosts');
			if ( !empty($selected_hosts) ) {
				foreach ($selected_hosts as $host_id) {
					if ( array_key_exists($host_id, $deposits) ) {
						$tmp_depostis[$host_id] = $deposits[$host_id];
					}
					if ( array_key_exists($host_id, $withdraws) ) {
						$tmp_withdraws[$host_id] = $withdraws[$host_id];
					}
				}
				$deposits = $tmp_depostis;
				$withdraws = $tmp_withdraws;
			}
		}

		$this->view->deposits = array();
		$this->view->withdraws = array();

		foreach ( $deposits as $k => $v ) {
			if ( !empty($v) ) {
				$host = $ws->Host->getById($k);
				$host->value = $v;
				$this->view->deposits[] = $host;
			}
		}

		foreach ( $withdraws as $k => $v ) {
			if ( !empty($v) ) {
				$host = $ws->Host->getById($k);
				$host->value = $v;
				$this->view->withdraws[] = $host;
			}
		}
	}
	else {
		if ( !empty($cancelFunds) ) {
			$this->view->message = UiUtil::printMessages('Funds submission canceled.');
		}
		else if( !empty($confirmFunds) ) {
			$deposits = $this->getRequest()->getPost('deposit');
			$withdraws = $this->getRequest()->getPost('withdraw');

			$centralCurrency = $ws->Currency->getSystemId();

			$this->view->message = '';

			foreach ( $deposits as $k => $v ) {
				try {
					$transactionId = $ws->Transaction->make(array(
						'hostId' => $k,
						'currencyId' => $centralCurrency,
						'typeName' => self::DEPOSIT_TRANSACTION_NAME,
						'value' => $v));

					It6_Log::info(
						"Deposit of the host '%hostId% with amount '%amount%'",
						It6_Log::TAG_ADMIN_OPERATION,
						array('hostId' => $k, 'amount' => $v, 'transactionId' => $transactionId));
				}
				catch (Exception $e) {
					It6_Log::warn(
						"Deposit of the host '%hostId%' with amount '%amount%' failed.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('hostId' => $k, 'amount' => $v));

					It6_Log::err($e);
					$this->view->message .= UiUtil::printErrors("Deposit failed on host '$k': " . $e->getMessage());
				}

			}

			foreach ( $withdraws as $k => $v ) {
				try {
					$transactionId = $ws->Transaction->make(array(
						'hostId' => $k,
						'currencyId' => $centralCurrency,
						'typeName' => self::WITHDRAW_TRANSACTION_NAME,
						'value' => -$v));

					It6_Log::info(
						"Withdraw of the host '%hostId% with amount '%amount%'",
						It6_Log::TAG_ADMIN_OPERATION,
						array('hostId' => $k, 'amount' => $v, 'transactionId' => $transactionId));
				} catch ( Exception $e ) {
					It6_Log::warn(
						"Withdraw of the host '%hostId%' with amount '%amount%' failed.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('hostId' => $k, 'amount' => $v));

					It6_Log::err($e);
					$this->view->message .= UiUtil::printErrors("Withdraw failed on host '$k': " . $e->getMessage());
				}

			}

			if ( empty($this->view->message) )
				$this->view->message .= UiUtil::printMessages('Funds submission successfull.');
			else
				$this->view->message .= UiUtil::printWarnings('Funds submission with some errors.');
		}

		$this->view->hosts = array();
		if ( isset($inData['filter']) ) {

			$extensions	= array();
			$fiterDef	= array();
			if(!empty($inData['branchHandle'])) {
				if (strpos($inData['branchHandle'], ';') || strpos($inData['branchHandle'], ' ') || strpos($inData['branchHandle'], ',')) {
					$inData['branchHandle'] = trim($inData['branchHandle'], '; ,');
					$branchHandles = explode(',', strtr($inData['branchHandle'], array(';' => ',', ' ' => ',')));
					$filterDef[] = array('?'=> array('branchHandle' => $branchHandles), 'OP' => 'IN (?)');
				} else {
					$filterDef[] = array(array('?' => array('branchHandle' => $inData['branchHandle'])));
				}
			}
			
			if(!empty($inData['townInitial']) && $inData['townInitial'] == 'Ch')
				$filterDef[] = array(array('?' => array('branchTown' => $inData['townInitial']), 'EXPR' => 'substring(?, 1, 2)'));
			else if(!empty($inData['townInitial']))
				$filterDef[] = array(array('?' => array('branchTown' => $inData['townInitial']), 'EXPR' => 'substring(?, 1, 1)'));
				
			if ( isset($inData['active']) ) {
				$filterDef[] = array(array('?' => array('branchIsActive' => $inData['active'])));
			}

			if(!empty($filterDef)) {
				$filter = new It6_WsExtension_Client_Filter('filter', $filterDef);
				$extensions[] = $filter;
			}

			$hosts = $ws->ext($extensions)->Host->getAll();

			foreach ( $hosts as $host ) {
				$inOut = $ws->Host->getInOut($host->hostId);
				$host->recommendedDeposit = $inOut['recommendedDeposit'];
				$host->recommendedWithdraw = $inOut['recommendedWithdraw'];
				$host->winTicketsCount = $inOut['winTicketsCount'];
				$host->winTicketsCash = $inOut['winTicketsCash'];
				$host->newWinTicketsCount = $inOut['newWinTicketsCount'];
				$host->newWinTicketsCash = $inOut['newWinTicketsCash'];
				
				
				$filtrRef = max($host->recommendedDeposit, $host->recommendedWithdraw);
				if(
					($filtrRef > 0 && !empty($inData['inNeedOfFunds']))
					|| ($filtrRef == 0 && !empty($inData['notInNeedOfFunds']))
				) {
					$host->fundButton =
						'<button onClick="loadGeneric('.
							self::BRANCH_FUNDING_ID .
							', \'content\', { ajax: true, '.
							self::FUND_HOST_ID . ' : \'' .
							$host->hostId.'\',deposit : document.getElementById(\'deposit_'
								. $host->hostId . '\').value'
								.', withdraw : document.getElementById(\'withdraw_'
								. $host->hostId . '\').value'
								.', inNeedOfFunds : '.(isset($_REQUEST['inNeedOfFunds'])?$_REQUEST['inNeedOfFunds']:1)
								.', notInNeedOfFunds : '.(isset($_REQUEST['notInNeedOfFunds'])?$_REQUEST['notInNeedOfFunds']:1)
								.', active : '.(isset($_REQUEST['active'])?$_REQUEST['active']:1)
								.", townInitials : '".(empty($_REQUEST['townInitial'])?'':$_REQUEST['townInitial'])."'"
								.'})">' .
							i18n::tr('fund').'</button>';

					$host->resetButton =
						'<button onClick="document.getElementById(\'deposit_'
							. $host->hostId . '\').value = '
								. (is_numeric($host->recommendedDeposit) ? $host->recommendedDeposit : 0 )
									.'; document.getElementById(\'withdraw_'
										. $host->hostId . '\').value = '
											. (is_numeric($host->recommendedWithdraw) ? $host->recommendedWithdraw : 0 )
												. '; return false;">' .
													i18n::tr('reset').'</button>';

					$host->nullButton =
						'<button onClick="document.getElementById(\'deposit_'
							. $host->hostId . '\').value = 0'
								.'; document.getElementById(\'withdraw_'
									. $host->hostId . '\').value = 0'
										. '; return false;">' .
											i18n::tr('null').'</button>';

					$this->view->hosts[] = $host;
				}

			}

			function cmp_hosts($a, $b) {
				$compareNumA = max($a->recommendedDeposit, $a->recommendedWithdraw);
				$compareNumB = max($b->recommendedDeposit, $b->recommendedWithdraw);
				if ($compareNumA == $compareNumB) {
					return 0;
				}
				return ($compareNumA > $compareNumB) ? -1 : 1;
			}

			usort($this->view->hosts, "cmp_hosts");
		}
	}
}

public function predepositsAction() {
	$ws = Zend_Registry::get('ws');

	$depositTransactionId = $this->getRequest()->getPost(self::MAKE_DEPOSIT_TRANSACTION_ID);
	$cancelTransactionId = $this->getRequest()->getPost(self::CANCEL_TRANSACTION_ID);
	$depositSelected = $this->getRequest()->getPost(self::DEPOSIT_SELECTED);
	$cancelSelected = $this->getRequest()->getPost(self::CANCEL_SELECTED);
	if ( !empty($depositTransactionId) ) {
		try {
			$transaction = $ws->Transaction->getById($depositTransactionId);
			$ws->Transaction->deposit($depositTransactionId, true);
			$this->view->result = UiUtil::printMessages(i18n::tr('Transaction deposited.'));
			It6_Log::info(
				"Transaction '%transactionId%' deposited.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'transactionId' => $depositTransactionId,
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));
		} catch ( Exception $e ) {
			It6_Log::warn(
				"Transaction '%transactionId%' deposit failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				$transaction);
			It6_Log::err($e);
			$this->view->result = UiUtil::printErrors(i18n::tr('Transaction deposit failed'));
		}
	}
	if ( !empty($cancelTransactionId) ) {
		try {
			$transaction = $ws->Transaction->getById($cancelTransactionId);
			$ws->Transaction->deposit($cancelTransactionId, false);
			$this->view->result = UiUtil::printMessages(i18n::tr('Transaction deposit canceled.'));
			It6_Log::info(
				"Transaction '%transactionId%' deposit canceled.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'transactionId' => $cancelTransactionId,
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));
		} catch ( Exception $e ) {
			It6_Log::warn(
				"Transaction '%transactionId%' deposit cancelation failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				$transaction);
			It6_Log::err($e);
			$this->view->result = UiUtil::printErrors(i18n::tr('Transaction deposit cancelation failed'));
		}

	}
	if ( !empty($depositSelected) ) {
		$selected = $this->getRequest()->getPost(self::SELECTED);
		$this->view->result = '';
		$n = 0;
		foreach ( $selected as $item ) {
			try {
				$transaction = $ws->Transaction->getById($item);
				$ws->Transaction->deposit($item, true);
				++$n;
				It6_Log::info(
					"Transaction '%transactionId%' deposited.",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'transactionId' => $item,
						'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
						'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
						'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));
			} catch ( Exception $e ) {
				$this->view->result .= UiUtil::printErrors(i18n::tr('Transaction deposit failed: ') . $item);
				It6_Log::warn(
					"Transaction '%transactionId%' deposit failed.",
					It6_Log::TAG_ADMIN_OPERATION,
					$transaction);
				It6_Log::err($e);
			}
		}

		if ( !empty($this->result) )
			$this->view->result .= UiUtil::printWarnings(i18n::tr('Some deposits failed.'));

		$this->view->result .= UiUtil::printMessages(i18n::tr('Successfully deposited transactions: ') . $n);
	}
	else if ( !empty($cancelSelected) ) {
		$selected = $this->getRequest()->getPost(self::SELECTED);
		$this->view->result = '';
		$n = 0;
		foreach ( $selected as $item ) {
			try {
				$transaction = $ws->Transaction->getById($item);
				$ws->Transaction->deposit($item, false);
				++$n;
				It6_Log::info(
					"Transaction '%transactionId%' deposit canceled.",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'transactionId' => $item,
						'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
						'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
						'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));
			} catch ( Exception $e ) {
				It6_Log::warn(
					"Transaction '%transactionId%' deposit cancelation failed.",
					It6_Log::TAG_ADMIN_OPERATION,
					$transaction);
				It6_Log::err($e);
				$this->view->result .= UiUtil::printErrors(i18n::tr('Transaction deposit cancel failed: ') . $item);
			}
		}

		if ( !empty($this->view->result) )
			$this->view->result = UiUtil::printWarnings(i18n::tr('Some cancelations failed.'));
		$this->view->result = UiUtil::printMessages(i18n::tr('Deposit cancel transactions: ') . $n);
	}

	$showTypeFilter = $this->getRequest()->getParam('showTypeFilter');
	$typeIds = $this->getRequest()->getParam('typeId');
	$this->view->form = new Models_Form_PredepositsFilter(self::PREDEPOSITS_ID, $showTypeFilter, $typeIds);

	if ( !$this->view->form->isValid($this->getRequest()->getParams()) ) {
		$this->view->result = UiUtil::printErrors(i18n::tr('Validation error'));
	}

	$values = $this->view->form->getValues();
	$this->view->form->populate($values);

	$where = array("status = 'pre-deposit'");
	if ( !empty($values['userHandle']) )
		$where['userHandle = ?'] = $values['userHandle'];
	if ( !empty($values['hostId']) )
		$where['hostId = ?'] = $values['hostId'];
	if ( !empty($values['typeId']) )
		$where['typeId IN (?)'] = $values['typeId'];
	if ( !empty($values['value']) )
		$where['ABS(value) > ?'] = $values['value'];
	if ( !empty($values['fromDate']) )
		$where['time >= ?'] = It6_Date::toDb($values['fromDate']);
	if ( !empty($values['toDate']) )
		$where['time <= ?'] = It6_Date::toDb($values['toDate']);

	if ( !empty($_REQUEST['export']) )	{
		$transactions = $ws->Transaction->getAllWhereOrder($where, $this->order);
		echo It6_Models_ExportHelper::assocArrayToCsv($transactions);
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		header('Content-type: text/plain');
		header("Content-Disposition: attachment; filename=\"predeposits.csv\"");
		return;
	}

	if(!empty($typeIds)) {
		$transactions = $ws->Transaction->getAllWhereOrder($where, $this->order);

		$this->view->transactions = array();
		foreach ( $transactions as $transaction ) {
			$transaction->confirmButton =
				'<button onClick="loadGeneric('.
					self::PREDEPOSITS_ID .
					', \'content\', { ajax: true, '.
					self::MAKE_DEPOSIT_TRANSACTION_ID . ' : \'' .
					$transaction->transactionId.'\'})">' .
					i18n::tr('deposit').'</button>';

			$transaction->cancelButton =
				'<button onClick="loadGeneric('.
					self::PREDEPOSITS_ID .
					', \'content\', { ajax: true, ' .
					self::CANCEL_TRANSACTION_ID . ' : \'' .
					$transaction->transactionId.'\'})">' .
					i18n::tr('no-deposit').'</button>';

			$this->view->transactions[] = $transaction;
		}
	}
	else
		$this->view->transactions = false;
}

public function confirmationsExportAction() {
	$file = $this->_request->getParam('file');
	if (!empty($file) && 1 == preg_match('/[-0-9a-z_.]+/i', $file)) {
		$filePath = It6_Bank_Batch_Export_Kb_Best::getExportsDir() . $file;
		if (file_exists($filePath)) {
			header('Content-Type: application/octet-stream');
			header('Content-Length: ' . filesize($filePath));
			header('Content-Disposition: attachment; filename="'.$file.'"');
			header("Content-Transfer-Encoding: binary\n");
			$fh = fopen($filePath, 'r');
			fpassthru($fh);
			fclose($fh);
			exit;
		}
		else {
			$this->view->error = 'File not found: ' . $file;
			$this->_response->setHttpResponseCode(404);
		}
	}
	else {
		$ws = Zend_Registry::get('ws');
		if ($this->_request->isPost()) {
			$dbAdmin = Zend_Registry::get('zdb_admin');
			$ts = $ws->Transaction->getAllForExport();
			$ts = It6_ArrayWrapper::toNativeArray($ts);
			if (!empty($ts)) {
				$export = new It6_Bank_Batch_Export_Kb_Best($dbAdmin);
				$content = $export->export($ts);
				if (!empty($content)) {
					$dir = $export->getExportsDir();
					$now = time();
					$file = $export->getNewExportFileName($now);
					file_put_contents($dir . $file, $content);
					$tIds = array();
					foreach ($ts as $t)
						$tIds[] = $t['transactionId'];
					$ws->Transaction->setExported($tIds, $now);
					$this->view->lastExport = array(
						'title' => $file,
						'url' => Models_ConfirmationsExport::getExportUrl($file),
					);
				}
			}
		}
		else
			$ts = $ws->Transaction->getAllForExport();
		$this->view->sectionId = self::CONFIRMATIONS_EXPORT_ID;
		$this->view->ts = $ts;
		$this->view->exports = Models_ConfirmationsExport::getExports();
	}
}

}
