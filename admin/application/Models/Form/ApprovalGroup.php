<?php

class Models_Form_ApprovalGroup extends It6_Models_DecoratedForm_Table {

	public function __construct($section,$id) {
		parent::__construct();

		$this->setName('ApprovalGroupForm');



		$gid = $this
			->createElement('hidden', 'approvalGroupId')
			->setValue($id);
		$this->addElement($gid);


		$name = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('Group name'))
			->setRequired(true);
		$this->addElement($name);


		$submit = $this->createElement('button', 'button');
		$submit->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitGeneric('$section','".$this->getName()."','content','&submit=1')");
		$this->addElement($submit);
	}
}
