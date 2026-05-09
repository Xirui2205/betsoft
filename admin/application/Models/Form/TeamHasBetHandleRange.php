<?php

class Models_Form_TeamHasBetHandleRange extends It6_Models_DecoratedForm_Table{

	public function __construct($section) {
		parent::__construct();

		$intValidator = new Zend_Validate_Int();

		$this->setName('TeamHasBetHandleRange');



		/*$id = $this->createElement('hidden', 'betHandleRangeId')
			->setValue($this->betHandleRangeId);*/

		$teamBySport = $this->ws->Team->getAllWhere(array('sport_id = ?' => $this->sportId));

/*
		$team = $this->createElement('select', 'teamId')
				->setLabel(i18n::tr('Team'))
				->setRequired(true);

		$team->addMultiOption('',I18n::tr('Choose a team...'));

		Models_Utils::createComboOptions($team, $teamBySport, array('id' => 'teamId', 'name' => 'name'));
*/

		for($i = 0; $i<=$this->rangeUpperLimit-$this->rangeLowerLimit; $i++) {
			//$team = new Zend_Form_Element_Select('teamId'.$i, array('label'=>I18n::tr('Team'),'size' => 1));

			$team = $this->createElement('select', 'teamId_'.$i)
				->setLabel(i18n::tr('Team').' '.($this->rangeLowerLimit+$i))
				->setRequired(true)
				->addMultiOption('',I18n::tr('Choose a team...'));
			Models_Utils::createComboOptions($team, $teamBySport, array('id' => 'teamId', 'name' => 'name'));
			$this->addElement($team);
		}


		$submit = $this
			->createElement('submit', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitGeneric('".$section."&superb=1', '$thisName', 'inner', '&submit=1')");
		$this->addElement($submit);
	}
}
