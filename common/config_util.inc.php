<?php

function getAppEnv() {
	if ($env = getenv('APPLICATION_ENVIRONMENT')) {
		return $env;
	} else if (function_exists('apache_getenv') &&
			$env = apache_getenv('APPLICATION_ENVIRONMENT')) {
		return $env;
	} else {
		return 'BBAS_TESTING';
	}
}

/**
 * @param string $name name of constant to define
 * @param scalar|array Scalar value will be defined, for array value with key that is equal to current environment will be defined.
 *                     If environment is unknown exception is thrown.
 *                     If value for current environment is not specified exception is thrown.
 * Examples:
 *    def('WEBSERVICE_HOST', array('PRODUCTION' => 'svc.domain.tld', 'PHPUNIT_TEST' => 'testsvc.domain.tld'));
 *    def('PROTOCOL', 'https://');
 */
function def($name, $value) {
	if (defined($name))
		return false;
	if (is_array($value)) {
		$env = getAppEnv();
		if (false === $env)
			throw new Exception('No application environment defined');
		if (!array_key_exists($env, $value))
			throw new Exception('No value for application environment specified: name="' . $name . '" current_env="' . $env . '"');
		$value = $value[$env];
	}
	define($name, $value);
	return true;
}

