<?php

class Models_Form_CancelTransaction extends Models_Form_ManualTransactionAbstract {

	protected static $FORM_NAME = 'CancelTransaction';
	protected static $ADMIN_SECTION_ID = 267;

	const MSG_INSERT_SUCCESSFUL = 'cancel-successful';
	const MSG_ARE_YOU_SURE = 'Are you sure to cancel the transaction?';
	const MSG_CONFIRM_TRANSACTION = 'Confirm-transaction-cancelation';
	const MSG_CANCEL_TRANSACTION = 'do-not-cancel-transaction';
	const MSG_INSERT_TRANSACTION = 'Cancel-transaction';
	
	//protected static $TRANSACTION_TYPE_ID = array('8' => 'Online card', '10' => 'Bank transfer');

	protected static function getTransactionType($values) {
		return $values['transactionTypeId'];
	}

	protected static function addInsertFormElements($form) {

		$transactionId = $form->createElement('text', 'transactionId')
						->setLabel(i18n::tr('transactionId'))
						->setRequired(true);

		$form->addElement($transactionId);

		return $form;
	}
	
	protected static function renderConfirmForm($form, $view) {
		$ws = Zend_Registry::get('ws');

		$values = $form->getValues();
		 
		$ret = $view->transaction = $ws->Transaction->getById($values['transactionId']);
		
		if ( false === $ret ) {
			$view->message = UiUtil::printErrors('Invalid transaction');
			return false;
		}


		$view->currency = $ws->Currency->getById($view->transaction->currencyId);
		$view->type = $ws->TransactionType->getById($view->transaction->typeId);
		
	}
	
	protected static function addConfirmFormElements($form) {
		$form->addElement($form->createElement('hidden', 'transactionId'));
		return $form;
	}
	
	protected static function findSimilarTransaction($values) {
		return false;
	}
	
	protected static function make($values) {
		try {
			$ret = Zend_Registry::get('ws')->Transaction->cancelOkTransaction(
					$values['transactionId']
				);

			return $ret;
		}
		catch (Exception $e) {
			return $e;
		}
	}
}
