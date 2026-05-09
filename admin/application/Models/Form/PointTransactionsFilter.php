<?php

class Models_Form_PointTransactionsFilter extends It6_Models_DecoratedForm_Table {	

	public function __construct($section) {
		parent::__construct();

		$this
			->setName('TransactionsFilterForm')
			->setMethod('get');

		$sectionE = $this
			->createElement('hidden', 'section')
			->setValue($section);
		$this->addElement($sectionE);

		$userHandle = $this
			->createElement('text', 'userHandle')
			->setLabel(i18n::tr('user_handle'));
		$this->addElement($userHandle);

		$types = It6_ArrayWrapper::toAssocArray(
			$this->ws->PointsTransactionType->getAll(),
			'pointsTransactionTypeId', '%name%');
		array_unshift($types,array('' => '---'));
		$typeId = $this
			->createElement('select', 'typeId')
			->setLabel(i18n::tr('type-id'))
			->addMultiOptions($types);
		$this->addElement($typeId);

		$value = $this
			->createElement('text', 'value')
			->setLabel(i18n::tr('value'));
		$this->addElement($value);

		$fromDate = $this
			->createElement('text', 'fromDate')
			->setLabel(i18n::tr('from-date'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateOnly');
		$this->addElement($fromDate);

		$toDate = $this
			->createElement('text', 'toDate')
			->setLabel(i18n::tr('to-date'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateOnly');
		$this->addElement($toDate);

		$submit = $this
			->createElement('submit', 'filter')
			->setLabel(i18n::tr('Filter'));
		$this->addElement($submit);
	}
}
