<?php

abstract class It6_Cron_Job_Email_ControllerAbstract {

	const DEFAULT_LAYOUT = 'email-default';

	protected $params;
	public $emailLang;
	protected $_layout;

	public function __construct(array $params) {
		$this->params = $params;
	}

	public function prepareEmail() {
		//TODO do budoucna asi z konfigu
		$this->emailLang = 'cs';
		$db = Zend_Registry::get('db');
		$lang = It6_Models_Language::getDataByIso($this->emailLang, $db);
		$translate = new It6_Translate_Admin($lang['id'], $db);
		Zend_Registry::set('translate', $translate);
	}

	public function getBodyText() {
		return $this->processTemplate('bodyText');
	}

	public function getSubject() {
		return SUBJECT_PREFIX.$this->processTemplate('subject');
	}

	public function getBccField() {
		return $this->processTemplate('bccField');
	}

	public function getCcField() {
		return $this->processTemplate('ccField');
	}

	public function getToField() {
		return $this->processTemplate('toField');
	}

	public function getBodyHtml($layoutScript=null) {
		if(file_exists(ROOT.It6_Cron_Job_Email::EMAIL_VIEW_PATH.'/' .$this->getType().'/bodyHtml.phtml')) {
			if($layoutScript == null)
				$layoutScript = self::DEFAULT_LAYOUT;

			$layout = new Zend_Layout();
			$layout->setLayoutPath(ROOT . It6_Cron_Job_Email::EMAIL_LAYOUT_PATH);
			$layout->setLayout($layoutScript);
			$layout->content = $this->processTemplate('bodyHtml');

			return $layout->render();
		}
		else
			return null;
	}

	protected function getType() {
		return get_called_class();
	}

/*
	protected function setLayout($layout) {
		$this->_layout = new Zend_Layout();
		$this->_layout->setLayoutPath(ROOT . It6_Cron_Job_Email::EMAIL_LAYOUT_PATH);
		$this->_layout->setLayout($layout);
		return $this->_layout;
	}

	protected function layout() {
		return $this->_layout;
	}
*/

	protected function processTemplate($__template) {
		$__fileName = ROOT . It6_Cron_Job_Email::EMAIL_VIEW_PATH . '/' .
				$this->getType().'/' . $__template . '.phtml';

		if ( file_exists($__fileName) ) {
			try {
				ob_start();
				require($__fileName);
				$ret = ob_get_contents();
				ob_end_clean();
			}
			catch (Exception $e) {
				ob_end_clean();
				throw $e;
			}
			return $ret;
		}
		else {
			return '';
		}
	}

	public function formatRate($value) {
		return It6_View_Helper_FormatRate::formatRateStatic($value);
	}

	public function formatCurrency($value, $currency = '', $precision = 2, $type='html') {
		return It6_View_Helper_FormatCurrency::formatCurrencyStatic($value, $currency, $precision, $type);
	}

	public function escape($str) {
		return htmlspecialchars($str);
	}

	public function getAttachment() {
		return false;
	}
}