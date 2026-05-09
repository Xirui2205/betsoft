<?php

class Models_Form_Banner extends It6_Models_DecoratedForm_Table{

	public function __construct($sectionId, $locations = array(), $bannerId) {
		parent::__construct();

		$this
			->setName('bannerForm')
			->setAction('?section='.$sectionId)
			->setMethod('post');


		$languages = Zend_Registry::get('ws')->Language->getAllActive();
		$languages = Models_Utils::getSelectOptions($languages, 'languageId', 'name');


		$controllerEl = $this->createElement('hidden', 'controllerId');
		$this->addElement($controllerEl);

		$bannerEl = $this->createElement('hidden', 'bannerId');
		$this->addElement($bannerEl);

		$locationEl = $this
			->createElement('select', 'locationId')
			->setLabel(i18n::tr('Location'))
			->addMultiOptions($locations);
		$this->addElement($locationEl);

		$languageEl = $this
			->createElement('select', 'languageId')
			->setLabel(i18n::tr('Language'))
			->addMultiOptions($languages);
		$this->addElement($languageEl);

		$titleEl = $this
			->createElement('text', 'title')
			->setLabel(i18n::tr('Title:'))
			->setRequired(true);
		$this->addElement($titleEl);

		$urlEl = $this
			->createElement('text', 'url')
			->setLabel(i18n::tr('Url:'))
			->setRequired(true);
		$this->addElement($urlEl);

		$imageEl = $this
			->createElement('text', 'imageName')
			->setlabel(i18n::tr('Image:'))
			->setDecorators($this->getGaleryDecorator(true, true, 0, 0, 'imageName'.$bannerId))
			->setAttrib('id', 'imageName'.$bannerId)
			->setRequired(true);
		$this->addElement($imageEl);

		$imageEl = $this
			->createElement('text', 'order')
			->setlabel(i18n::tr('Order:'))
			->setRequired(true);
		$this->addElement($imageEl);

		$targetEl = $this
			->createElement('select', 'target')
			->setLabel(i18n::tr('Open in'))
			->addMultiOptions(array('_self' => i18n::tr('Same Window'), '_blank' => i18n::tr('New Window')))
			->setRequired(true);
		$this->addElement($targetEl);

		$dateValidFrom = $this
			->createElement('text', 'validFrom')
			->setLabel(i18n::tr('Valid From:'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateTime');
		$this->addElement($dateValidFrom);


		$dateValidTo = $this
			->createElement('text', 'validTo')
			->setLabel(i18n::tr('Valid To:'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateTime');
		$this->addElement($dateValidTo);

		$textEl = $this
			->createElement('textarea', 'text')
			->setLabel(i18n::tr('Text:'));
		$this->addElement($textEl);

		$submitEl = $this
			->createElement('submit', 'submit')
			->setLabel(i18n::tr('Submit'))
			->setOrder(1000);
		$this->addElement($submitEl);
	}
}
