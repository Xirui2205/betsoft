<?php
class It6_WsExtension_Server_Factory {

	const EXTENSION_NAMESPACE = 'It6_WsExtension_Server_';

	static public function newExtension(
		$class,
		$id,
		$params,
		$preprocessing = null,
		$postprocessing = null,
		$namespace = null
	) {
		if (!isset($namespace)) {
			$namespace = static::EXTENSION_NAMESPACE;
		}
		$fullClass = $namespace . $class;
		if (is_subclass_of($fullClass, 'It6_WsExtension_Server_Abstract'))
			return new $fullClass($id, $params, $preprocessing, $postprocessing);
		else
			throw new Exception('Invalid WsExtension class: "' . $fullClass . '"');
	}
}
