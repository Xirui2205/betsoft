<?php
class It6_View_Helper_Trans extends Zend_View_Helper_Abstract {

public function trans($index, $section = null, $dictionary = null, $wrapUnfoundTag=true) {
	return Zend_Registry::get('translate')->trans($index, $section, $dictionary, $wrapUnfoundTag);
}

} // It6_View_Helper_Trans
