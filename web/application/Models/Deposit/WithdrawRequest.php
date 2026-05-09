<?php

class Models_Deposit_WithdrawRequest {

	const AMOUNT = 'amount';
	const BRANCH_ID = 'branchId';
	const WITHDRAW_METHOD = 'withdrawMethod';

	protected $_transactionType = null;

	public $currencies = null;
	public $minAmount = false;
	public $maxAmount = false;
	public $feeFix = 0; // central currency
	public $feeRel = 0; // percent

	public function __construct($view) {
		$view->userId = (Zend_Registry::isRegistered('user_id') ? Zend_Registry::get('user_id') : 0);
		$transactionType = Zend_Registry::get('ws')->TransactionType
			->getByName(Webservice_TransactionType::NAME_USER_WITHDRAW_BANK);
		if (empty($transactionType))
			throw new ExHandler('Transaction type "' . Webservice_TransactionType::NAME_USER_WITHDRAW_BANK . '" not found');

		//$view->_transactionType = $transactionType;
		$view->minAmount = (isset($transactionType->lowLimit) ? $transactionType->lowLimit : false);
		$view->maxAmount = (isset($transactionType->highLimit) ? $transactionType->highLimit : false);
		$view->feeFix = (empty($transactionType->feeFix) ? 0 : $transactionType->feeFix);
		$view->feeRel = (empty($transactionType->feeRel) ? 0 : $transactionType->feeRel);

		$view->currencies = array();
		foreach (It6_Models_Currency::readDataAll() as $id => $data) {
			if ($data['allowed']) {
				$rate = $data['rate'];
				$data2 = array(
					'feeFix' => $view->feeFix * $data['rate'],
					'feeRel' => $view->feeRel,
				);
				if (false !== $view->minAmount)
					$data2['minAmount'] = $view->minAmount * $rate;
				if (false !== $view->maxAmount)
					$data2['maxAmount'] = $view->maxAmount * $rate;
				$view->currencies[$id] = array_merge($data, $data2);
			}
		}

	}

