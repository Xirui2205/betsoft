<?php

class Models_Form_BranchOpeningHours extends It6_Models_DecoratedForm_Table {

	public function __construct($section) {
		parent::__construct();

		$this->setName('BranchOpeningHoursForm');


		$monday = $this
			->createElement('text', '0')
			->setLabel(i18n::tr('monday'))
			->setAttrib('class', 'wide')
			->addValidator(new It6_Validate_OpeningHours());
		$this->addElement($monday);

		$tuesday = $this
			->createElement('text', '1')
			->setLabel(i18n::tr('tuesday'))
			->setAttrib('class', 'wide')
			->addValidator(new It6_Validate_OpeningHours());
		$this->addElement($tuesday);

		$wednesday = $this
			->createElement('text', '2')
			->setLabel(i18n::tr('wednesday'))
			->setAttrib('class', 'wide')
			->addValidator(new It6_Validate_OpeningHours());
		$this->addElement($wednesday);

		$thursday = $this
			->createElement('text', '3')
			->setLabel(i18n::tr('thursday'))
			->setAttrib('class', 'wide')
			->addValidator(new It6_Validate_OpeningHours());
		$this->addElement($thursday);

		$friday = $this
			->createElement('text', '4')
			->setLabel(i18n::tr('friday'))
			->setAttrib('class', 'wide')
			->addValidator(new It6_Validate_OpeningHours());
		$this->addElement($friday);

		$saturday = $this
			->createElement('text', '5')
			->setLabel(i18n::tr('saturday'))
			->setAttrib('class', 'wide')
			->addValidator(new It6_Validate_OpeningHours());
		$this->addElement($saturday);

		$sunday = $this
			->createElement('text', '6')
			->setLabel(i18n::tr('sunday'))
			->setAttrib('class', 'wide')
			->addValidator(new It6_Validate_OpeningHours());
		$this->addElement($sunday);


		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick', "submitBranchOpeningHoursForm($section)");
		$this->addElement($submit);
	}
}
