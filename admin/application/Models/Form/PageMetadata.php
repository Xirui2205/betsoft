<?php
class Models_Form_PageMetadata extends It6_Models_DecoratedForm_Table {

	public function __construct($sectionId) {
		parent::__construct();
		$this->setName('pageMetadataForm');

		$controllerId = $this->createElement('hidden', 'controllerId');
		$this->addElement($controllerId);

		$langId = $this->createElement('hidden', 'langId');
		$this->addElement($langId);

		$name = $this
			->createElement('text', 'title')
			->setLabel(i18n::tr('title'))
			->setRequired(true);
		$this->addElement($name);

		$description = $this
			->createElement('textarea', 'description')
			->setOptions(array('cols' => '40', 'rows' => '4'))
			->setLabel(i18n::tr('description'));
		$this->addElement($description);

		$keywords = $this
			->createElement('textarea', 'keywords')
			->setOptions(array('cols' => '40', 'rows' => '4'))
			->setLabel(i18n::tr('keywords'));
		$this->addElement($keywords);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$sectionId."', '310&', '".$this->getName()."', 'page-metadata-main', '&submit=1','#page-metadata-index-form');return false;");
		$this->addElement($submit);
	}

	public function populate(array $values) {
		parent::populate($values);
	}
}