<?php
abstract class It6_WsExtension_Server_Query extends It6_WsExtension_Server_Abstract implements It6_WsExtension_Query {

	const META_COL_CONV = 'colConv';

	public function __construct($id, $params, $preprocessing = null, $postprocessing = null) {
		parent::__construct($id, $params, $preprocessing, $postprocessing);
	}

	public function preprocess(&$inputs, &$metadata, &$handler) {
		$colConv = $metadata[static::META_COL_CONV];
		$this->modifyQuery($handler, $colConv, $metadata);
	}

	public function postprocess(&$outputs, &$metadata, &$handler) {}

	abstract public function modifyQuery(&$query, $colConv, $metadata);
}
