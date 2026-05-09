<?php

class Models_Form_SoundsSettings extends It6_Models_DecoratedForm_Table {

	public function __construct() {
		parent::__construct();			

			$file = $this
				->createElement('file','fileName1')
				->setLabel(i18n::tr('Level1').": ".Webservice_Parameter::getGlobalParameter('confirmation.sound.level1'))
				->setDestination(ROOT . 'admin/www/audio/')
     			->addValidator('Size', false, array('max' => '5242880'))
                ->addValidator('Extension', false, array('ogg'));
			$this->addElement($file);

			$file = $this
				->createElement('file','fileName2')
				->setLabel(i18n::tr('Level2').": ".Webservice_Parameter::getGlobalParameter('confirmation.sound.level2'))
				->setDestination(ROOT . 'admin/www/audio/')
     			->addValidator('Size', false, array('max' => '5242880'))
                ->addValidator('Extension', false, array('ogg'));
			$this->addElement($file);

			$file = $this
				->createElement('file','fileName3')
				->setLabel(i18n::tr('Level3').": ".Webservice_Parameter::getGlobalParameter('confirmation.sound.level3'))
				->setDestination(ROOT . 'admin/www/audio/')
     			->addValidator('Size', false, array('max' => '5242880'))
                ->addValidator('Extension', false, array('ogg'));
			$this->addElement($file);

			$submit = $this
				->createElement('submit', 'save')
				->setLabel(i18n::tr('Submit'));
			$this->addElement($submit);
	}
}