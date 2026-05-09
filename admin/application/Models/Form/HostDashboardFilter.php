<?php

class Models_Form_HostDashboardFilter extends It6_Models_DecoratedForm_Table {

	public function __construct($section) {

		$ws = Zend_Registry::get('ws');

		parent::__construct();

		$formName = 'HostDashboardFilterForm';

		$this->setName($formName)
			->setMethod('get')
			->setAttrib("cssClass", "table-filter");

		$sectionE =
			$this->createElement('text', 'section')
			->setLabel(i18n::tr('branch-id TODO'));
		$sectionE->setValue($section);
		$this->addElement($sectionE);

/*
		$group1 = $this->createElement('select', 'group1');
		$group1
			->setLabel(i18n::tr('group1'))
			->setMultiOptions(static::$GROUPS);
		$this->addElement($group1);
*/

		$this->addElement('submit', 'filter', array('label' => i18n::tr('Filter')));


	}
}
