<?php

class Models_Form_BranchUser extends It6_Models_DecoratedForm_Table {

	public function __construct(){
		parent::__construct();



		$handle = $this
			->createElement('text', $conv['handle'])
			->setlabel(i18n::tr('Handle'))
			->addValidator('alnum')
			->setRequired(true);
		$this->addElement($name);


		$region = $this
			->createElement('select', $conv['branch_location_id'])
			->setLabel(i18n::tr('Region'))
			->addMultiOptions($this->ws->BranchLocation->getAllAsAssociativeArray());
		$this->addElement($region);
	}
}
