<?php

class Models_Form_TypeEvent extends It6_Models_DecoratedForm_Table {
	const ISDEFAULT_FIELD_PREFIX = 'isDefault_';
	const ISBINDED_FIELD_PREFIX = 'isBinded_';
	const ORDER_FIELD_PREFIX = 'order_';
	
	
	public function __construct($sectionId, $eventId) {
		parent::__construct();
		$this->setName('typeEventForm');
		
		
		$types = $this->ws->Type->getAll();
		$types = It6_ArrayWrapper::toNativeArray($types);
		
		
		$id = $this
			->createElement('hidden', 'eventId')
			->setValue($eventId);
		$this->addElement($id);

		foreach($types as $type) {
			$isBinded = $this
				->createElement('checkbox', self::ISBINDED_FIELD_PREFIX . $type['typeId'])
				->setLabel($type['name'].':</label></td><td><label>is_binded')
				->setDecorators($this->getElementDecorator(true, false));
			$this->addElement($isBinded);

			$isDefault = $this
				->createElement('checkbox', self::ISDEFAULT_FIELD_PREFIX . $type['typeId'])
				->setLabel('is_default')
				->setDecorators($this->getElementDecorator(false, false));
			$this->addElement($isDefault);
			
			$order= $this
				->createElement('text', self::ORDER_FIELD_PREFIX . $type['typeId'])
				->setLabel('order')
				->setDecorators($this->getElementDecorator(false, true));
			$this->addElement($order);
		}


		$submitSport = $this
			->createElement('button', 'updateSport')
			->setLabel(i18n::tr('update_entire_sport'))
			->setDecorators($this->getButtonDecorator(true, false))
			->setAttrib(
				'onClick',
				"submitAndReloadGeneric('".$sectionId."', '290&', '".$this->getName()."', 'type-event', '&submit=sport','#formTypeFilter');return false;"
			);
		$this->addElement($submitSport);
		
		$submitEvent = $this
			->createElement('button', 'updateEvent')
			->setLabel(i18n::tr('update_event'))
			->setDecorators($this->getButtonDecorator(false, true, 5))
			->setAttrib(
				'onClick',
				"submitAndReloadGeneric('".$sectionId."', '290&', '".$this->getName()."', 'type-event', '&submit=event','#formTypeFilter');return false;"
			);
		$this->addElement($submitEvent);
	}
	
	
	
	public function populate(array $values) {
		foreach($values as $typeId => $data) {
			$this->getElement(self::ISBINDED_FIELD_PREFIX . $data['typeId'])->setValue(1);

			if(!empty($data['isDefault'])) {
				$this->getElement(self::ISDEFAULT_FIELD_PREFIX . $data['typeId'])->setValue(1);
			}
			if(isset($data['order'])) {
				$this->getElement(self::ORDER_FIELD_PREFIX . $data['typeId'])->setValue($data['order']);
			}
			
		}
	}
}
