<?php
class Models_Form_AffiliateBanner extends It6_Models_DecoratedForm_Table {

	public function __construct($sectionId) {
		parent::__construct();
		$this->setName('affiliateBannerForm');

		if ($sectionId == 383) {

			$affiliateBannerId = $this->createElement('hidden', 'affiliateBannerId');
			$this->addElement($affiliateBannerId);

			$submit = $this
				->createElement('hidden', 'submit')
				->setValue(true);
			$this->addElement($submit);

		} else {

			$file = $this
				->createElement('file','fileName')
				->setLabel(i18n::tr('File'))
				->setDestination(ROOT . 'web/www/affiliate/banners/')
				->setRequired(true);
			$this->addElement($file);

		}

		$title = $this
			->createElement('text', 'imageTitle')
			->setLabel(i18n::tr('article_title'))
			->setRequired(true);
		$this->addElement($title);

		$alt = $this
			->createElement('text', 'alt')
			->setLabel('ALT');
		$this->addElement($alt);

		$targetEl = $this
			->createElement('select', 'target')
			->setLabel(i18n::tr('type_open'))
			->addMultiOptions(array('_self' => i18n::tr('Same Window'), '_blank' => i18n::tr('New Window')))
			->setRequired(true);
		$this->addElement($targetEl);

		$from = $this
			->createElement('text', 'validFrom')
			->setLabel(i18n::tr('valid_from'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateTime')
			->setRequired(true);
		$this->addElement($from);

		$to = $this
			->createElement('text', 'validTo')
			->setLabel(i18n::tr('valid_to'))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateTime')
			->setRequired(true);
		$this->addElement($to);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndUpload('".$sectionId."', '380&', '".$this->getName()."', 'affiliate-banner-main', '#affiliate-banner-index-form');return false;");
		$this->addElement($submit);
	}

	public function populate(array $values) {
		parent::populate($values);
	}
}