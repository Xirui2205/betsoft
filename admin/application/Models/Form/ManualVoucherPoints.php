<?php

class Models_Form_ManualVoucherPoints extends Models_Form_ManualTransactionAbstract {

	protected static $FORM_NAME = 'VoucherPoints';
	protected static $ADMIN_SECTION_ID = 309;

	protected static function make($values) {
		try {
			$ret = Zend_Registry::get('ws')->Voucher->useVoucher($values['handle'],$values['userId']);
			return $ret;
		}
		catch (Exception $e) {
			return $e;
		}
	}

	protected static function findSimilarTransaction($values) {
		return false;
	}

	
	protected static function addInsertFormElements($form) {
		$ws = Zend_Registry::get('ws');
		
		$handle = $form->createElement('text', 'handle')
		->setLabel(i18n::tr('voucher_handle'))
		->setRequired(true);
	
		$form->addElement($handle);
	
		$userHandle = $form->createElement('text', 'userHandle')
		->setLabel(i18n::tr('user_handle'))
		->addFilter( new It6_Filter_NineDigitHandle() )
		->addValidator( new It6_Validate_NineDigitHandle() )
		->setRequired(true);
	
		$form->addElement($userHandle);
	
		return $form;
	}
	
	protected static function renderConfirmForm($form, $view) {
		parent::renderConfirmForm($form, $view);
		$ws = Zend_Registry::get('ws');
		$values = $form->getValues();
		static::fixValuesUserId($values);
		$view->values = $values;
		if (array_key_exists('userId', $values)) {
			try {
				$ws->Voucher->validate($values['handle'],$values['userId']);
				$view->voucher = $ws->Voucher->getByHandle($values['handle']);
			}
			catch (Exception $e) {
				$view->message = UiUtil::printErrors('Not valid voucher number: '.$e->getMessage());
				return false;
			}
		}
	}
	
	protected static function addConfirmFormElements($form) {
		$form->addElement($form->createElement('hidden', 'handle'));
		$form->addElement($form->createElement('hidden', 'userHandle'));
		return $form;
	}
	
	protected static function getTransactionType($values) {
		return "Voucher";
	}

}
