<?php

class DepositController extends Zend_Controller_Action {

private $THEAD_LAYOUT			= 'standard-thead-noorder';
private $TBODY_LAYOUT_PTRANS	= 'point-transaction-tbody';
private $TBODY_LAYOUT_TRANS		= 'transaction-tbody';

private $filterData		= array();
private $paginatorData	= array('recsPerPage' => 10);
private $orderData		= array('createdTime DESC');



public function init() {
	$this->tr = Zend_Registry::get('translate');
	$this->ws = Zend_Registry::get('ws');
	$this->userId = (Zend_Registry::isRegistered('user_id') ? Zend_Registry::get('user_id') : 0);
	$this->view->leftMenuItems = Models_Helpers_Panels::getPersonalMenuItems();
	if (empty($this->userId)) {
		$this->view->error = $this->tr->trans('ticket_er_1');
		//$this->_redirect($this->view->urlSet(21)); // vklad-vyber/index
	}
	$this->view->userId = $this->userId;

	It6_GlobalCache::turnOff();
}

/**
 * @deprecated replaced by static page
 */
public function indexAction() {
	Models_BasicRender::render($this->view, $this->_request);
	$this->view->langIso = $this->_request->getParam('lang');
}

public function transactionsAction() {
	$this->view->noRight = true;
	Models_BasicRender::render($this->view,$this->_request);

	$values		= $this->getRequest()->getParams();
	$extensions	= array();

	$this->paginatorData['recsPerPage'] = $this->ws->Parameter->getUserParameter('pagination.Transaction.count',$this->userId);

	if(!empty($values['filter']))
		$this->filterData = $values['filter'];
	if(!empty($values['paginator']))
		$this->paginatorData = $values['paginator'];

	//create filter
	$filter = new It6_WsForm_Filter(array(
		array('created_from', 'timeFrom', 'date', array(
			array(array('DATE(?)' => 'date'), '>= DATE(?)')), 'It6_Validate_Date'),
		array('created_before', 'timeTo', 'date', array(
			array(array('DATE(?)' => 'date'), '<= DATE(?)')), 'It6_Validate_Date'),
	), 'Plain');
	$filter->getExtension($this->filterData, $extensions);

	//create pagination
	$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
	$paginator->getExtension($this->paginatorData, $extensions);


	//create table
	$table = new It6_WsForm_Table(array(
		array('transaction_id','transactionId'),
		array('tt.name','typeName'),
		array('value','value'),
		array('time','time'),
		array('balance','balance')
	));
	$table->getColumnsExtension($extensions);
	//$table->getOrderExtension(array('bhTime DESC', 'transaction_id DESC'), $extensions);

	//make sure the transactions all belong to the proper user
	$filterDefUser = array(
		array('?'=> array('userId' => Zend_Registry::get('user_id'))),
		array('?'=> array('accountType' => 'user'))
	);
	$filterUser = new It6_WsExtension_Client_Filter('filter2', $filterDefUser);
	$extensions[] = $filterUser;


	$transactions	= Zend_Registry::get('ws')->ext($extensions)->Transaction->getAllHistory();

	foreach ( $transactions as $transaction ) {
		if ( 'canceled' == $transaction->bhStatus )
			$transaction->value *= -1;
		//if ( $transaction->startBalance == $transaction->endBalance )
		//	$transaction->value = '-';
	}

	$transactions	= It6_ArrayWrapper::toNativeArray($transactions);

	$this->view->filter		= $filter->getLayout('standard-filter', $this->filterData);
	$this->view->paginator	= $paginator->getLayout(null, 'standard-paginator', $extensions['paginator']->getResponse());
	$this->view->thead		= $table->getTheadLayout($this->THEAD_LAYOUT);
	$this->view->tbody		= $table->getTbodyLayout($this->TBODY_LAYOUT_TRANS, $transactions);
}

public function withdrawRequestAction() {
	$this->view->noRight = true;
	Models_BasicRender::render($this->view,$this->_request);

	$this->view->errorAccessDenied = false;
	$this->view->errorBankDenied = false;

	$userBankAccount = $this->ws->UserBankAccount->getByUserId($this->userId);
	$model = new Models_Deposit_WithdrawRequest($this->view);
	$this->view->currencies	= $model->currencies;
	$this->view->feeRel		= $model->feeRel;
	$this->view->trgt		= $this->getRequest()->getParam('trgt');
/*
	$this->view->currencies = $model->currencies;
	$this->view->feeRel = $model->feeRel;

	$form = new Zend_Form();
	$form->setAction('')
		->setMethod('post')
		->setAttrib('id', 'depositForm');

	$amountLimits = array();
	if (false !== $model->minAmount)
		$amountLimits['min'] = $model->minAmount;
	if (false !== $model->maxAmount)
		$amountLimits['max'] = $model->maxAmount;
	$elemAmount = new Zend_Form_Element_Text('amount');
	$elemAmount->setRequired(true)
		->addValidator(new It6_Validate_Float($amountLimits))
		->setLabel('deposit_amount')
		->setAttrib('size', 10)
		->setAttrib('maxlength', 10)
		->setAttrib('style', 'text-align: right;')
		->setValue($this->formatCurrency(0));
	$form->addElement($elemAmount);
var_dump($amountLimits);
*/

/*
$ws = Zend_Registry::get('ws');
		$userData = $ws->User->getById(Zend_Registry::get('user_id'));

		$form = new It6_Models_DecoratedForm_Table();

		$form->setName($name)->setAction('./')->setMethod('post');
		if (!$display) $form->setAttrib('style','display:none');
		$tresholdMin = new Zend_Validate_GreaterThan(array('min' => 0));
		$tresholdMax = new Zend_Validate_LessThan(array('max' => $userData['balance']));

		$method = $form->createElement('hidden', self::WITHDRAW_METHOD);
		$method->setValue($name);
		$form->addElement($method);

		$amount = $form->createElement('text', self::AMOUNT);
		$amount
			->setlabel('withdraw_amount')
			->addValidator('int')
			->addValidator($tresholdMin)
			->addValidator($tresholdMax)
			->setRequired(true);
		$form->addElement($amount);
*/

/*
	$currencyOpts = array();
	foreach ($model->currencies as $c)
		$currencyOpts[ $c['isoCode'] ] = $c['name'];
	$elemCurrency = new Zend_Form_Element_Select('depositCurrency');
	$elemCurrency->setRequired(true)
		->setLabel('deposit_currency')
		->setMultiOptions($currencyOpts);
	if ($this->userId) {
		$userCurrency = It6_Models_User::get($this->userId, 'currencyId');
		if ($userCurrency && array_key_exists($userCurrency, $model->currencies))
			$elemCurrency->setValue($model->currencies[$userCurrency]['isoCode']);
	}
	$form->addElement($elemCurrency);

	$elemFee = new Zend_Form_Element_Text('depositFee');
	$elemFee->setRequired(false)
		->setLabel('deposit_fee')
		->setAttrib('disabled', 'disabled')
		->setValue($this->formatCurrency(0));
	$form->addElement($elemFee);

	$elemTotal = new Zend_Form_Element_Text('depositTotal');
	$elemTotal->setRequired(false)
		->setLabel('total_amount')
		->setAttrib('disabled', 'disabled')
		->setValue($this->formatCurrency(0));
	$form->addElement($elemTotal);

	$elemSubmit = new Zend_Form_Element_Submit('depositSubmit');
	$elemSubmit->setLabel('');
	$form->addElement($elemSubmit);

	if ($this->getRequest()->isPost()) {
		$valid = $form->isValid($_POST);
		if (!$this->userId)
			$valid = false;
		if ($valid) {
			$depositCurrencyId = $model->currencyIsoCodeToId($_POST['depositCurrency']);
			if (!$depositCurrencyId)
				$valid = false;
		}
		if ($valid) {
			$orderId = $model->registerOrderMuzo($this->userId, $depositCurrencyId, It6_Validate_Float::parseFloat($_POST['depositAmount']));
			if (empty($orderId))
				throw new Exception('Order for online deposit not created');
			$this->view->popUpUrl = $this->view->UrlSet(56, 'id=' . urlencode($orderId));
		}
	}
*/

	//$this->view->form = $form;





	if ($this->ws->User->canWithdraw($this->userId)) {
	//if (!isset($this->_request->withdrawMethod)) {
	//}

		if( isset( $_POST['submit'] ) ) {
			//var_dump($_POST);exit;
			if (isset($this->_request->withdrawMethod) && $this->_request->withdrawMethod == 'withdrawBank') {
				Models_Deposit_WithdrawRequest::submitBank($this->view, $this->_request->withdrawMethod);
				$this->view->formBranch = Models_Deposit_WithdrawRequest::buildForm($this->view, 'withdrawBranch');
			}

			else {
				Models_Deposit_WithdrawRequest::submitBranch($this->view, $this->_request->withdrawMethod);
				$this->view->formBank = Models_Deposit_WithdrawRequest::buildForm($this->view,'withdrawBank');
			}

		}

		if (empty($userBankAccount))
			$this->view->errorBankDenied = It6_FeedbackMsg::printError('user_bank_account_incomplete');
		else
			$this->view->formBank = Models_Deposit_WithdrawRequest::buildForm($this->view,'withdrawBank');
			$this->view->formBranch = Models_Deposit_WithdrawRequest::buildForm($this->view, 'withdrawBranch');

	} else {
		$this->view->errorAccessDenied = It6_FeedbackMsg::printError('user_not_allowed_to_withdraw');
	}
}

public function depositCardAction() {
	$this->view->noRight = true;
	Models_BasicRender::render($this->view,$this->_request);
	$this->view->registerHelper(new It6_View_Helper_FormSubmit(), 'formSubmit');

	$model = new Models_Deposit_Card();
	$this->view->currencies = $model->currencies;
	$this->view->feeRel = $model->feeRel;

	//$form = new Zend_Form();
	$form = new It6_Models_DecoratedForm_Table();
	$form->setAction('')
		->setMethod('post')
		->setAttrib('id', 'depositForm');

	$form->addElement(new It6_Form_Element_Hash('depositCardToken'));

	$amountLimits = array();
	if (false !== $model->minAmount)
		$amountLimits['min'] = $model->minAmount;
	if (false !== $model->maxAmount)
		$amountLimits['max'] = $model->maxAmount;
	$elemAmount = $form->createElement('text','depositAmount');
	$elemAmount->setRequired(true)
		->addValidator(new It6_Validate_Float($amountLimits))
		->setLabel('deposit_amount')
		->setAttrib('size', 10)
		->setAttrib('maxlength', 10)
		->setAttrib('style', 'text-align: right;')
		->setValue($this->formatCurrency(0));
	$form->addElement($elemAmount);

	if ( count($model->currencies) > 1 ) {
		$currencyOpts = array();
		foreach ($model->currencies as $c)
			$currencyOpts[ $c['isoCode'] ] = $c['name'];
		$elemCurrency = $form->createElement('select','depositCurrency');
		$elemCurrency->setRequired(true)
			->setLabel('deposit_currency')
			->setMultiOptions($currencyOpts);
		if ($this->userId) {
			$userCurrency = It6_Models_User::get($this->userId, 'currencyId');
			if ($userCurrency && array_key_exists($userCurrency, $model->currencies))
				$elemCurrency->setValue($model->currencies[$userCurrency]['isoCode']);
		}
		$form->addElement($elemCurrency);
	}

	$elemFee = $form->createElement('text','depositFee');
	$elemFee->setRequired(false)
		->setLabel('deposit_fee')
		->setAttrib('disabled', 'disabled')
		->setValue($this->formatCurrency(0));
	$form->addElement($elemFee);

	$elemTotal = $form->createElement('text','depositTotal');
	$elemTotal->setRequired(false)
		->setLabel('total_amount')
		->setAttrib('disabled', 'disabled')
		->setValue($this->formatCurrency(0));
	$form->addElement($elemTotal);

	$elemSubmit = $form->createElement('submit','depositSubmit', array('class'=>'btn'));
	$elemSubmit->setLabel('submit');
	$form->addElement($elemSubmit);

	if ($this->getRequest()->isPost()) {
		$valid = $form->isValid($_POST);
		if (!$this->userId)
			$valid = false;
		if ($valid) {
			if ( count($model->currencies) > 1 )
				$depositCurrencyId = $model->currencyIsoCodeToId($_POST['depositCurrency']);
			else {
				$depositCurrencyId = current($model->currencies);
				$depositCurrencyId = $model->currencyIsoCodeToId($depositCurrencyId['isoCode']);
			}
			if (!$depositCurrencyId)
				$valid = false;
		}
		if ($valid) {
			$orderId = $model->registerOrderMuzo($this->userId, $depositCurrencyId, It6_Validate_Float::parseFloat($_POST['depositAmount']));
			if (empty($orderId))
				throw new Exception('Order for online deposit not created');
			$this->view->popUpUrl = $this->view->UrlSet(56, 'id=' . urlencode($orderId));
		}
	}

	$this->view->form = $form;
}

public function depositBankAction() {
	$this->view->noRight = true;
	Models_BasicRender::render($this->view,$this->_request);
	$user = $this->ws->User->getById($this->userId);
	$this->view->userHandle = $user['userHandle'];
}

public function depositBranchAction() {
	if(!BRANCHES_ENABLED){
		$this->_redirect($this->view->urlSet(35)); // muj-ucet/osobni
	}
	$this->view->noRight = true;
	Models_BasicRender::render($this->view,$this->_request);
}

public function exchangePointsAction() {


	if( isset( $_POST['submit'] ) ) {
		Models_Deposit_ExchangePoints::submit($this->view);
	}

	Models_Deposit_ExchangePoints::render($this->view);
	Models_BasicRender::render($this->view,$this->_request);
}

public function pointTransactionsAction() {  
	$this->view->noRight = true;
	
	Models_BasicRender::render($this->view,$this->_request);

	$values		= $this->getRequest()->getParams();
	$extensions	= array();

	$this->paginatorData['recsPerPage'] = $this->ws->Parameter->getUserParameter('pagination.PointTransaction.count',$this->userId);

	if(!empty($values['filter']))
		$this->filterData = $values['filter'];
	if(!empty($values['paginator']))
		$this->paginatorData = $values['paginator'];

	//create filter
	$filter = new It6_WsForm_Filter(array(
		array('created_from', 'timeFrom', 'date', array(
			array(array('DATE(?)' => 'date'), '>= DATE(?)')), 'It6_Validate_Date'),
		array('created_before', 'timeTo', 'date', array(
			array(array('DATE(?)' => 'date'), '<= DATE(?)')), 'It6_Validate_Date'),
	), 'Plain');
	$filter->getExtension($this->filterData, $extensions);


	//create pagination
	$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
	$paginator->getExtension($this->paginatorData, $extensions);


	//create table
	$table = new It6_WsForm_Table(array(
		array('transaction_id','transactionId'),
		array('tt.name','typeName'),
		array('time','time'),
		array('value','value'),
		array('balance','balance'),
	));
	$table->getColumnsExtension($extensions);
	$table->getOrderExtension(array('time DESC','transaction_id DESC'), $extensions);

	//make sure the point transactions all belong to the proper user
	$filterDefUser = array('?'=> array('userId' => Zend_Registry::get('user_id')), 'OP' => '=');
	$filterUser = new It6_WsExtension_Client_Filter('filter2', $filterDefUser);
	$extensions[] = $filterUser;


	$transactions	= Zend_Registry::get('ws')->ext($extensions)->PointsTransaction->getAll();
	$transactions	= It6_ArrayWrapper::toNativeArray($transactions);

	$this->view->filter		= $filter->getLayout('standard-filter', $this->filterData);
	$this->view->paginator	= $paginator->getLayout(null, 'standard-paginator', $extensions['paginator']->getResponse());
	$this->view->tbody		= $table->getTbodyLayout($this->TBODY_LAYOUT_PTRANS, $transactions);	
	
	$this->view->user = Zend_Registry::get('ws')->User->getById(Zend_Registry::get('user_id'));

}

private function formatCurrency($value, $name = null) {
	$s = It6_Validate_Float::formatFloat($value, 2);
	if (!empty($name))
		$s .= ' ' . $name;
	return $s;
}

} // class DepositController
