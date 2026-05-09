<?php
class Models_Form_SettingLimits extends It6_Models_DecoratedForm_Table {

	const NAME = 'settingLimits';
	const AMOUNT = 'limitAmount';
	const DATE_TO = 'timeTo';
	const DATE_FROM = 'timeFrom';

	public function __construct() {
		parent::__construct();

		$this->setName(self::NAME)->setAction('./');

		$this->addElement(self::createToken($this->getView(), self::NAME . 'Token'));

		$amount = $this
			->createElement('text', self::AMOUNT, array('maxlength'=> 9, 'style' => 'text-align:right'))
			->setlabel('limit_amount')
			->addValidator(new It6_Validate_Int(array('min' => Webservice_Parameter::getGlobalParameter('user.settingLimits.min'), 'max' => Webservice_Parameter::getGlobalParameter('user.settingLimits.max'))))
			->addFilter(new It6_Filter_Int())
			->setValue(It6_Validate_Int::formatInteger(0))
			->setAttrib('id', self::AMOUNT . self::NAME)
			->setRequired(true);
		$this->addElement($amount);

		/*$time_from = $this
			->createElement('text', self::DATE_FROM, array('maxlength'=> 9, 'style' => 'text-align:right'))
			->setLabel('time_from')
			->addValidator(new It6_Validate_Date())
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateTime')
			->setRequired(true);
		$this->addElement($time_from);*/

		$today = It6_Date::now();
		$month = date("d.m.Y h:m:s", strtotime("+30 day"));

		$time_to = $this
			->createElement('text', self::DATE_TO, array('maxlength'=> 9, 'style' => 'text-align:right'))
			->setLabel('time_to')
			->addValidator(new It6_Validate_Date())
			->addValidator(new It6_Validate_DateGreaterThanDate($today))
			->addValidator(new It6_Validate_DateLowerThanDate($month))
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateTime')
			->setRequired(true);
		$this->addElement($time_to);

		$this->addElement('submit', 'submit', array('label' => 'save','class'=>'btn btn-big'));
		$this->getElement('submit')->setAttrib('id', 'submit_'.self::NAME);
	}

	public function populate(array $values) {
		if (!empty($values)) {
			$this->getElement('limitAmount')->setAttrib('disabled', 'disabled');
			//$this->getElement('timeFrom')->setAttrib('disabled', 'disabled');
			$this->getElement('timeTo')->setAttrib('disabled', 'disabled');
			$this->getElement('submit')->setAttrib('disabled', 'disabled');
		}
		parent::populate($values);
	}

	public static function createToken($view, $name) {
		$token = new It6_Form_Element_Hash($name);
		if (isset($view)) {
			$view->$name = $token->getHash();
		}
		return $token;
	}
}