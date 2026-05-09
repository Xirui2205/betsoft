<?php

class Models_Form_StatisticsFilter extends It6_Models_DecoratedForm_Table {

	const MULTISELECT_LIMIT = 50;
	
	private static $GROUP_FILTER = array(
			'userId' =>
				array('entity' => 'User', 'id' => 'userId', 'name' => 'username'),
			'sportId' =>
				array('entity' => 'Sport', 'id' => 'sportId', 'name' => 'name'),
			'eventId' =>
				array('entity' => 'Event', 'id' => 'eventId', 'name' => 'name'),
			'typeId' =>
				array('entity' => 'BetType', 'id' => 'betTypeId', 'name' => 'name'),
			'locationId' =>
				array('entity' => 'BranchLocation', 'id' => 'id', 'name' => 'name'),
			'branchId' =>
				array('entity' => 'Branch', 'id' => 'branchId', 'name' => 'name'),
			'hostId' =>
				array('entity' => 'Host', 'id' => 'hostId', 'name' => 'name'),
			'type' =>
				array('entity' => 'Type', 'id' => '', 'name' => ''),

		);

	private static $GROUPS = array(
		'' => '---',
		'userId' => 'user',
		'sportId' => 'sport',
		'eventId' => 'event',
		'typeId' => 'market type',
		'locationId' => 'location',
		'branchId' => 'branch',
		'hostId' => 'host',
		'type' => 'ticket type');

	protected static $CALC_TYPE = array(
		'1' => 'cashFlow',
		'0' => 'payout'
		);

	public $group1s = null;
	public $group2s = null;
		
	public function __construct($section) {

		$ws = Zend_Registry::get('ws');

		parent::__construct();

		$formName = 'StatisticsFilterForm';

		$this->setName($formName)
			->setMethod('get')
			->setAttrib("cssClass", "table-filter");

		$sectionE = $this->createElement('hidden', 'section');
		$sectionE->setValue($section);
		$this->addElement($sectionE);

		$group1 = $this->createElement('select', 'group1');
		$group1
			->setLabel(i18n::tr('group1'))
			->setMultiOptions(static::$GROUPS)
			->setDecorators($this->getElementDecorator(true,false));
		$this->addElement($group1);

		$group2 = $this->createElement('select', 'group2');
		$group2
			->setLabel(i18n::tr('group2'))
			->setMultiOptions(static::$GROUPS)
			->setDecorators($this->getElementDecorator(false,true,0,2));
		$this->addElement($group2);

		$limit1 = $this->createElement('text', 'limit1');
		$limit1
			->setLabel(i18n::tr('limit1'))
			->setDecorators($this->getElementDecorator(true,false));
		$this->addElement($limit1);

		$limit2 = $this->createElement('text', 'limit2');
		$limit2
			->setLabel(i18n::tr('limit2'))
			->setDecorators($this->getElementDecorator(false,false));
		$this->addElement($limit2);
		
		$calcType = $this->createElement('select', 'calcType');
		$calcType
			->setLabel(i18n::tr('calcType'))
			->setMultiOptions(static::$CALC_TYPE)
			->setDecorators($this->getElementDecorator(false,true));
		$this->addElement($calcType);


		$fromDate = $this->createElement('text', 'fromDate');
		$fromDate->setLabel(i18n::tr('from-date'))
			->setDecorators($this->getDateTimeDecorator(true, false))
			->setAttrib('class', 'dateTime');
		$this->addElement($fromDate);

		$toDate = $this->createElement('text', 'toDate');
		$toDate->setLabel(i18n::tr('to-date'))
			->setDecorators($this->getDateTimeDecorator(false, true,0,2))
			->setAttrib('class', 'dateTime');
		$this->addElement($toDate);

		if ( !empty($_REQUEST['group1']) ) {
			$gf = static::$GROUP_FILTER[$_REQUEST['group1']];
			if ( $_REQUEST['group1'] == 'type')
				$group1s = array('simple' => 'simple','kombi' => 'kombi', 'maxikombi' => 'maxikombi');
			else {

				$group1s = array();
				foreach ( $ws->$gf['entity']->getAll() as $item ) {
					$group1s[$item->$gf['id']] = $item->$gf['name'];
				}
			}

			$ed = empty($_REQUEST['group2'])
				? $this->getElementDecorator(true,true,0,4)
				: $this->getElementDecorator(true,false);

			if ( count($group1s) < self::MULTISELECT_LIMIT ) {
				$group1Filter = $this->createElement('multiselect', 'group1filter')
					->addMultioptions($group1s)
					->setDecorators($ed)
					->setLabel(i18n::tr($gf['entity'] . '-filter'));

			}
			else {
				$group1Filter = $this->createElement('text', 'group1filter')
					->setDecorators($ed)
					->setLabel(i18n::tr($gf['entity'] . '-filter'));
				$this->group1s = $group1s;
			}
			$this->addElement($group1Filter);
		}

		if ( !empty($_REQUEST['group2']) ) {
			$gf = static::$GROUP_FILTER[$_REQUEST['group2']];

			if ( $_REQUEST['group2'] == 'type')
				$group2s = array('simple' => 'simple','kombi' => 'kombi', 'maxikombi' => 'maxikombi');
			else {
				$group2s = array();
				foreach ( $ws->$gf['entity']->getAll() as $item ) {
					$group2s[$item->$gf['id']] = $item->$gf['name'];
				}
			}

			if ( count($group2s) < self::MULTISELECT_LIMIT ) {
				$group2Filter = $this->createElement('multiselect', 'group2filter')
					->setDecorators($this->getElementDecorator(false,true,0,2))
					->addMultioptions($group2s)
					->setLabel(i18n::tr($gf['entity'] . '-filter'));
				$this->addElement($group2Filter);
			}
			else {
				$group2Filter = $this->createElement('text', 'group2filter')
					->setDecorators($this->getElementDecorator(false,true,0,2))
					->setLabel(i18n::tr($gf['entity'] . '-filter'));
				$this->addElement($group2Filter);
				$this->group2s = $group2s;
			}
		}

		$submitFilter = $this->createElement('submit', 'filter')
			->setLabel(i18n::tr('Filter'))
			->setDecorators($this->getElementDecorator(true,false,0,2));
		$this->addElement($submitFilter);

		$submitCSV = $this->createElement('submit', 'CSV')
			->setLabel(i18n::tr('Export'))
			->setDecorators($this->getElementDecorator(false,true));
		$this->addElement($submitCSV);

	}
}
