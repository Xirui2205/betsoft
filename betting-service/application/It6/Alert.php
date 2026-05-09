<?php

abstract class It6_Alert {

	const PARAMETER_PREFIX = 'alert';
	const PARAMETER_SEPARATOR = '.';
	const PARAM_DISABLED = 'disabled';

	public static function assert($params, $userId = null, $branchId = null) {
		$disabled = static::getParameter(static::PARAM_DISABLED, $userId, $branchId);
		if ( !empty($disabled) ) {
			It6_Log::debug(
				"Alert '%type%' is disabled.",
				It6_Log::TAG_ALERT,
				array(
					'userId' => $userId,
					'branchId' => $branchId,
					'type' => static::getType()));

			return false;
		}

		if ( true === static::check($params, $userId, $branchId) ) {
			It6_Log::info(
				"Alert '%type%' ran.",
				It6_Log::TAG_ALERT,
				array(
					'params' => $params,
					'userId' => $userId,
					'branchId' => $branchId,
					'type' => static::getType()));

			return static::run($params, $userId, $branchId);
		} else {
			It6_Log::debug(
				"Alert '%type%' doesn't ran.",
				It6_Log::TAG_ALERT,
				array(
					'params' => $params,
					'userId' => $userId,
					'branchId' => $branchId,
					'type' => static::getType()));

			return false;
		}
	}

	public static function check($params, $userId = null, $branchId = null) {
		throw new Exception("Call of abstract method");
	}

	protected static function run($params, $userId = null, $branchId = null) {
		throw new Exception("Call of abstract method");
	}

	protected static function getType() {
		preg_match('/_([A-Za-z0-9]+)$/',get_called_class(), $matches);
		return $matches[1];
	}

	protected static function getParameterFullName($name) {
		return implode(
			self::PARAMETER_SEPARATOR,
			array(
				self::PARAMETER_PREFIX,
				static::getType(),
				$name));
	}

	protected static function getParameter($name, $userId = null, $branchId = null, $common = false) {
		$fullName = true === $common
			? self::PARAMETER_PREFIX . self::PARAMETER_SEPARATOR . $name
			: static::getParameterFullName($name);

		if ( !empty($userId) )
			$ret = Webservice_Parameter::getUserParameter($fullName, $userId);
		else if ( !empty($branchId) )
			$ret = Webservice_Parameter::getBranchParameter($fullName, $branchId);
		else
			$ret = Webservice_Parameter::getGlobalParameter($fullName);

		if ( true !== $common && null == $ret )
			$ret = static::getParameter($name, $userId, $branchId, true);

		return $ret;
	}

}