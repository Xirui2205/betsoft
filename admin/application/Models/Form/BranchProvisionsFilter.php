<?php

class Models_Form_BranchProvisionsFilter extends It6_Models_DecoratedForm_Table {
	
	public function __construct($section) {

		$ws = Zend_Registry::get('ws');

		parent::__construct();

		$formName = 'BranchProvisionsFilterForm';

		$this->setName($formName)
			->setMethod('get')
			->setAttrib("cssClass", "table-filter");

		$this->addElement(
			$this->createElement('hidden', 'section')
					->setValue($section));


		$fromDate = $this->createElement('text', 'fromDate');
		$fromDate->setLabel(i18n::tr('from-date'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateOnly');
		$this->addElement($fromDate);

		$toDate = $this->createElement('text', 'toDate');
		$toDate->setLabel(i18n::tr('to-date'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateOnly');
		$this->addElement($toDate);


		$this->addElement('submit', 'filter', array('label' => i18n::tr('Filter')));
		$this->addElement('submit', 'CSV', array('label' => i18n::tr('Export')));

	}
}