	public static function buildForm($view, $name, $display = TRUE) {

		$ws = Zend_Registry::get('ws');
		$userData = $ws->User->getById(Zend_Registry::get('user_id'));

		$form = new It6_Models_DecoratedForm_Table();

		$form->setName($name)->setAction('./')->setMethod('post');

		$form->addElement(self::createToken($view, $name . 'Token'));

		if (!$display) $form->setAttrib('style','display:none');

		if ($name == 'withdrawBank') $trName = Webservice_TransactionType::NAME_USER_WITHDRAW_BANK;
		else $trName = Webservice_TransactionType::NAME_USER_WITHDRAW_CASH;

		$transactionType = Zend_Registry::get('ws')->TransactionType->getByName($trName);

		$limitLow = max(0,-$transactionType->highLimit);
		$limitHigh = min($userData['balance'],-$transactionType->lowLimit);

		$view->balance = $userData['balance'];

		$method = $form
			->createElement('hidden', self::WITHDRAW_METHOD)
			->setValue($name)
			->setAttrib('id', self::WITHDRAW_METHOD.$name);
		$form->addElement($method);

		$amount = $form->createElement('text', self::AMOUNT, array('maxlength'=> 9));
		$amount
			->setlabel('withdraw_amount')
			->addValidator(new It6_Validate_Int(array('min' => $limitLow, 'max' => $limitHigh)))
			->addFilter(new It6_Filter_Int())
			->setValue(It6_Validate_Int::formatInteger(0))
			->setAttrib('id', self::AMOUNT.$name)
			->setAttrib('class', 'input110 margin2')
			->setRequired(true);
		$form->addElement($amount);

		$model = new Models_Deposit_WithdrawRequest($view);

		if ($name == 'withdrawBank') {
			if ( count($view->currencies) > 1 ) {
				$currencyOpts = array();
				foreach ($view->currencies as $c)
					$currencyOpts[ $c['isoCode'] ] = $c['name'];


				$elemPropperty = NULL;
				if (count($currencyOpts)==1) $elemPropperty = array('disabled' => 'disabled');

				$elemCurrency = $form->createElement('select','withdrawCurrency',$elemPropperty);
				$elemCurrency->setRequired(true)
					->setLabel('withdraw_currency')
					->setMultiOptions($currencyOpts);
				if ($view->userId) {
					$userCurrency = It6_Models_User::get($view->userId, 'currencyId');
					if ($userCurrency && array_key_exists($userCurrency, $view->currencies))
						$elemCurrency->setValue($view->currencies[$userCurrency]['isoCode']);
				}
				$form->addElement($elemCurrency);
			}

			$elemFee = $form->createElement('text','withdrawFee');
			$elemFee->setRequired(false)
				->setLabel('withdraw_fee')
				->setAttrib('disabled', 'disabled')
				->setAttrib('class', 'input110 margin2')
				->setAttrib('style', 'color: #666')
				->setValue($view->formatCurrency(0));
			$form->addElement($elemFee);

			$elemTotal = $form->createElement('text','withdrawTotal');
			$elemTotal->setRequired(false)
				->setLabel('total_amount')
				->setAttrib('disabled', 'disabled')
				->setAttrib('class', 'input110 margin2')
				->setAttrib('style', 'color: #666')
				->setValue($view->formatCurrency(0));
			$form->addElement($elemTotal);
		}

		if ($name == 'withdrawBranch') {
			$branches = $ws->Branch->getAllWithdrawEnabled();
			$elm = $form->createElement('select',self::BRANCH_ID);
			$elm->setLabel($view->trans('withdraw_branch'));
			$elm->setDisableTranslator(true);
			$options = array('0' => 'choose_branch');
			foreach ($branches as $b) {
				$options[$b['branchId']] = trim($b['town']) . ' - ' . trim($b['street']);
			}
			$elm->addMultiOptions($options);
			$elm->setValue($userData['branchId']);
			$elm->addValidator(new It6_Validate_Isolator(new It6_Validate_SelectNon0()));
			$elm->class = 'goldSelect192 margin2';

			//$elm->setRequired(true);
			//$elm->setAttrib('id','branches');
			$form->addElement($elm);
		}
		//} else {
			//pokud uzivatel nema vyplneno bankovni spojeni
		//	$userBankAccount = $ws->UserBankAccount->getByUserId(Zend_Registry::get('user_id'));
			//if (empty($userBankAccount)) {
				/*$form = new It6_Models_DecoratedForm_Table();
				$form->setName($name)->setAction('./')->setMethod('post');
				$form->setAttrib('class','errorForm');
				if (!$display) $form->setAttrib('style','display:none');
				$warning = $form->createElement('text', 'nope', array('style' => 'display:none'));
				$warning->setlabel('user_bank_account_incomplete');
				$form->addElement($warning);

				return $form;*/
				//return It6_FeedbackMsg::printError('user_bank_account_incomplete');
		//	}
		//}
		$form->addElement('submit', 'submit', array('label' => 'send_request','class'=>'btn'));
		$form->getElement('submit')->setAttrib('id', 'submit'.$name);

		return $form;
	}

	public static function createToken($view, $name) {
		$token = new It6_Form_Element_Hash($name);
		if (isset($view)) {
			$view->$name = $token->getHash();
		}
		return $token;
	}

	public static function submitBank($view,$name) {
		//$this->tr = Zend_Registry::get('translate');
		$errorType = 'form validation error';
		$view->resultBranch = NULL;
		$form = self::buildForm($view, $name, true);
		if ($form->isValid($_POST)) {
			$amount = $form->getValue(self::AMOUNT);
//			var_dump($view->currencies);
	//		var_dump($_POST);

			if ( count($view->currencies) > 1 )
				$currencyId = self::currencyIsoCodeToId($_POST['withdrawCurrency'],$view);
			else {
				$currencyId = current($view->currencies);
				$currencyId = self::currencyIsoCodeToId($currencyId['isoCode'], $view);
			}

			$fee = self::getFee($currencyId,$amount,$view);

			//dodatecna validace souctu fee+amount (validator validuje jen amount<balance)
			if ( $amount <= 0 || ( $view->balance < $amount + $fee)) {
				$form->populate($_POST);
				$view->formBank = $form;
				$form->addError($view->trans('error_withdraw_request'));
				$view->error = true;
				//$view->result = 'Must be non negative';
				$view->resultBank =It6_FeedbackMsg::printError('error_withdrawal_request_bank');
			}
			else {
				try {
					Zend_Registry::get('ws')->Transaction->make(array(
						'typeName' => 'user.withdraw.bank',
						'userId' => Zend_Registry::get('user_id'),
						'currencyId' => $currencyId,
						'value' => -$amount,
						'fee' => $fee,
						'hostId' => It6_Models_Host::ID_INTERNET));
					$view->error = false;
					$view->success = true;
					$view->resultBank = It6_FeedbackMsg::printNotice('success_withdrawal_request_via_bank');
					$view->formBank = '';
					It6_Log::info(
						"Withdraw request bank success",
						It6_Log::TAG_USER_OPERATION,
						array(
							'userId' => Zend_Registry::get('user_id'),
							'amount' => $_POST['amount'],
							'fee' => $fee,
							'currencyId' => $currencyId,
							'userBalance' => $view->balance
							)
					);
				}
				catch (Exception $e) {
					$form->addError($e->getMessage());
					//$this->error = true;
					$errorType = 'form sending error';
					$view->result = $e->getMessage();
				}
			}
		} else {
			$form->populate($_POST);
			$view->formBank = $form;
			$form->addError($view->trans('error_withdraw_request'));
			$view->error = true;
//			$view->result = 'Must be a non negative integer';
			$view->resultBank = It6_FeedbackMsg::printError('error_withdrawal_request_bank');
		}

		if ($view->error) {
			//TODO Je nutne toto evidovat jako error?
			It6_Log::notice(
				"Withdraw request bank $errorType",
				It6_Log::TAG_USER_OPERATION,
				array(
					'userId' => Zend_Registry::get('user_id'),
					'amount' => $_POST['amount'],
					'fee' => isset($fee) ? $fee : null,
					'currencyId' => isset($currencyId) ? $currencyId : null,
					'userBalance' => $view->balance
					)
			);
		}
	}

