<?php

class Models_Form_TypeDetail extends It6_Models_DecoratedForm_Table {

	public function __construct($sectionId) {
		parent::__construct();
		$this->setName('typeDetailForm');

		$offerCategories = $this->ws->OfferCategory->getAll();
		$offerCategories = It6_ArrayWrapper::toNativeArray($offerCategories);
		$offerCategories = It6_ArrayWrapper::toMultioption($offerCategories, 'offerCategoryId', 'name');

		$orderTypes = $this->ws->Type->getOrderTypes();
		$orderTypes = It6_ArrayWrapper::toAssocArray($orderTypes, 'id', '%name%', array('' => '--none--'));

		$id = $this->createElement('hidden', 'typeId');
		$this->addElement($id);

		$typeAliasId = $this
			->createElement('text', 'typeAliasId')
			->setLabel(i18n::tr('type_alais_id'))
			->setRequired(true)
			->addValidator('digits');
		$this->addElement($typeAliasId);

		$name = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('name'))
			->addValidator( new It6_Validate_BetTypeName() )
			->setRequired(true);
		$this->addElement($name);

		$order = $this
			->createElement('text', 'order')
			->setLabel(i18n::tr('order'))
			->addValidator('digits');
		$this->addElement($order);

		$visible= $this
			->createElement('select', 'visible')
			->setLabel(i18n::tr('visible'))
			->addMultiOptions(array('0'=>i18n::tr('no'), '1'=>i18n::tr('yes')));
		$this->addElement($visible);

		$offerCategory = $this
			->createElement('select', 'offerCategoryId')
			->setLabel(i18n::tr('offer_category'))
			->setMultiOptions($offerCategories);
		$this->addElement($offerCategory);

		$orderTypeId = $this
			->createElement('select', 'orderTypeId')
			->setLabel(i18n::tr('order_type'))
			->setMultiOptions($orderTypes);
		if (!empty($_REQUEST['groupMaster'])) $orderTypeId->setRequired(true);
		$this->addElement($orderTypeId);

		$typeAliasGroup = $this
			->createElement('text', 'typeAliasGroup')
			->setLabel(i18n::tr('type_alais_group'))
			->addValidator( new It6_Validate_BetTypeName() );
		$this->addElement($typeAliasGroup);

		$groupMaster= $this
			->createElement('select', 'groupMaster')
			->setLabel(i18n::tr('group_master'))
			->addMultiOptions(array('0'=>i18n::tr('no'), '1'=>i18n::tr('yes')));
		$this->addElement($groupMaster);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$sectionId."', '138&', '".$this->getName()."', 'type-main', '&submit=1','#formTypeFilter');return false;");
		$this->addElement($submit);
	}
}