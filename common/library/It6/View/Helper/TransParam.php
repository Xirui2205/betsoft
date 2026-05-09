<?php

class It6_View_Helper_TransParam extends Zend_View_Helper_Abstract {

public function transParam($index, $param, $section = null, $dictionary = null, $wrapUnfoundTag=true) {
	return Zend_Registry::get('translate')->transParam($index, $param, $section, $dictionary, $wrapUnfoundTag);
}

} // It6_View_Helper_TransParam
