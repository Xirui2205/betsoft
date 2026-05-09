<?php

class Models_Form_PredepositsFilter extends It6_Models_DecoratedForm_Table {

	public static $STATUSES = array ("ok" => "ok","pending" => "pending","canceled" => "canceled");

	public function __construct($section) {

		$ws = Zend_Registry::get('ws');

		parent::__construct();

		$formName = 'TransactionsFilterForm';

		$this->setName($formName)
			->setMethod('get')
			->setAttrib("cssClass", "table-filter");

		$sectionE = $this->createElement('hidden', 'section');
		$sectionE->setValue($section);
		$this->addElement($sectionE);

		$userId = $this
			->createElement('text', 'userHandle')
			->setDecorators($this->getElementDecorator(true, false))
			->setLabel(i18n::tr('user_handle'));
		$this->addElement($userId);

		$hostId = $this
			->createElement('text', 'hostId')
			->setDecorators($this->getElementDecorator(false, true))
			->setLabel(i18n::tr('host-id'));
		$this->addElement($hostId);

		$types = array();
		$tmp = $ws->TransactionType->getAllWhere(
			array('lateDeposit = 1'));
		foreach ( $tmp as $type ) {
			$types[$type->transactionTypeId] = $type->name;
		}
		$typeId = $this
			->createElement('multiselect', 'typeId')
			->setDecorators($this->getElementDecorator(true, false))
			->setLabel(i18n::tr('type-id'))
			->addMultiOptions($types);
		$this->addElement($typeId);

		//this is here to allow for disabling of the filter in case the page is visited by a link specifyuing the transaction type to be displayed
		if(empty($showTypeFilter) || $showTypeFilter != 'false') {
			$types = array();
			$tmp = $ws->TransactionType->getAllWhere(
				array('lateDeposit = 1'));
			foreach ( $tmp as $type ) {
				$types[$type->transactionTypeId] = $type->name;
			}
			$typeId = $this
				->createElement('multiselect', 'typeId')
				->setDecorators($this->getElementDecorator(true, false))
				->setLabel(i18n::tr('type-id'))
				->addMultiOptions($types);
			$this->addElement($typeId);

			$nextElPreSapcer = 0;
		}
		else {
			$typeIdSubForm = new Zend_Form_SubForm();
			$typeIdSubForm
				->setElementsBelongTo('typeId')
				->setDecorators($this->getSubFormDecorator(array('no-table')));
			$this->addSubForm($typeIdSubForm, 'typeId');
			foreach($typeIds as $key => $typeId) {
				$typeId = $this
					->createElement('hidden', strval($key))
					//->setLabel(i18n::tr('type-id'))
					->setValue($typeId)
					->setDecorators($this->gethiddenDecorator(true, false));;
				$this->getSubForm('typeId')->addElement($typeId);
			}

			$nextElPreSapcer = 2;
		}




		$value = $this
			->createElement('text', 'value')
			->setDecorators($this->getElementDecorator(false, true))
			->setLabel(i18n::tr('value'));
		$this->addElement($value);

		$fromDate = $this
			->createElement('text', 'fromDate')
			->setLabel(i18n::tr('from-date'))
			->setDecorators($this->getDateTimeDecorator(true, false))
			->setAttrib('class', 'dateOnly');
		$this->addElement($fromDate);

		$toDate = $this
			->createElement('text', 'toDate')
			->setLabel(i18n::tr('to-date'))
			->setDecorators($this->getDateTimeDecorator(false, true))
			->setAttrib('class', 'dateOnly');
		$this->addElement($toDate);


		$submit = $this
			->createElement('submit', 'filter')
			->setLabel(i18n::tr('Filter'))
			->setDecorators($this->getButtonDecorator(true, true, 3));
		$this->addElement($submit);
	}
}
