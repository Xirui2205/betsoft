<?php

class Models_Helpers_Captcha {

	/**
	 * Always generate new CAPTCHA after previous CAPTCHA validation, or previous data will be lost
	 * and validation will probably fail.
	 * @param string $name Name of the CAPTCHA, should be page specific and contain only safe characters.
	 * @param boolean $generate TUE to generate new CAPTCHA value
	 * @return It6_Captcha_Image
	 */
	public static function create($name, $generate = false) {
		$captcha = new It6_Captcha_Image(array(
			'name' => $name,
			'wordlen' => 4,
			'timeout' => 600,
			'font' => '../www/fonts/Arial_Bold.ttf',
			'imgDir' => '../www/captcha/',
			'imgUrl' => '/captcha/',
			'width' => 157,
			'height' => 35,
			'fontSize' => 18,
			'imgAlt' => 'captcha_alt',
			'lineNoiseLevel' => 2,
			'dotNoiseLevel' => 30,
			'expiration' => 600,
			'gcFreq' => 1,
		));
		if ($generate)
			$captcha->generate();
		return $captcha;
	}

	/**
	 * Initializes view variables for use with ajax/captcha.phtml view script
	 * which will add image and inputs.
	 * @param Zend_View $view
	 * @param It6_Captcha_Image $captcha
	 */
	public static function initView(&$view, $captcha) {
		$view->captchaName = $captcha->getName();
		$view->captchaId = $captcha->getId();
		$view->captchaWordlen = $captcha->getWordlen();
		$view->captchaImgUrl = '/captcha.php?id=' . $captcha->getId();
		$view->captchaImgAlt = $captcha->getImgAlt();
		$view->captchaImgWidth = $captcha->getWidth();
		$view->captchaImgHeight = $captcha->getHeight();
	}

}
