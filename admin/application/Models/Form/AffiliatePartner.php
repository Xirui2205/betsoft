<?php
class Models_Form_AffiliatePartner extends It6_Models_DecoratedForm_Table {

	public function __construct($sectionId) {
		parent::__construct();
		$this->setName('affiliatePartnerForm');

		if ($sectionId == 377) {
			$affiliatePartnerId = $this->createElement('hidden', 'affiliatePartnerId');
			$this->addElement($affiliatePartnerId);
		}

		$name = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('name'))
			->setRequired(true);
		$this->addElement($name);

		$url = $this
			->createElement('text', 'url')
			->setLabel(i18n::tr('url_address'))
			->setRequired(true);
		$this->addElement($url);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$sectionId."', '374&', '".$this->getName()."', 'affiliate-partner-main', '&submit=1','#affiliate-partner-index-form');return false;");
		$this->addElement($submit);
	}

	public function populate(array $values) {
		parent::populate($values);
	}
}