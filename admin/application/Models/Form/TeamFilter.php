<?php

class Models_Form_TeamFilter extends It6_Models_DecoratedForm_Table {

	public function __construct() {
		parent::__construct();

		$this
			->setName('TeamFilterForm')
			->setMethod('get')
			->setAttrib('cssClass', 'table-filter');



		$teamId = $this
			->createElement('text', 'teamId')
			->setLabel(i18n::tr('Team Id'));

		$sportName = $this
			->createElement('text', 'sportName')
			->setLabel(i18n::tr('Sport Name'));

		$brId = $this
			->createElement('text', 'betradarId')
			->setLabel(i18n::tr('Betradar Id'));

		$abbrivation = $this
			->createElement('text', 'abbrivation')
			->setLabel(i18n::tr('Abbreviation'));

		$teamName = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('Team Name'));

		$fulltext = $this
			->createElement('text', 'fulltext')
			->setLabel(i18n::tr('Fulltext'));

		$filterSubForm = new Zend_Form_SubForm();
		$filterSubForm
			->setElementsBelongTo('filter')
			->setDecorators($this->getSubFormDecorator(array('no-table')))
			->addElements(array($fulltext, $teamId, $teamName, $sportName, $brId, $abbrivation));
		$this->addSubForm($filterSubForm, 'filter');


		$submit = $this
			->createElement('submit', 'filter')
			->setLabel(i18n::tr('Filter'));
		$this->addElement($submit);
	}
}
