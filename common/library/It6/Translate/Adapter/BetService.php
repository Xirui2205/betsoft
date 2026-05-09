<?php

/**
 * This adapter wraps old Translate class, class instance is read from Zend_Registry.
 * @author Stastny
 */
class It6_Translate_Adapter_BetService extends Zend_Translate_Adapter {

private $translator = null;

public function __construct($options = array()) {
	parent::__construct($options);
	if ($options instanceof Zend_Config)
		$options = $options->toArray();
	$registryKey = (empty($options['registryKey']) ? 'translate' : $options['registryKey']);
	if (!Zend_Registry::isRegistered($registryKey))
		throw new Exception('No translator registered in Zend_Registry with key "' . $registryKey . '"');
	$this->translator = Zend_Registry::get($registryKey);
}

public function toString () {
	return 'BetService';
}

protected function _loadTranslationData($data, $locale, array $options = array()) {
	//throw new Exception('Adapter does not support loading additional data');
	return array();
}

public function translate ($messageId, $locale = null) {
	if (is_array($messageId)) // plural
		$messageId = $messageId[0]; // no plural support
	return $this->translator->trans($messageId);
}

public function isTranslated($messageId, $original = false, $locale = null) {
	return $this->translator->hasTranslation($messageId);
}

} // class It6_Translate_Adapter
