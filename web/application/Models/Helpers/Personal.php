<?php


class Models_Helpers_Personal{

	private static function buildForm(Models_Helpers_User $user) {
		$form = new Zend_Form;

		$form->setAction('./')->setMethod('post');

		$gender = new Zend_Form_Element_Radio('gender');
		$gender->setRegisterInArrayValidator(false);
		$gender->setRequired(true);
		$form->addElement($gender);

		$phone = new Zend_Form_Element_Text('telefon');
		$form->addElement($phone);
		$phone->addValidator(new It6_Validate_PhoneNumber());
		$phone->setRequired(false);

		$passOld = new Zend_Form_Element_Password('pass_old');
		$passOld->setAutoInsertNotEmptyValidator(false);
		$passOld->setAllowEmpty(false);
		$passOld->addValidator( new It6_Validate_Password($user->nick, $user->password, !empty($_POST['pass_new']) || !empty($_POST['pass_again'])) );
		$form->addElement($passOld);

		if(!empty($_POST['pass_old']) || !empty($_POST['pass_again']))
			$required = true;
		else
			$required = false;
		$pass = new Zend_Form_Element_Password('pass_new');
		$pass->setAutoInsertNotEmptyValidator(false);
		$pass->setAllowEmpty(false);
		$pass->addValidator( new It6_Validate_Password($user->nick, null, $required));
		$form->addElement($pass);

		if(!empty($_POST['pass_old']) || !empty($_POST['pass']))
			$required = true;
		else
			$required = false;
		$pass2 = new Zend_Form_Element_Password('pass_again');
		$pass2->setAllowEmpty(false);
		$pass2->addValidator( new It6_Validate_Password($user->nick, null, $required) );
		$form->addElement($pass2);



		$accountPrefix = new Zend_Form_Element_Text('account_prefix');
		$form->addElement($accountPrefix);
		$accountPrefix->addValidator(new It6_Validate_BankAccountPrefix());

		$accountNumber = new Zend_Form_Element_Text('account_number');
		$form->addElement($accountNumber);
		$accountNumber->addValidator(new It6_Validate_BankAccountNumber('account_number', 'bank_id','account_prefix'));

		$bankId = new Zend_Form_Element_Text('bank_id');
		$form->addElement($bankId);
		$bankId->addValidator(new It6_Validate_BankId('bank_id','account_number'));


		$newsletter = new Zend_Form_Element_Checkbox('news');
		$newsletter->setCheckedValue('yes');
		$form->addElement($newsletter);

		$form->addElement(new It6_Form_Element_Hash('myAccountPersonalToken'));

		return $form;
	}


	/**
	 * Kontrola formulare
	 * @param object $view view object
	 * @return array
	 */

	public static function validate($view){

		try{
			$ws = Zend_Registry::get('ws');
			$user = new Models_Helpers_User();
			$user->readDb(Zend_Registry::get('user_id'));
			$form = Models_Helpers_Personal::buildForm($user);
			if ($form->isValid($_POST)) {
				$view->result = 'edit-user-failed';
				$view->success = false;
				self::form2user($form, $user);
				if (true === $user->updateDb()) {
					$err = false;
					$accountNumber = $form->getValue('account_number');
					if(!empty($accountNumber)) {
						$data = array(
							'userId'		=> $user->id,
							'accountPrefix'	=> $form->getValue('account_prefix'),
							'accountNumber'	=> $accountNumber,
							'bankId'		=> $form->getValue('bank_id')
						);

						if(!$ws->UserBankAccount->insert($data))
							$err = true;
					}
					$pass = $form->getValue('pass_new');
					if (!empty($pass)) {
						try {
							if (!$ws->User->setPassword(true, $user->id, $pass, false))
								$err = true;
						}
						catch (Exception $e) {
							$err = true;
						}
					}
					if (!$err) {
						$view->result = 'edit-user-ok';
						$view->success = true;
					}
				}
			} else {
				$view->success = false;
				$view->result = 'form-not-valid';
			}

			$view->error = $form->getErrors();
			$view->values = $form->getValues();
			$user->birthDate = It6_Date::fromDbAsDate($user->birthDate);
			$view->values['user'] = $user;
			$view->values['bank'] = $ws->UserBankAccount->getByUserId($user->id);


		}  catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
	}

	public static function read($view) {
		$user = new Models_Helpers_User();
		$user->readDb(Zend_Registry::get('user_id'));
		$user->birthDate = It6_Date::fromDbAsDate($user->birthDate);
		$form = Models_Helpers_Personal::buildForm($user);
		Models_Helpers_Personal::user2form($user, $form);
		$view->values = $form->getValues();
		$view->values['user'] = $user;
		$view->values['bank'] = Zend_Registry::get('ws')->UserBankAccount->getByUserId($user->id);
	}

	public static function user2form(Models_Helpers_User $user, Zend_Form $form) {
		$form->getElement('gender')->setValue('f' == $user->gender ? 'zena' : 'muz');
		$form->getElement('telefon')->setValue($user->phone);
		$form->getElement('news')->setChecked(1 == $user->newsletter);
	}

	public static function form2user(Zend_Form $form, Models_Helpers_User $user) {
		$user->gender = ('zena' == $form->getValue('gender') ? 'f' : 'm');
		$user->phone = $form->getValue('telefon');
		$user->password = null;
		$user->newsletter = $form->getElement('news')->isChecked() ? 1 : 0;
	}

}
