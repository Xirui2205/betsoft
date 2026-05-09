<?php

class Models_Form_BranchFundingFilter extends It6_Models_DecoratedForm_Table {

	public function __construct($section) {
		parent::__construct();

		$initialsRaw	= Zend_Registry::get('ws')->Branch->getTownInitials();
		$initialsRaw	= It6_Models_Form_Util::getSelectOptions($initialsRaw, 'initial', 'initial');
		$initials[0]	= '--'.'Select'.'--';
		$initials		= array_merge($initials, $initialsRaw);

		$active = $this
			->createElement('checkbox', 'active')
			->setLabel(i18n::tr('branch_active'));
		$this->addElement($active);

		$inNeedEl = $this
			->createElement('checkbox', 'inNeedOfFunds')
			->setLabel(i18n::tr('in_need_of_funds'));
		$this->addElement($inNeedEl);

		$notInNeedEl = $this
			->createElement('checkbox', 'notInNeedOfFunds')
			->setLabel(i18n::tr('not_in_need_of_funds'));
		$this->addElement($notInNeedEl);

		$towns = $this
			->createElement('select', 'townInitial')
			->setLabel(i18n::tr('Town'))
			->addMultioptions($initials);
		$this->addElement($towns);
		
		$branchHandle = $this
			->createElement('text', 'branchHandle')
			->setLabel(i18n::tr('branch_handle'));
		$this->addElement($branchHandle);

		$submit = $this
			->createElement('submit', 'filter')
			->setLabel(i18n::tr('Show'));
		$this->addElement($submit);

	}
}
