<?php

class It6_MessageFormat {

	/**
	 * Formats given message by replacing numbered/named placeholders (eg. "{0}" or {count}) by message parameters.
	 * Unmatched placeholders are stripped out.
	 * You can use escape sequence '\{' to avoid left bracket to be parsed as start of placeholder,
	 * use '\\' escape sequence for '\' character itself.
	 * In placeholder names use only characters without special meaning in PCRE, names are not case sensitive.
	 * One placeholder can occure more times in format string.
	 * @param string|array $message message format string
	 * @param mixed $params one (matched against {0}) or array of message parameters (placeholder_number_or_name => replacement)
	 * @return string formated message
	 */
	public static function format($message, $params) {
		if (!is_array($params))
			$params = array($params);
		$cb = function ($matches) use (&$params) {
			$bss = $matches[1];
			$bssCount = strlen($bss);
			$escape = $bssCount % 2; // escape { ?
			$bssCount = ($bssCount - $escape) / 2; // number of escaped \
			$name = $matches[3];
			$value = (array_key_exists($name, $params) ? $params[$name] : '');
			$replace = ($escape ? $matches[2] : $value);
			return substr($bss, 0, $bssCount) . $replace;
		};
		return preg_replace_callback('/(?<!\\\\)(\\\\*)(\\{([^}]*)\\})/i', $cb, $message);
	}
	
}
