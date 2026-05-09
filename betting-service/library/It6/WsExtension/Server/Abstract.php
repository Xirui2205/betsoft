<?php

abstract class It6_WsExtension_Server_Abstract extends It6_WsExtension {

	const DEFAULT_PREPROCESSING = true;
	const DEFAULT_POSTPROCESSING = true;
	
	protected $id;
	protected $params;
	protected $preprocessing;
	protected $postprocessing;

	public function __construct($id, $params, $preprocessing = null, $postprocessing = null) {
		$this->id = $id;
		$this->params = $params;
		$this->preprocessing = (isset($preprocessing) ? $preprocessing : static::DEFAULT_PREPROCESSING);
		$this->postprocessing = (isset($postprocessing) ? $postprocessing : static::DEFAULT_POSTPROCESSING);
	}

	public function getParam($name) {
		if (is_array($this->params) && array_key_exists($name, $this->params))
			return $this->params[$name];
		else
			return null;
	}

	public function hasPreprocessing() {
		return $this->preprocessing;
	}

	public function setPreprocessing($preprocessing = null) {
		$this->preprocessing = (isset($preprocessing) ? $preprocessing : static::DEFAULT_PREPROCESSING);
	}

	public function hasPostprocessing() {
		return $this->postprocessing;
	}
	
	public function setPostprocessing($postprocessing = null) {
		$this->postprocessing = (isset($postprocessing) ? $postprocessing : static::DEFAULT_POSTPROCESSING);
	}

	protected function setOutput(&$outputs, $key, $value) {
		if ( !array_key_exists(static::KEY_EXTENSIONS, $outputs) )
			$outputs[static::KEY_EXTENSIONS] = array();

		if ( !array_key_exists($this->id, $outputs[static::KEY_EXTENSIONS]) )
			$outputs[static::KEY_EXTENSIONS][$this->id] = array();
			
		$outputs[static::KEY_EXTENSIONS][$this->id][$key] = $value;
	}

	/**
	 * Inserts extensions outputs among other outputs.
	 * @param array $outputs Container for all outputs that will be modified
	 * @param array $extensionsOutputs Extensions' outputs
	 */
	public static function setOutputs(&$outputs, $extensionsOutputs) {
		$outputs[static::KEY_EXTENSIONS] = $extensionsOutputs;
	}

	/**
	 * Separates extensions output from other outputs.
	 * @param array $outputs Extensions' outputs among other data
	 * @return array Following pair: (array nonextensionsOutputs, array extensionsOutputs)
	 */
	public static function extractOutputs($outputs) {
		$data = $outputs;
		$extensions = array();
		if (isset($outputs[static::KEY_EXTENSIONS])) {
			$extensions = $outputs[static::KEY_EXTENSIONS];
			unset($data[static::KEY_EXTENSIONS]);
		}
		return array($data, $extensions);
	}

	/**
	 * Copies result data of this extension from one outputs to another ones.
	 * This function is useful for merging several outputs into one.
	 * @param array $srcOutputs
	 * @param array $dstOutputs
	 * @param boolean $overwrite
	 */
	public static function copyOutputs($srcOutputs, &$dstOutputs, $overwrite = true) {
		if ( isset($srcOutputs[static::KEY_EXTENSIONS]) ) {
			foreach ($srcOutputs[static::KEY_EXTENSIONS] as $id => $output) {
				if ($overwrite || !isset($dstOutputs[static::KEY_EXTENSIONS][$id]) )
					$dstOutputs[static::KEY_EXTENSIONS][$id] = $srcOutputs[static::KEY_EXTENSIONS][$id];
			}
		}
	}
	
	abstract public function preprocess(&$inputs, &$metadata, &$handler);	
	abstract public function postprocess(&$outputs, &$metadata, &$handler);
}
