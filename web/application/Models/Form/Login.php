<?php

class Models_Form_Login extends It6_Models_DecoratedForm_Table {


	public function __construct() {
		parent::__construct();
		$this
			->setName('loginPageForm')
			->setAttrib('class', 'form');

		$redirectTo = $this
			->createElement('hidden', 'redirectTo');
		$this->addElement($redirectTo);

		$nick = $this
			->createElement('text', 'pageNick')
			->setLabel('reg_email')
			->setAttrib('onClick', 'if(typeof emptyNick == "undefined"){this.value="";emptyNick=1;}')
			->setAttrib('class', 'input110 margin2')
			->setRequired(true);
		$this->addElement($nick);
		
		$pass = $this
			->createElement('password', 'pagePass')
			->setLabel('password')
			->setRequired(true)
			->setAttrib('class', 'input110 margin2');
		$this->addElement($pass);
		
		$submit = $this
			->createElement('submit', 'submit')
			->setAttrib('class', 'btn btn-big')
			->setLabel('login');
		$this->addElement($submit);
	}
}
