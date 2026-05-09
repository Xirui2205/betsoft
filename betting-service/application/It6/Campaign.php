<?php
abstract class It6_Campaign {
	
	const PARAMETER_PREFIX = 'campaign';
	const PARAMETER_SEPARATOR = '.';
	
	public static function putOn($circumstances) {
		if ( true === static::validate($circumstances) ) {
			It6_Log::info(
				"Campaign '%type%' put on.",
				It6_Log::TAG_CAMPAIGN,
				array(
					'$circumstances' => $circumstances,
					'type' => static::getType()));

			return static::run($circumstances);
		} else {
			It6_Log::debug(
				"Campaign '%type%' doesn't put on.",
				It6_Log::TAG_CAMPAIGN,
				array(
					'$circumstances' => $circumstances,
					'type' => static::getType()));

			return false;
		}
	}

	public static function validate($circumstances) {
		throw new Exception("Call of abstract method");
	}
	
	public static function run($circumstances) {
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

	/**
	 * Retrives campaing parameter(s)
	 * @param string|array $name One or list of parameter names (short names)
	 * @param boolean $common If FALSE then campaign specific parameter full name
	 *                is tried first before common parameter full name.
	 *                False is default.
	 * @return mixed One parameter value if $name was string or map (name => value)
	 *               if list was passed, unknown parameters' values will be NULL.
	 */
	protected static function getParameter($name, $common = false) {
		static $cache = array();

		$more = is_array($name);
		$names = ($more ? $name : array($name));

		$fullNames = array();
		foreach ($names as $_name) {
			$fullNames[$_name] = array(
				'common' => self::PARAMETER_PREFIX . self::PARAMETER_SEPARATOR . $_name,
				'spec' => static::getParameterFullName($_name),
			);
		}
		$result = array();
		$unknown = array();
		$unknownFull = array();
		foreach ($fullNames as $_name => $variants) {
			$hit = false;
			if (!$common) {
				$key = $variants['spec'];
				$hit = isset($cache[$key]);
			}
			if (!$hit) {
				$key = $variants['common'];
				$hit = array_key_exists($key, $cache);
			}
			if ($hit) {
				$result[$_name] = $cache[$key];
			}
			else {
				$unknown[] = $_name;
				$unknownFull[] = $variants['common'];
				$unknownFull[] = $variants['spec'];
			}
		}
		if (!empty($unknown)) {
			$params = Webservice_Parameter::getGlobalParameter($unknownFull);
			foreach ($unknown as $_name) {
				$variants = $fullNames[$_name];
				foreach ($variants as $variant) {
					$cache[$variant] = (isset($params[$variant]) ? $params[$variant] : null);
				}
				$key = null;
				if (!$common) {
					$key = $variants['spec'];
					if (!isset($cache[$key])) {
						$key = null;
					}
				}
				if (!isset($key)) {
					$key = $variants['common'];
					if (!isset($cache[$key])) {
						$key = null;
					}
				}
				$result[$_name] = (isset($key) ? $cache[$key] : null);
			}
		}

		if ($more) {
			return $result;
		}
		else {
			return (isset($result[$name]) ? $result[$name] : null);
		}
	}
}