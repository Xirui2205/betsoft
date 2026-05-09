<?php

class Models_Form_BranchStem extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $stemId=0, $insert = false) {
		parent::__construct();
		$extensions = array();

		$this->setName('StemForm');
		
		$id = $this
			->createElement('hidden', 'stemId');
		$this->addElement($id);

		$name = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('stem-name'))
			->setRequired(!$insert);
		$this->addElement($name);
		
		$adminFilter = array(
				'role2 IN (?)' => array('branch-technician', 'branch-admin'),
		);
		$admins = $this->ws->ext($extensions)->Admin->getAllWhereOrder($adminFilter, array('surname'));
		$admins = It6_ArrayWrapper::toAssocArray($admins, 'adminId', '%lastName% '.'%firstName%',array());
		
		$admin = $this
		->createElement('select', 'adminId')
		->setLabel(i18n::tr('stem-admin'))
		->addMultiOptions($admins);
		$this->addElement($admin);
		
		$desc = $this
		->createElement('text', 'desc')
		->setLabel(i18n::tr('stem-desc'));
		$this->addElement($desc);
		
		$file = $this
		->createElement('text', 'fileName')
		->setLabel(i18n::tr('stem-filename'));
		$this->addElement($file);
		
		$check = $this
		->createElement('checkbox', 'sendMail')
		->setLabel(i18n::tr('stem-sendmail'));
		$this->addElement($check);
		
		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick', "submitAndReloadGeneric('".$section."', '".$stemId."', '".$this->getName()."', 'branch-stem', '&submit=1', '#formStemFilter'); return false;");
		$this->addElement($submit);
	}
}
