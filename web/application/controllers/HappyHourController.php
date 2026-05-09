<?php

class HappyHourController extends Zend_Controller_Action {
	
	const CAPTCHA_NAME = 'happyHourCaptcha';
	
	public function init() {
		$this->view->addHelperPath('views/helpers/', 'My_View_Helper');
		Models_BasicRender::render($this->view, $this->_request);
		Models_Ajax_Ticket::get($this->view);
		It6_GlobalCache::turnOff();
	}
	
	public function visitAction() {
		$ws = Zend_Registry::get('ws');
		
		$this->view->showForm = $ws->Campaign->validate(
			'HappyHourVisit',
			'get',
			array('userId'  => Zend_Registry::get('user_id'))
		);
		
		if ( !$this->view->showForm ) {
			$this->view->feedbackMsg = It6_FeedbackMsg::printWarning('happy_hour_is_closed');
			return;
		}
		
		$form = new Models_Form_HappyHourVisitConfirm($this->view);
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
		
				try {
					$tmp = $ws->Campaign->putOn(
						'HappyHourVisit',
						'get',
						array('userId'  => Zend_Registry::get('user_id'))
					);
					if ( !$tmp ) {
						$this->view->feedbackMsg = It6_FeedbackMsg::printWarning('happy_hour_is_closed');
					}
					else {
						$this->view->feedbackMsg = It6_FeedbackMsg::printNotice('happy_hour_visit_success');
						It6_Log::info(
							'Happy hour visit succesfuly added.',
							It6_Log::TAG_USER_OPERATION,
							array(
								'userId' => Zend_Registry::get('user_id')
							)
						);
					}
					$this->view->showForm = false;
				}
				catch (Exception $e) {
					$this->view->feedbackMsg = It6_FeedbackMsg::printError('happy_hour_visit_failed');
					$form->reset();
					It6_Log::notice(
						'Happy hour visit failed: %message%.',
						It6_Log::TAG_USER_OPERATION,
						array(
							'message' => $e->getMessage(),
							'userId' => Zend_Registry::get('user_id')
						)
					);
					It6_Log::err($e);
				}
				
			}
			else
				$form->addError('form_not_valid');
		
			$this->view->captchaErrors = $formForValid->getElement(self::CAPTCHA_NAME)->getErrors();
			
		}
		
		
		$this->view->form = $form;
		
		$captchaImg = Models_Helpers_Captcha::create(static::CAPTCHA_NAME, true);
		Models_Helpers_Captcha::initView($this->view, $captchaImg);
		
		Models_Helpers_Panels::leftCol($this->view);
		Models_Helpers_Panels::rightCol($this->view);
	}
	
}
