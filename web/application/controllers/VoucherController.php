<?php

class VoucherController extends Zend_Controller_Action {
	
	const CAPTCHA_NAME = 'voucherCaptcha';
	const MAX_VOUCHER_HANDLE_NOT_FOUNDS = 5;
	const MAX_VOUCHER_HANDLE_NOT_FOUNDS_TIME_WINDOW = 7200;
	
	public function init() {
		$this->view->addHelperPath('views/helpers/', 'My_View_Helper');
		Models_BasicRender::render($this->view, $this->_request);
		Models_Ajax_Ticket::get($this->view);
		It6_GlobalCache::turnOff();
	}
	
	public function useAction() {
		$this->view->noRight = true;
		$this->view->leftMenuItems = Models_Helpers_Panels::getPersonalMenuItems();
		
		$key = 'voucher_handle_not_found_'.Zend_Registry::get('user_id');
		$value = It6_GlobalCache::getKey($key);
		
		if ( empty($value) )
			$value = 0;
		
		if ( $value > static::MAX_VOUCHER_HANDLE_NOT_FOUNDS ) {
			$this->view->blocked = true;
			$this->view->feedbackMsg = It6_FeedbackMsg::printWarning('voucher_screen_is_blocked');
		}
		else {
			$this->view->blocked = false;			
			$ws = Zend_Registry::get('ws');
			
			$form = new Models_Form_VoucherUse($this->view);
			$values	= $this->getRequest()->getPost();
			
			if(isset($values['submit'])) {
				$formForValid = clone $form;
			/*
				$captchaImg = Models_Helpers_Captcha::create(self::CAPTCHA_NAME);
				$captcha = new Zend_Form_Element($captchaImg->getName());
				//$formForValid->addElement($captcha);
				$captcha->setRequired(true);
				$captcha->addValidator($captchaImg);
			*/
				$form->isValid($values);
			
				if($formForValid->isValid($values)) {
			
					try {
						$ws->Voucher->validate(
							$values['handle'],
							Zend_Registry::get('user_id')
						);
						$valid = true;
					}
					catch (Exception $e) {
						$valid = false;
						$this->view->feedbackMsg = It6_FeedbackMsg::printError($e->getMessage());
						It6_Log::notice(
							'Voucher use was not successful: %message%.',
							It6_Log::TAG_USER_OPERATION,
							array(
								'message' => $e->getMessage(),
								'handle'  => $values['handle'],
								'userId'  => Zend_Registry::get('user_id')
							)
						);
						It6_GlobalCache::setKey($key,$value + 1,static::MAX_VOUCHER_HANDLE_NOT_FOUNDS_TIME_WINDOW);
					}
					
					if ( $valid ) {
					
						try {
							$ws->Voucher->useVoucher(
								$values['handle'],
								Zend_Registry::get('user_id')
							);
							
							$this->view->feedbackMsg = It6_FeedbackMsg::printNotice('voucher_used_successfuly');
							It6_Log::info(
								'Voucher used successfuly',
								It6_Log::TAG_USER_OPERATION,
								array(
									'handle'  => $values['handle'],
									'userId'  => Zend_Registry::get('user_id')
								)
							);
							$form->reset();
							
						}
						catch (Exception $e) {
							$this->view->feedbackMsg = It6_FeedbackMsg::printError('voucher_use_failed');
							$form->reset();
							It6_Log::warn(
								'Voucher use failed: %message%.',
								It6_Log::TAG_USER_OPERATION,
								array(
									'message' => $e->getMessage(),
									'handle'  => $values['handle'],
									'userId'  => Zend_Registry::get('user_id')
								)
							);
							It6_Log::err($e);
						}
					}
					
				}
				else
					$form->addError('form_not_valid');
			
				//$this->view->captchaErrors = $formForValid->getElement(self::CAPTCHA_NAME)->getErrors();
				
			}
			
			
			$this->view->form = $form;
		
		/*
			$captchaImg = Models_Helpers_Captcha::create(static::CAPTCHA_NAME, true);
			Models_Helpers_Captcha::initView($this->view, $captchaImg);
			*/
		}
	}
	
}
