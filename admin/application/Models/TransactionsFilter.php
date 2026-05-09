<?php

class Models_Form_TransactionsFilter extends It6_Models_DecoratedForm_Table {

	public static $STATUSES = array ("ok" => "ok","pending" => "pending","canceled" => "canceled");

	public function __construct($section) {
		parent::__construct();

		$this
			->setName('TransactionsFilterForm')
			->setMethod('get');

		$sectionE = $this
			->createElement('hidden', 'section')
			->setValue($section);
		$this->addElement($sectionE);

		$userId = $this
			->createElement('text', 'userId')
			->setLabel(i18n::tr('user-id'));
		$this->addElement($userId);

		$types = It6_ArrayWrapper::toAssocArray(
			$this->ws->TransactionType->getAll(),
			'transactionTypeId', '%name%');
		$typeId = $this
			->createElement('select', 'typeId')
			->setLabel(i18n::tr('type-id'))
			->addMultiOptions($types);
		$this->addElement($typeId);

		$value = $this
			->createElement('text', 'value')
			->setLabel(i18n::tr('value'));
		$this->addElement($value);

		$status = $this
			->createElement('multiselect', 'status')
			->setLabel(i18n::tr('status'))
			->addMultiOptions(self::$STATUSES);
		$this->addElement($status);

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
