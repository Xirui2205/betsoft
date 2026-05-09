<?php

class It6_Translate_Ws extends It6_Translate {

public function trans($key, $section = null, $dictionary = null, $wrapUnfoundTag=true) {
	//TODO: implement (using It6_Models_Translator?)
	return $key;
}

public function transParam($key, $param, $section = null, $dictionary = null, $wrapUnfoundTag=true) {
	//TODO: implement (using It6_Models_Translator?)
	return $key;
}

public function hasTranslation($key, $section = null, $dictionary = null) {
	return true;
}

public function getCurrentLangId() {
	return DEFAULT_LANG_ID;
}

} // It6_Translate_Ws
