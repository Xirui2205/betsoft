<?php

class It6_Log_Filter_Tag extends Zend_Log_Filter_Abstract
{

	protected $_regexp;
	protected $_tag;

	/**
	 * All filter criteria must be  satisfied
	 * @param string $regexp regular expression that tag must match or NULL
	 * @param string|array $tag one or more tags to be logged or NULL
	 */
	public function __construct($regexp, $tag) {
		if (isset($regexp) && @preg_match($regexp, '') === false) {
			require_once 'Zend/Log/Exception.php';
			throw new Zend_Log_Exception("Invalid regular expression '$regexp'");
		}
		$this->_regexp = $regexp;
		$this->_tag = $tag;
	}

	static public function factory($config) {
		$config = self::_parseConfig($config);
		$config = array_merge(array(
			'regexp' => null,
			'tag' => null,
		), $config);

		return new self(
			$config['regexp'],
			$config['tag']
		);
	}

	public function accept($event) {
		$eventTag = $event['tag'];
		if ( isset($this->_regexp) && (1 != preg_match($this->_regexp, $eventTag)) )
			return false;
		if (isset($this->_tag)) {
			if (is_array($this->_tag)) {
				$matched = false;
				foreach ($this->_tag as $tag) {
					if ($tag == $eventTag) {
						$matched = true;
						break;
					}
				}
				if (!$matched)
					return false;
			}
			else if ($this->_tag != $eventTag)
				return false;
		}
		return true;
	}
}
