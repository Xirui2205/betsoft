<?php

class Zend_View_Helper_EscapeJs extends Zend_View_Helper_Abstract {

	public function escapeJs($value, $singleQuote, $isAttr) {
		$table =  array("\n" => '\\n', "\t" => '\\t');
		if ($singleQuote)
			$table["'"] = "\\'";
		else
			$table['"'] = '\\"';
		$ret = strtr($value, $table);
		if ($isAttr)
			$ret = htmlspecialchars($ret);
		return $ret;
	}

}