<?php


class It6_Form_Element_Captcha extends Zend_Form_Element_Captcha {

	public function __construct($captchaName) {
		$captcha = Models_Helpers_Captcha::create($captchaName);

		$this->setCaptcha($captcha);
		parent::__construct($captchaName);
	}



	public function render(Zend_View_Interface $view = null) {
		$captcha = $this->getCaptcha();
		$captcha->setName($this->getFullyQualifiedName());

		$decorators	= $this->getDecorators();
		$decorator	= new It6_Form_Decorator_Captcha(array('captcha' => $captcha));
		array_unshift($decorators, $decorator);

		$decorator = $captcha->getDecorator();
		$this->setValue($this->getCaptcha()->generate());

		return Zend_Form_Element::render($view);
	}
}
