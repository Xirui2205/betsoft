<?php

class Models_Form_ClientCardInsert extends It6_Models_DecoratedForm_Table {

	public function __construct($sectionId) {
		parent::__construct();
		$this->setName('clientCardFormInsert');

		/* $exts = array(new It6_WsExtension_Client_Columns('columns', array('branchId', 'name')));
		$exts[] = new It6_WsExtension_Client_Filter('non-internet', array('?' => array('typeId' => '1'), 'OP' => '<>'));
		$branchesRaw = $this->ws->ext($exts)->Branch->getAll();
		$branchesRaw = It6_ArrayWrapper::toNativeArray($branchesRaw);
		$branchesRaw = It6_ArrayWrapper::toMultioption($branchesRaw, 'branchId', 'name');
		$branches = array(0 => 'none') + $branchesRaw; */

		$clientCardId = $this
			->createElement('text', 'clientCardId')
			->addValidator(new It6_Validate_DigitsNonZero())
			->setRequired(true)
			->setLabel(i18n::tr('client_card_id'));
		$this->addElement($clientCardId);

		/* $branch = $this
			->createElement('select', 'branchId')
			->setLabel(i18n::tr('branch'))
			->addMultiOptions($branches);
		$this->addElement($branch); */	

		$set = $this
			->createElement('select', 'set')
			->setLabel(i18n::tr('set'))
			->addMultiOptions(array(1 => '1', 2 => '2'));
		$this->addElement($set);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$sectionId."', '334&', '".$this->getName()."', 'client-card-main', '&submit=1','#client-card-index-form');return false;");
		$this->addElement($submit);
	}

	public function populate(array $values) {
		if (!empty($values['used'])) $this->getElement('branchId')->setAttrib('disabled', 'disabled');
		parent::populate($values);
	}
}