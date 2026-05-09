<?php

class Models_Form_ManualDeposit extends Models_Form_ManualTransactionAbstract {

	protected static $FORM_NAME = 'Deposit';
	protected static $ADMIN_SECTION_ID = 226;

	protected static $TRANSACTION_TYPE_NAME = 
		array('user.deposit.card' => 'Online card', 'user.deposit.bank' => 'Bank transfer', 'user.bonus.entry' => "User bonus entry");

	protected static function getTransactionType($values) {
		return $values['transactionTypeName'];
	}
	
	protected static function addInsertFormElements($form) {
		$form = parent::addInsertFormElements($form);

		$depositType = $form->createElement('select', 'transactionTypeName')
						->setMultiOptions(static::$TRANSACTION_TYPE_NAME)
						->setLabel(i18n::tr('deposit type'))
						->setRequired(true);

		$form->addElement($depositType);

		return $form;
	}

	protected static function addConfirmFormElements($form) {
		$form = parent::addConfirmFormElements($form);

		$form->addElement($form->createElement('hidden', 'transactionTypeName'));

		return $form;
	}

	protected static function renderConfirmForm($form, $view) {
		parent::renderConfirmForm($form, $view);
		$view->type = static::$TRANSACTION_TYPE_NAME[$view->values['transactionTypeName']];
	}
}
