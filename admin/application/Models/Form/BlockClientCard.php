<?php

class Models_Form_BlockClientCard extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $userId = null){
		parent::__construct();

		$this->setName('BlockClientCardForm');


		$id = $this->createElement('hidden', 'userId');
		$id->setValue($userId);
		$this->addElement($id);		

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Block_client_card_number'))
			//->setAttrib('onClick',"submitUserProfileForm($section)");
			->setAttrib('onClick',"submitAndReloadGeneric('".$section."', '".$section."&', '".$this->getName()."', 'user-profile', '&submit=1','#formUsersFilter');return false;");
		$this->addElement($submit);
	}
}
