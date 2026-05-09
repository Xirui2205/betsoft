<?php

class Utils {

/**
 * Process PTHML template. Template should avoid using variable names starting with undescore character.
 * @param $template string Template content of file name.
 * @param $vars array variables for template (key will become variable idetifier and value must be PHP expression eg. 'var' => "'test'" for string expression).
 * @param $isFile boolean TRUE if $template is file name.
 */
public static function processTemplate($_template, array $_vars = array(), $_isFile = false) {
	ob_start();
	foreach ($_vars as $_name => $_value) {
		eval("\$$_name=\$_vars['$_name'];");
	}
	if ($_isFile)
		include($_template);
	else
		eval('?>' . $_template );
	$_out = ob_get_contents();
	ob_end_clean();
	return $_out;
}

/**
 * @deprecated
 * @see It6_Text::camelCaseToDashed() 
 * Convert string from camelCase notation to dashed notation.
 * For reverse mapping after Zend's camelCasing of action names.
 * Try to avoid non alphabet characters in dashed notation for action names,
 * because Zend is making ambiguous conversion (like [a1-b -> a1B] and [a-1-b -> a1B]).
 * eg. howToPlaceBet -> how-to-place-bet
 *     example2number -> example2number
 *     example3numberAndMore -> example3number-and-more
 */
public static function camelCaseToDashed($cc) {
	if (1 == preg_match('/^([^[:upper:]]+)([[:upper:]].*)$/', $cc, $matches)) {
		$d = $matches[1];
		preg_match_all('/([[:upper:]])([^[:upper:]]*)/', $matches[2], $matches2, PREG_SET_ORDER);
		foreach ($matches2 as $match)
			$d .= '-' . strtolower($match[1]) . $match[2];
		return $d;
	}
	else
		return $cc;
}

public static function printSelectOptions($from,$to,$default) {
	$out = '';
	$sel = '';
	for ($i = $from; $i<=$to; $i++) {
		if ($i == $default) $sel = ' selected="selected"';
		$out .= '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
		$sel = '';
	}
	return $out;
}

}
