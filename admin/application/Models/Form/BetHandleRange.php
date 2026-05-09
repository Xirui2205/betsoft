<?php

class Models_Form_BetHandleRange extends It6_Models_DecoratedForm_Table {

	public function __construct($section) {
		parent::__construct();

		$intValidator	= new Zend_Validate_Int();

		$this->setName('BetHandleRange');


/*
		$tid = $this
			->createElement('hidden', 'teamId')
			->setValue($this->teamId);
		$this->addElement($tid);
*/

		$sport = $this
			->createElement('select', 'sportId')
			->setLabel(i18n::tr('Sport'))
			->setAttrib('onChange', "ld('".$section."&superb=1', 'inner', { sportId : this.value, betHandleRangeId : ".$this->betHandleRangeId."} )")
			->setRequired(true);
		Models_Utils::getSportsComboOptions($sport,$this->ws->Sport->getAll());
		$this->addElement($sport);

		if ($this->sportId)
			$sport->setValue($this->sportId);


		$event = $this
			->createElement('select', 'eventId')
			->setLabel(i18n::tr('Event'))
			->setRequired(true);
			//->addMultiOption('', i18n::tr('Choose sport ..'));
		$this->addElement($event);


		if ($this->sportId) {
			$where = array('sport_id=?'=>$this->sportId);
			Models_Utils::createComboOptions($event,$this->ws->Event->getAllWhere($where), array('id' => 'eventId', 'name' => 'name'));
		}


		$from = $this
			->createElement('text', 'from')
			->setLabel(i18n::tr('From'))
			->setRequired(true)
			->addValidator($intValidator);
		$this->addElement($from);


		$to = $this
			->createElement('text', 'to')
			->setLabel(i18n::tr('To'))
			->setRequired(true)
			->addValidator($intValidator);
		$this->addElement($to);


		$submit = $this->createElement('button', 'button');
		$submit->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick', "submitGeneric('".$section."&superb=1', '".$this->getName()."', 'inner', '&submit=1')");
		$this->addElement($submit);
	}
}
