<?php

class Models_Form_TeamPlayer extends It6_Models_DecoratedForm_Table{

	public function __construct($section, $viewSectionId) {
		parent::__construct();

		$this->setName('TeamPlayerForm');

		$tid = $this->createElement('hidden', 'teamId');
		$this->addElement($tid);

		$tpid = $this->createElement('hidden', 'teamPlayerId');
		$this->addElement($tpid);
		
		$name = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('Player name'))
			->setRequired(true);
		$this->addElement($name);

		$submit = $this
			->createElement('submit', 'submit')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$section."', '".$viewSectionId."&', '".$this->getName()."', 'inner', '&submit=1','#formUsersFilter');return false;");
			//->setAttrib('onClick',"submitGeneric('".$section."', 'TeamForm', 'content','&submit=1');return false;");
		$this->addElement($submit);
	}
}
