<?php

// article insert / update form
class Models_Form_Document extends It6_Models_DecoratedForm_Table {

	//private $auxBetsOptions = array();
	
	public function __construct($sectionId) {
		parent::__construct();
		$this->setName('documentForm');

		$id = $this->createElement('hidden', 'documentId');
		$this->addElement($id);

		$name = $this
				->createElement('text', 'name')
				->setLabel(i18n::tr('name'))
				->addValidator('stringLength', array('min' => 2, 'max' => 100, 'encoding'=>'UTF-8'))
				->setRequired(true);
		$this->addElement($name);

		$url = $this
				->createElement('text', 'url')
				->setLabel(i18n::tr('url'))
				->addValidator('stringLength', array('min' => 2, 'max' => 100, 'encoding'=>'UTF-8'))
				->setAttrib("size", "100")
				->setRequired(true);
		$this->addElement($url);
		
		$submit = $this
				->createElement('button', 'button')
				->setLabel(i18n::tr('Submit'))
				->setAttrib('onClick', "submitAndReloadGeneric('" . $sectionId . "', '395&', '" . $this->getName() . "', 'document', '&submit=1');return false;");
		$this->addElement($submit);
	}
	
}