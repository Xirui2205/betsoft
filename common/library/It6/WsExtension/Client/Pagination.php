<?php
class It6_WsExtension_Client_Pagination extends It6_WsExtension_Client_Abstract implements It6_WsExtension_Pagination {

	private $total;

	public function __construct($id, $limit, $offset) {
		parent::__construct(
			$id,
			array(
				static::PARAM_LIMIT => $limit,
				static::PARAM_OFFSET => $offset,
			),
			array(
				static::PARAM_TOTAL,
				static::PARAM_TOTALPAGES,
			)
		);
	}

}
