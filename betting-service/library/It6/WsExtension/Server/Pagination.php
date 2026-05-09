<?php
class It6_WsExtension_Server_Pagination extends It6_WsExtension_Server_Query implements It6_WsExtension_Pagination {

	public function __construct($id, $params, $preprocessing = null, $postprocessing = null) {
		parent::__construct($id, $params, $preprocessing, $postprocessing);
	}

	public function modifyQuery(&$select, $colConv, $metadata) {
		$select
			->reset(Zend_Db_Select::LIMIT_COUNT)
			->reset(Zend_Db_Select::LIMIT_OFFSET);
		if (array_key_exists(static::PARAM_LIMIT, $this->params)) {
			$limit = $this->params[static::PARAM_LIMIT];
			$offset = (array_key_exists(static::PARAM_OFFSET, $this->params)
				? $this->params[static::PARAM_OFFSET] : 0
			);
			if(!empty($limit)) {
				return $select->limit($limit, $offset);
			}
			else {
				return $select;
			}
		}
		else if (array_key_exists(static::PARAM_PAGESIZE, $this->params)) {
			$pageSize = $this->params[static::PARAM_PAGESIZE];
			$page = (array_key_exists(static::PARAM_PAGE, $this->params)
				? $this->params[static::PARAM_PAGE] : 0
			);
			return $select->limitPage($page, $pageSize);
		}
	}

	public function postprocess(&$outputs, &$metadata, &$select) {
		$columns = $select->getPart(Zend_Db_Select::COLUMNS);
		$limit = $select->getPart(Zend_Db_Select::LIMIT_COUNT);
		$offset = $select->getPart(Zend_Db_Select::LIMIT_OFFSET);
		$group = $select->getPart(Zend_Db_Select::GROUP);
		$order = $select->getPart(Zend_Db_Select::ORDER);
		if (is_array($order)) {
			foreach ($order as &$term) {
				if (is_array($term)) {
					if (1 < count($term))
						$term = (string)($term [0]) . ' ' . $term[1];
					else
						$term = (string)($term [0]);
				}
			}
		}

		if(empty($group)) {
			$count = $select
				->reset(Zend_Db_Select::COLUMNS)
				->reset(Zend_Db_Select::LIMIT_COUNT)
				->reset(Zend_Db_Select::LIMIT_OFFSET)
				->reset(Zend_Db_Select::ORDER)
				->columns(array('COUNT(*)'))
				->query()->fetchColumn();
		}
		else {
			$subquery = $select
				->reset(Zend_Db_Select::COLUMNS)
				->reset(Zend_Db_Select::LIMIT_COUNT)
				->reset(Zend_Db_Select::LIMIT_OFFSET)
				->reset(Zend_Db_Select::ORDER)
				->columns(reset($group))
				->assemble();

			$count = $select->getAdapter()
				->query('SELECT COUNT(*) FROM ('.$subquery.') AS a')
				->fetchColumn();
		}


		$select->columns($columns)->order($order)->limit($limit, $offset);
		if (array_key_exists(static::PARAM_PAGESIZE, $this->params))
			$this->setOutput($outputs, static::PARAM_TOTALPAGES, ceil($count / $this->params[static::PARAM_PAGESIZE]));
		$this->setOutput($outputs, static::PARAM_TOTAL, $count);
	}
}
