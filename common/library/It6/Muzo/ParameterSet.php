<?php

class It6_Muzo_ParameterSet {
	protected $params = array();
	protected $valid = null;
	protected $validationMessages = array();

	/**
	 * @var bool $skipOptionalInDigest If TRUE and optional parameter was not used then no separator will be inserted into digest text.
	 *      Override in child classes.
	 */
	protected $skipOptionalInDigest = true;

	public function __construct(array $params) {
		$this->params = $params;
	}

	public function revalidate() {
		$this->valid = true;
		$this->validationMessages = array();
		foreach ($this->params as $name => $data) {
			if (isset($data['value'])) {
				if (!empty($data['validatorClass'])) {
					$class = $data['validatorClass'];
					if (isset($data['validatorParams']) && is_array($data['validatorParams']))
						$validatorParams = $data['validatorParams'];
					else
						$validatorParams = array();
					$validator = new $class($validatorParams);
					if (!$validator->isValid($data['value'])) {
						$this->valid = false;
						$this->validationMessages[$name] = $validator->getValidationErrorMessage();
					}
				}
			}
			else if (!isset($data['optional']) || (true !== $data['optional']) ) {
				$this->valid = false;
				$this->validationMessages[$name] = 'required but missing';
			}
		}
		return $this->valid;
	}

	public function isValid() {
		return $this->valid;
	}

	public function getValidationMessages() {
		return $this->validationMessages;
	}

	public function buildQueryString($currentQueryString) {
		$qs = '';
		foreach ($this->params as $name => $data) {
			if (isset($data['value'])) {
				if (!empty($qs))
					$qs .= '&';
				$qs = urlencode($name) . '=' . urlencode($data['value']);
			}
		}
		if (!empty($currentQueryString))
			$qs .= $currentQueryString . '&' . $qs;
		return $qs;
	}

	public function setParam($name, $value, $mustBeKnown) {
		if (array_key_exists($name, $this->params)) {
			$this->params[$name]['value'] = $value;
			return true;
		}
		else
			return !$mustBeKnown;
	}

	public function readParams(array $values, $exactMatch) {
		$result = true;
		foreach ($values as $name => $value) {
			if (!$this->setParam($name, $value, $exactMatch))
				$result = false;
		}
		return $result;
	}

	public function getParam($name) {
		if (array_key_exists($name, $this->params)) {
			$data = $this->params[$name];
			if (array_key_exists('value', $data))
				return $data['value'];
		}
		return null;
	}

	public function getParams() {
		$params = array();
		foreach ($this->params as $name => $data) {
			if (array_key_exists('value', $data))
				$params[$name] = $data['value'];
		}
		return $params;
	}

	public function getTextForDigest() {
		$first = true;
		$text = '';
		foreach ($this->params as $name => $data) {
			if (isset($data['notInDigest']) && (true === $data['notInDigest']) )
				continue;
			if (isset($data['value'])) {
				if (!empty($data['digestPreprocessorClass'])) {
					$class = $data['digestPreprocessorClass'];
					$preprocessor = new $class();
					$paramText = $preprocessor->preprocess($name, $data);
				}
				else
					$paramText = $data['value'];
				if ($first)	
					$first = false;
				else
					$text .= '|';
				$text .= trim($paramText);
			}
			else if (!$this->skipOptionalInDigest) {
				if ($first)	
					$first = false;
				else
					$text .= '|';
			}
		}
		return $text;
	}

}
