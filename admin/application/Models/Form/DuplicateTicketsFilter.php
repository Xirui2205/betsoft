<?php

class Models_Form_DuplicateTicketsFilter extends It6_Models_DecoratedForm_Table {

	public function __construct($section) {
		parent::__construct();

		$this
			->setName('DuplicateTicketsFilterForm')
			->setMethod('get')
			->setAttrib('cssClass', 'table-filter');

		$sectionE = $this
			->createElement('hidden', 'section')
			->setValue($section);
		$this->addElement($sectionE);

		$minDuplicity = $this
			->createElement('text', 'minDuplicity')
			->setLabel(i18n::tr('Min duplicity'));
		$this->addElement($minDuplicity);


		$submit = $this
			->createElement('submit', 'filter')
			->setLabel(i18n::tr('Filter'));
		$this->addElement($submit);


	}
}
