<?php
class It6_WsExtension_Server_Columns extends It6_WsExtension_Server_Query implements It6_WsExtension_Columns {

	const META_CONV = 'conv';

	public function __construct($id, $params, $preprocessing = null, $postprocessing = null) {
		parent::__construct($id, $params, $preprocessing, $postprocessing);
	}

	public function modifyQuery(&$select, $colConv, $metadata) {
		$columns = $this->params[static::PARAM_COLUMNS];
		$conv = $metadata[static::META_CONV];
		if (!empty($columns) && empty($this->params[static::PARAM_ADD]))
			$select->reset(Zend_Db_Select::COLUMNS);
		if (empty($columns))
			$columns = array_flip($conv);
		else if (empty($this->params[static::PARAM_NO_CONV_MATCH]))
			$columns = array_flip(array_intersect($conv, $columns));
		else {
			$_columns = array();
			$_conv = array_flip($conv);
			foreach ($columns as $alias => $expr) {
				if (is_numeric($alias)) {
					$alias = $expr;
					$dbExpr = (isset($_conv[$expr]) ? $_conv[$expr] : $expr);
				}
				else {
					if (is_array($expr)) {
						foreach ($expr as $convAlias => $_expr) break;
						$expr = $_expr;
					}
					else
						$convAlias = $alias;
					$dbColumn = (isset($_conv[$convAlias]) ? $_conv[$convAlias] : $convAlias);
					$dbExpr = preg_replace('/(?<!\\\\)\\?/', $dbColumn, $expr);
				}
				$_columns[$alias] = $dbExpr;
			}
			$columns = $_columns;
		}
		$select->columns( $columns );
	}

//	public function preprocess(&$inputs, &$metadata, &$handler) {
//		parent::preprocess($inputs, $metadata, $handler);
//		$inputs[static::INPUT_COLUMNS] = $this->params[static::PARAM_COLUMNS];
//	}

}