	public static function submitBranch($view,$name) {
		//$this->tr = Zend_Registry::get('translate');
		$errorType = 'form validation error';
		$view->resultBank = NULL;
		$form = self::buildForm($view, $name, true);
		if ($form->isValid($_POST)) {

			$ws = Zend_Registry::get('ws');
			$amount = $form->getValue(self::AMOUNT);
			$branchId = $form->getValue(self::BRANCH_ID);
			if ( $amount <= 0 || !isset($branchId) ) {
				$form->populate($_POST);
				$view->formBranch = $form;
				$form->addError($view->trans('error_withdraw_request'));
				$view->error = true;
				//$view->result = 'Must be non negative';
				$view->resultBranch = It6_FeedbackMsg::printError('error_withdrawal_request_branch'); //$this->tr 'error_withdraw_request';
			}
			else {
				try {
					//TODO betterway how to choose host in the branch
					$hosts = $ws->Host->getActiveByBranchId($branchId);
					$hostId = $hosts[0]->hostId;
					$userId = Zend_Registry::get('user_id');
					$currencyId = It6_Models_User::get($userId, 'currencyId', $db);
					$transactionData = array(
						'typeName' => 'user.withdraw.cash',
						'userId' => $userId,
						'currencyId' => $currencyId,
						'hostId' => $hostId,
						'notes' => 'branchId: '.$branchId,
						'value' => -$amount
					);
					$ws->User->requestCashWithdrawal($transactionData);
					$view->error = false;
					$view->success = true;
					$view->resultBranch = It6_FeedbackMsg::printNotice('success_withdrawal_request_via_branch');
					$view->formBranch = '';
				}
				catch (Exception $e) {
					if ($e->getCode() == 100) { // User.requestCashWithdrawal : not allowed
						$view->error = true;
					}
					else {
						$form->addError($e->getMessage());
						//$this->error = true;
						$errorType = 'form sending error';
						$view->result = $e->getMessage();
					}
				}
			}
		} else {
			$view->error = true;
		}

		if ($view->error) {
			$form->populate($_POST);
			$view->formBranch = $form;
			$form->addError($view->trans('error_withdraw_request'));
//			$view->result = 'Must be a non negative integer';
			$view->resultBranch =It6_FeedbackMsg::printError('error_withdrawal_request_branch'); //$this->tr 'error_withdraw_request';
			It6_Log::notice(
				"Withdraw request branch $errorType",
				It6_Log::TAG_USER_OPERATION,
				array(
					'userId' => Zend_Registry::get('user_id'),
					'amount' => $_POST['amount'],
					'branchId' => $_POST['branchId']
				)
			);
		}
	}

	public static function getFee($currencyId, $amount, $view) {
		if (!array_key_exists($currencyId, $view->currencies))
			throw new ExHandler('Unknown currency ID: ' . $currencyId);
		$currency = $view->currencies[$currencyId];
		return ($currency['rate'] * $view->feeFix + $amount * $view->feeRel);
	}

	public static function currencyIsoCodeToId($isoCode, $view) {
	foreach ($view->currencies as $id => $data) {
		if ($data['isoCode'] == $isoCode)
			return $id;
	}
	return false;
}
}
