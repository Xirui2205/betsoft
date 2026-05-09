<?php

abstract class It6_Translate {

abstract public function trans($key, $section = null, $dictionary = null, $wrapUnfoundTag=true);

/**
 * After translation calls It6_MessageFormat::format(translatedResource, $params)
 * @param string $key
 * @param mixed $params @see It6_MessageFormat
 * @param string $action
 * @param string $controller
 */
public function transParam($key, $params, $section = null, $dictionary = null, $wrapUnfoundTag=true) {
	$translated = $this->trans($key, $section, $dictionary, $wrapUnfoundTag);
	return It6_MessageFormat::format($translated, $params);
}

abstract public function hasTranslation($key, $section = null, $dictionary = null);

abstract public function getCurrentLangId();

} // class It6_Translate
