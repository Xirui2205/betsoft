<?php
class It6_WsExtension_Client_Order extends It6_WsExtension_Client_Abstract implements It6_WsExtension_Order {

	/**
	 * @param string $id
	 * @param array $order
	 */
	public function __construct($id, $order) {
		parent::__construct($id, array(
			static::PARAM_ORDER => $order
		));
	}

}
