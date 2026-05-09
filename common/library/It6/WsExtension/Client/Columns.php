<?php
class It6_WsExtension_Client_Columns extends It6_WsExtension_Client_Abstract implements It6_WsExtension_Columns {

	public function __construct($id, $columns, $noConvMatch = null) {
		$params = array(static::PARAM_COLUMNS => $columns);
		if (isset($noConvMatch))
			$params[static::PARAM_NO_CONV_MATCH] = $noConvMatch;
		parent::__construct($id, $params);
	}

}
