<?php

/**
 * Base class for other Betradar data node wrapper classes.
 */
class It6_Betradar_Node {

public $brId = null;
public $text = null;

public function __construct($brId) {
	$this->brId = $brId;
	$this->text = '';
}

public function addText($text) {
	$this->text .= $text['charData'];
}

/**
 * Remove node from collection of betradar nodes identified by brId member.
 * @param array $collection Collection of betradar nodes
 * @param string|integer|NULL $brId Betradar ID of node to be matched, NULL if all
 * @return integer|It6_Betradar_Node|NULL If all were requested to be removed, count of removed nodes is returned.
 *            If specific node were requested to be removed, node or NULL (when not found) is returned.
 */
protected function _removeNode(&$collection, $brId) {
	if (!isset($brId)) {
		$c = sizeof($collection);
		$collection = array();
		return $c;
	}
	else {
		$key = null;
		foreach ($collection as $k => $node) {
			if ($node->brId == $brId) {
				$key = $k;
				break;
			}
		}
		if (isset($key)) {
			$node = &$collection[$key];
			unset($collection[$key]);
			return $node;
		}
		else
			return null;
	}
}

/**
 * Add data that are to be preloaded before import to passed preloaded data handler.
 * @param It6_Betradar_PreloadedData $preloadedData Preloaded data handler
 */
public function getDataToPreload(&$preloadedData) {
	// standard implementation does nothing
}

} // class
