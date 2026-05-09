<?php

class Models_Form_DataSettings extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $paramId = null, $mode = null) {
		require_once('controllers/ParametersSettingsController.php');
		parent::__construct();

		$this->setName('DataMainForm');

		$nazev = $this
			->createElement('text', 'nazev')
			->setLabel(i18n::tr('nazev'))
			->setRequired(true);
		$this->addElement($nazev);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$section."', '354&', '".$this->getName()."', 'data-settings', '&submit=1','#formDataFilter');return false;");
		$this->addElement($submit);
	}
}