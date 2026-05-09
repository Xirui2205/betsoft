<?php

class Models_Form_EventMain extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $eventId=0, $insert = false, $seoUrlTypeId) {
		parent::__construct();
		$ws = Zend_Registry::get('ws');

		$this->setName('EventMainForm');

		$translationId = $this->createElement('hidden', 'translationId');
		$this->addElement($translationId);
/*
		$name = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('name'))
			->setRequired(true);
		$this->addElement($name);
*/
		$key = $this
			->createElement('text', 'key')
			->setLabel(i18n::tr('key'))
			->addFilter(new It6_Filter_TranslationKey())
			->addValidator(new It6_Validate_EventKey($eventId))
			->setRequired(true);
		$this->addElement($key);

		$betradarId = $this
			->createElement('text', 'betradarId')
			->setlabel(i18n::tr('betradar_id'))
			->addValidator(new Zend_Validate_Regex(array('pattern' => '/^[0-9]+(;[0-9]+)*$/')))
			->setRequired(true);
		$this->addElement($betradarId);

		$aliasFrom = $this
			->createElement('text', 'betAliasFrom')
			->setLabel(i18n::tr('alias_from'))
			->addValidator(new Zend_Validate_Digits());
		$this->addElement($aliasFrom);

		$aliasTo = $this
			->createElement('text', 'betAliasTo')
			->setLabel(i18n::tr('alias_to'))
			->addValidator(new Zend_Validate_Digits());
		$this->addElement($aliasTo);

		$validFrom = $this
			->createElement('text', 'validFromTime')
			->setLabel(i18n::tr('valid_from'))
			->addValidator(new It6_Validate_Date())
			->setAttrib('class', 'dateTime')
			->setDecorators($this->getDateTimeDecorator())
			->setRequired(true)
			->setValue('1.1.2015 00:00:00');
		$this->addElement($validFrom);

		$validTo = $this
			->createElement('text', 'validToTime')
			->setLabel(i18n::tr('valid_to'))
			->addValidator(new It6_Validate_Date())
			->setAttrib('class', 'dateTime')
			->setDecorators($this->getDateTimeDecorator())
			->setRequired(true)
			->setValue('31.12.2025 23:55:59');
		$this->addElement($validTo);

		$sport = $this
			->createElement('select', 'sportId')
			->setLabel(i18n::tr('sport'))
			->setRequired(true);
		$this->addElement($sport);
		$sports = $ws->Sport->getAllOrder(array('name'));
		Models_Utils::getSportsComboOptions($sport, $sports);

		$order = $this
			->createElement('text', 'navigationOrder')
			->setLabel(i18n::tr('order'))
			->addValidator(new It6_Validate_DigitsNonZero())
			->setRequired(!$insert);
		$this->addElement($order);
		
		$orderOffergen = $this
			->createElement('text', 'navigationOrderOffergen')
			->setLabel(i18n::tr('order_in_print'))
			->addValidator(new It6_Validate_DigitsNonZero())
			->setRequired(!$insert);
		$this->addElement($orderOffergen);
/*
		$marked = $this
			->createElement('select', 'navigationHighlight')
			->setLabel(i18n::tr('marked'))
			->addMultiOptions(array('0'=>I18n::tr('no'), '1'=>I18n::tr('yes')));
		$this->addElement($marked);
*/
		
		$regions = Models_Utils::getSelectOptions(
			$ws->Region->getAllOrder(array('name')), 'regionId');
		$region = $this
			->createElement('select', 'regionId')
			->setLabel(i18n::tr('region'))
			->addMultiOptions($regions);
		$this->addElement($region);
		
		$langs = $ws->Language->getAllActive();
		
		foreach ( $langs as $lang ) {
			$seoUrl = $this
				->createElement('text','seoUrl'.$lang['languageId'])
				->setLabel(i18n::tr($lang['name']))
				->setRequired(true)
				->addFilter(new It6_Filter_SeoUrl());
			$this->addElement($seoUrl);
		}

/*
		$separate = $this
			->createElement('select', 'navigationDelimiter')
			->setLabel(i18n::tr('separate'))
			->addMultiOptions(array('0'=>I18n::tr('no'), '1'=>I18n::tr('yes')));
		$this->addElement($separate);
*/

		$visible = $this
			->createElement('checkbox', 'navigationVisible')
			->setLabel(i18n::tr('navigation_visible'));
			//->setChecked();
		$this->addElement($visible);

		$brTimeOffset = $this->createElement('text', 'betradarTimeOffset')
			->setLabel(i18n::tr('betradar_time_offset'))
			->addValidator(new It6_Validate_Int());
		$this->addElement($brTimeOffset);

		$dependentTicketGames = $ws->Campaign->getEventDependentTicketGames(DEFAULT_LANG_ID);
		$ticketGameOptions = array();
		foreach ($dependentTicketGames as $game) {
			$ticketGameOptions[$game['gameId']] = $game['gameName'];
		}
		$ticketGames = $this
			->createElement('multiselect', 'ticketGames')
			->setLabel(i18n::tr('ticket_game'))
			->addMultiOptions($ticketGameOptions);
		$this->addElement($ticketGames);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick', "submitEventMainForm($section, $eventId)");
		$this->addElement($submit);
	}
}
