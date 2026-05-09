<?php

class Models_Form_Team extends It6_Models_DecoratedForm_Table{

	public function __construct($section, $viewSectionId) {
		parent::__construct();

		$intValidator	= new Zend_Validate_Int();

		$this->setName('TeamForm');



		$tid = $this->createElement('hidden', 'teamId');
		$this->addElement($tid);


		$sport = $this
			->createElement('select', 'sportId')
			->setLabel(i18n::tr('Sport'))
			->setRequired(true);
		Models_Utils::getSportsComboOptions($sport, $this->ws->Sport->getAllOrder(array('name')));
		$this->addElement($sport);


		$betradarId = $this
			->createElement('text', 'betradarId', array('class'=>'short'))
			->setLabel(i18n::tr('Betradar Id'))
			->addValidator($intValidator)
			->setRequired(true);
		$this->addElement($betradarId);


		$name = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('Team name'))
			->setRequired(true);
		$this->addElement($name);


		$abbr = $this
			->createElement('text', 'shortName')
			->setLabel(i18n::tr('Team abbreviation'))
			->setRequired(true);
		$this->addElement($abbr);

		$submit = $this
			->createElement('submit', 'submit')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$section."', '".$viewSectionId."&', '".$this->getName()."', 'inner', '&submit=1','#formUsersFilter');return false;");
			//->setAttrib('onClick',"submitGeneric('".$section."', 'TeamForm', 'content','&submit=1');return false;");
		$this->addElement($submit);
	}
}
