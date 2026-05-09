<?php

class ProviderRegistrationController extends Zend_Controller_Action {

	const CAPTCHA_NAME = 'providerRegistrationCaptcha';

	public function init() {
		$this->view->addHelperPath('views/helpers/', 'My_View_Helper');
		Models_BasicRender::render($this->view, $this->_request);
		Models_Ajax_Ticket::get($this->view);

		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);
		It6_GlobalCache::turnOff();
	}

	/**
	 * Creates captcha instance, generates its value, initialize view for captcha use
	 */
	private function initCaptcha() {
		$captchaImg = Models_Helpers_Captcha::create('providerRegistrationCaptcha', true);
		Models_Helpers_Captcha::initView($this->view, $captchaImg);
	}

	public function indexAction() {
		$this->view->form = new Models_Form_ProviderRegistration($this->view);
		$this->initCaptcha();
	}


	public function registerAction() {
		$form	= new Models_Form_ProviderRegistration($this->view);
		$values	= $this->getRequest()->getPost();

		if(isset($values['submit'])) {
			$formForValid = clone $form;

			$captchaImg = Models_Helpers_Captcha::create(self::CAPTCHA_NAME);
			$captcha = new Zend_Form_Element($captchaImg->getName());
			$formForValid->addElement($captcha);
			$captcha->setRequired(true);
			$captcha->addValidator($captchaImg);

			$form->isValid($values);

			if($formForValid->isValid($values)) {

				if(Models_Helpers_ProviderRegistration::processForm($values))
					$form->addError('provider_registration_failed');
				else {
					$this->view->feedbackMsg = It6_FeedbackMsg::printNotice('provider_registartion_ok');
					$form->reset();
				}
			}
			else
				$form->addError('form_not_valid');

			$this->view->captchaErrors = $formForValid->getElement(self::CAPTCHA_NAME)->getErrors();
		}
		
		$this->view->form = $form;
		$this->initCaptcha();
		$this->render('index');
	}
}
