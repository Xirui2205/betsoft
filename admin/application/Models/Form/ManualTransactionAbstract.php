<?php

abstract class Models_Form_ManualTransactionAbstract {

	protected static $FORM_METHOD = 'get';
	protected static $FORM_CSS_CLASS = 'table-filter';
	protected static $OMIT_CURRENCY = false;
	protected static $SIGN = 1;

	const MSG_INSERT_SUCCESSFUL = 'insert-transaction-successful';
	const MSG_INSERT_NOT_SUCCESSFUL = 'insert-transaction-not-successful';
	const MSG_ARE_YOU_SURE = 'Are you sure to commit the transaction?';
	const MSG_CONFIRM_TRANSACTION = 'confirm-transaction';
	const MSG_CANCEL_TRANSACTION = 'cancel-transaction';
	const MSG_INSERT_TRANSACTION = 'insert-transaction';

	public static function fixValuesUserId(&$values) {
		if (is_array($values) && !array_key_exists('userId', $values) && array_key_exists('userHandle', $values)) {
			try {
				$user = Zend_Registry::get('ws')->User->getByHandle($values['userHandle']);
				$values['userId'] = $user->userId;
			}
			catch (Exception $e) {}
		}
	}

	public static function render($request, $view) {
		$form = static::getInsertForm();
		if ( null != $request->getParam('insert')
				&& $form->isValid( $request->getParams() ) ) {

			$view->message = '';

			$values = $form->getValues();
			static::fixValuesUserId($values);
			$similar =  static::findSimilarTransaction($values);


			if ( false != $similar ) {
				$view->message = UiUtil::printWarnings('Similar transaction already committed!!!');
			}

			$view->message .= UiUtil::printWarnings(I18n::tr(static::MSG_ARE_YOU_SURE));
			static::renderConfirmForm($form, $view);
			$form = static::getConfirmForm();
			if ( !$form->isValid( $request->getParams() ) ) {
				$view->message = UiUtil::printErrors('invalid confirm form');
				$form = static::getInsertForm();
			}

		}

		else if ( null != $request->getParam('cancel') ) {
			$form = static::resetForm($form);
		}
		else if ( null != $request->getParam('confirm') ) {

			if ( !$form->isValid( $request->getParams() ) ) {

			}
			else {
				$values = $form->getValues();
				static::fixValuesUserId($values);

				$r = static::make($values);
				if ( empty($r) ) {
					$view->message = UiUtil::printWarnings(static::MSG_INSERT_NOT_SUCCESSFUL);
					It6_Log::notice(
						"Manual transaction '%transactionId%'('%type%') of amount '%amount%' was not committed.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'type' => static::getTransactionType($values),
							'amount' => !empty($values['amount']) ? $values['amount'] : null,
							'userId' => $values['userId']
						)
					);
				}
				else if ( is_numeric($r) ) {
					$view->message = UiUtil::printMessages(static::MSG_INSERT_SUCCESSFUL);
					It6_Log::info(
						"Manual transaction '%transactionId%'('%type%') of amount '%amount%' committed.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'transactionId' => $r,
							'type' => static::getTransactionType($values),
							'amount' => !empty($values['amount']) ? $values['amount'] : null,
							'userId' => $values['userId']
						)
					);

					$form = static::resetForm($form);
				}
				else {
					$view->message = UiUtil::printErrors($r->getMessage());
					It6_Log::warn(
						"Commiting manual transaction ('%type%') of amount '%amount%' failed.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'type' => static::getTransactionType($values),
							'amount' => !empty($values['amount']) ? $values['amount'] : null,
							'userId' => $values['userId']
						)
					);

					It6_Log::err($r);
				}
			}
		}

		$view->form = $form;
	}

	protected static function resetForm($form) {
		$ws = Zend_Registry::get('ws');
		$form->reset();
		$form->populate(array(
			'section'=>static::$ADMIN_SECTION_ID,
			'currencyId'=>$ws->Currency->getSystemId()));
		return $form;
	}

	protected static function findSimilarTransaction($values) {
		return Zend_Registry::get('ws')->Transaction->findSimilar(array(
			'typeName' => static::getTransactionType($values),
			'currencyId' => $values['currencyId'],
			'value' => $values['amount'] * static::$SIGN,
			'userId' => $values['userId']));
	}

	protected static function make($values) {
		try {
			$ret = Zend_Registry::get('ws')->Transaction->make(array(
					'typeName' => static::getTransactionType($values),
					'currencyId' => $values['currencyId'],
					'value' => $values['amount'] * static::$SIGN,
					'userId' => $values['userId'],
					'hostId' => It6_Models_Host::ID_INTERNET));

			return $ret;
		}
		catch (Exception $e) {
			return $e;
		}
	}

	protected static function getTransactionType($values) {
		return static::$TRANSACTION_TYPE_NAME;
	}

	protected static function getInsertForm() {
		$form = static::createCommonForm();
		$form = static::addInsertFormElements($form);
		$form = static::addInsertFormSubmits($form);
		return $form;
	}

	protected static function getConfirmForm() {
		$form = static::createCommonForm();
		$form = static::addConfirmFormElements($form);
		$form = static::addConfirmFormSubmits($form);
		return $form;
	}

	protected static function renderConfirmForm($form, $view) {
		$ws = Zend_Registry::get('ws');
		$values = $form->getValues();
		static::fixValuesUserId($values);
		$view->values = $values;
		if (array_key_exists('userId', $values)) {
			try {
				$view->user = Zend_Registry::get('ws')->User->getById($values['userId']);
			}
			catch (Exception $e) {
				$view->message = UiUtil::printErrors('Invalid user');
				return false;
			}
		}
		if ( array_key_exists('currencyId', $values) ) {
			$view->currency = Zend_Registry::get('ws')->Currency->getById($values['currencyId']);
		}
	}

	protected static function createCommonForm() {
		$form = new It6_Models_DecoratedForm_Table();
		$form = static::setFormBasicProperties($form);
		$form = static::addSectionElement($form);
		return $form;
	}

	protected static function setFormBasicProperties($form) {
		return $form->setName(static::$FORM_NAME)
			->setMethod(static::$FORM_METHOD)
			->setAttrib("cssClass", static::$FORM_CSS_CLASS);
	}

	protected static function addSectionElement($form) {
		return $form->addElement(
			$form->createElement('hidden', 'section')
					->setValue(static::$ADMIN_SECTION_ID));
	}

	protected static function addInsertFormElements($form) {
		$ws = Zend_Registry::get('ws');
//var_dump($form);
		$amount = $form->createElement('text', 'amount')
							->setLabel(i18n::tr('amount'))
							->setRequired(true);

		$amount = static::addAmountValidators($amount);

		$form->addElement($amount);

		if ( false == static::$OMIT_CURRENCY ) {

			$currency = It6_ArrayWrapper::toAssocArray(
				$ws->Currency->getAllColumns(array('currencyId', 'name')) ,'currencyId','%name%');

			$currencyId = $form->createElement('select', 'currencyId')
							->setMultiOptions($currency)
							->setLabel(i18n::tr('currency code'))
							->setValue($ws->Currency->getSystemId())
							->setRequired(true);

			$form->addElement($currencyId);
		}

		$userHandle = $form->createElement('text', 'userHandle')
						->setLabel(i18n::tr('user_handle'))
						->addFilter( new It6_Filter_NineDigitHandle() )
						->addValidator( new It6_Validate_NineDigitHandle() )
						->setRequired(true);

		$form->addElement($userHandle);

		return $form;
	}

	protected static function addConfirmFormElements($form) {
		$form->addElement($form->createElement('hidden', 'amount'));
		if ( false == static::$OMIT_CURRENCY ) {
			$form->addElement($form->createElement('hidden', 'currencyId'));
		}
		$form->addElement($form->createElement('hidden', 'userHandle'));
		return $form;
	}

	protected static function addConfirmFormSubmits($form) {
		$form->addElement(
				'submit', 'confirm', array('label' => i18n::tr(static::MSG_CONFIRM_TRANSACTION)));
		$form->addElement(
				'submit', 'cancel', array('label' => i18n::tr(static::MSG_CANCEL_TRANSACTION)));

		return $form;
	}

	protected static function addInsertFormSubmits($form) {
		return $form->addElement(
				'submit', 'insert', array('label' => i18n::tr(static::MSG_INSERT_TRANSACTION)));
	}

	protected static function getType() {
		$ws = Zend_Registry::get('ws');

		if (isset($_REQUEST["transactionTypeName"]))
			$transactionTypeName = $_REQUEST["transactionTypeName"];
		elseif ( is_array(static::$TRANSACTION_TYPE_NAME) )
			$transactionTypeName = current(array_keys(static::$TRANSACTION_TYPE_NAME));
		else
			$transactionTypeName = static::$TRANSACTION_TYPE_NAME;

		return $ws->TransactionType->getByName($transactionTypeName);
	}

	protected static function addAmountValidators($amount) {
		$ws = Zend_Registry::get('ws');

		$type = static::getType();

		if ( 1 == static::$SIGN ) {
			if ( null != $type->highLimit )
				$amount->addValidator(new Zend_Validate_LessThan(array('max' => $type->highLimit)));

			if ( null != $type->lowLimit )
				$amount->addValidator(new Zend_Validate_GreaterThan(array('min' => $type->lowLimit)));
		}
		else {
			if ( null != $type->lowLimit )
				$amount->addValidator(new Zend_Validate_LessThan(array('max' => -$type->lowLimit)));

			if ( null != $type->highLimit )
				$amount->addValidator(new Zend_Validate_GreaterThan(array('min' => -$type->highLimit)));
		}

		return $amount;
	}
}
