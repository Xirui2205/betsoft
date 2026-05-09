<?php
class Models_Form_AffiliatePartnerAssign extends It6_Models_DecoratedForm_Table {

	public function __construct($sectionId) {
		parent::__construct();
		$this->setName('affiliatePartnerForm');

		$affiliatePartnerId = $this->createElement('hidden', 'affiliatePartnerId');
		$this->addElement($affiliatePartnerId);

		$bannersRaw = $this->ws->AffiliateBanner->getAll();
		$bannersRaw = It6_ArrayWrapper::toNativeArray($bannersRaw);
		$bannersRaw = It6_ArrayWrapper::toMultioption($bannersRaw, 'affiliateBannerId', 'fileName');
		$banners = array(null => I18n::tr('none')) + $bannersRaw;

		$banner = $this
			->createElement('select', 'affiliateBannerId')
			->setLabel('Banner')
			->addMultiOptions($banners)
			->setRequired(true);
		$this->addElement($banner);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(I18n::tr('Submit'))
			->setAttrib('onClick',"submitGeneric('".$sectionId."', '".$this->getName()."', 'affiliate-partner-banner', '&submit=1');return false;");
		$this->addElement($submit);
	}

	public function populate(array $values) {
		parent::populate($values);
	}
}