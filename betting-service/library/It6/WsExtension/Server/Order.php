<?php
class It6_WsExtension_Server_Order extends It6_WsExtension_Server_Query implements It6_WsExtension_Order {

	public function __construct($id, $params, $preprocessing = null, $postprocessing = null) {
		parent::__construct($id, $params, $preprocessing, $postprocessing);
	}

	public function modifyQuery(&$query, $colConv, $metadata) {
		$order = $this->params[static::PARAM_ORDER];
		$cols = array();
		foreach ($order as $v) {
			$col = call_user_func($colConv,$v);
			if(is_array($col))
				$cols[] = reset($col);
			else
				$cols[] = $col;
		}
		$query = $query->order($cols);
	}
}
