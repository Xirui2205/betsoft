<?php

class Models_Form_ClientCard extends It6_Models_DecoratedForm_Table {

	public function __construct($sectionId) {
		parent::__construct();
		$this->setName('clientCardForm');

		$exts = array(new It6_WsExtension_Client_Columns('columns', array('branchId', 'name')));
		$exts[] = new It6_WsExtension_Client_Filter('non-internet', array('?' => array('typeId' => '1'), 'OP' => '<>'));
		$branchesRaw = $this->ws->ext($exts)->Branch->getAll();
		$branchesRaw = It6_ArrayWrapper::toNativeArray($branchesRaw);
		$branchesRaw = It6_ArrayWrapper::toMultioption($branchesRaw, 'branchId', 'name');
		$branches = array(0 => 'none') + $branchesRaw;

		$id = $this->createElement('hidden', 'clientCardId');
		$this->addElement($id);

		$clientCardId = $this
			->createElement('text', 'clientCardIdCopy')
			->setLabel(i18n::tr('client_card_id'))
			->setAttrib('disabled', 'disabled');
		$this->addElement($clientCardId);

		$generated = $this
			->createElement('text', 'generated')
			->setLabel(i18n::tr('generated'))
			->setAttrib('disabled', 'disabled');
		$this->addElement($generated);

		$adminId = $this
			->createElement('text', 'adminId')
			->setLabel(i18n::tr('admin_id'))
			->setAttrib('disabled', 'disabled')
			->addValidator('digits');
		$this->addElement($adminId);

		$branch = $this
			->createElement('select', 'branchId')
			->setLabel(i18n::tr('branch'))
			->addMultiOptions($branches);
		$this->addElement($branch);	

		$set = $this
			->createElement('text', 'set')
			->setLabel(i18n::tr('set'))
			->addValidator('digits');
		$this->addElement($set);

		$used = $this
			->createElement('text', 'used')
			->setLabel(i18n::tr('used'))
			->setAttrib('disabled', 'disabled');
		$this->addElement($used);

		$blocked = $this
			->createElement('text', 'blocked')
			->setLabel(i18n::tr('blocked'))
			->setAttrib('disabled', 'disabled');
		$this->addElement($blocked);

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