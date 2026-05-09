<?php

class It6_WsForm_Table {

	private $columns;
	private $extName;

	private static $LAYOUT_FOLDER	= 'wsForm/';
	private static $THEAD_LAYOUT	= 'standard-thead-order';
	private static $TBODY_LAYOUT	= 'standard-tbody';



	/**
	 * name: __construct
	 * @param array $columns array Array of arrays definning the table columns. The elements have the structure:
	 *   <ol>
	 *     <li>(string)column title if null there will be no title</li>
	 *     <li>(string)column wsName if null, there will be no order buttons</li>
	 *     <li>(string)column type defining hot the value should be modified before output Possible values are:
	 *       <ul>
	 *         <li>dateTime</li>
	 *         <li>time</li>
	 *         <li>date</li>
	 *       </ul>
	 *     </li>
	 *   </ol>
	 * @param $extName string The name for this extension
	 */
	public function __construct($columns, $extName=null) {
		$this->columns = $columns;
		if($extName == null)
			$this->extName = 'table';
	}



	public function getTheadLayout($layout=null, $orderData = null) {
		if($layout == null)
			$layout = self::$THEAD_LAYOUT;
		if (!isset($orderData))
			$orderData = array();

		$layoutName = self::$LAYOUT_FOLDER.$layout;
		$layout = new Zend_Layout();
		$layout->setLayoutPath(LAYOUT_PATH);

		$layout->columns = $this->columns;
		$order = array();
		foreach($this->columns as $column) {
			if(!empty($column)) {
				if ( in_array($column[1] . '_ASC',$orderData) ) {
					$order[$column[1]] = 'asc';
					break;
				}
				else if ( in_array($column[1] . '_DESC',$orderData) ) {
					$order[$column[1]] = 'desc';
					break;
				}
			}
		}
		if ( empty($order) ) {
			foreach($this->columns as $column) {
				if(!empty($column)) {
					if ( in_array($column[1] . ':asc',$orderData) ) {
						$order[$column[1]] = 'asc';
						break;
					}
					else if ( in_array($column[1] . ':desc',$orderData) ) {
						$order[$column[1]] = 'desc';
						break;
					}
				}
			}
			if ( empty($order) ) {
				foreach($this->columns as $column) {
					if(!empty($column)) {
						if ( in_array($column[1] . ' ASC',$orderData) ) {
							$order[$column[1]] = 'asc';
							break;
						}
						else if ( in_array($column[1] . ' DESC',$orderData) ) {
							$order[$column[1]] = 'desc';
							break;
						}
					}
				}
			}
		}
		foreach($this->columns as $column) {
			if ( !empty($column) && empty($order[$column[1]]) )
				$order[$column[1]] = '';
		}

		$layout->order = $order;

		$layout->setLayout($layoutName);
		return $layout->render();
	}



	public function getTbodyLayout($layout=null, $tableData, $additionalData = null) {
		if($layout == null)
			$layout = self::$TBODY_LAYOUT;

		$layoutName = self::$LAYOUT_FOLDER.$layout;
		$layout = new Zend_Layout();
		$layout->setLayoutPath(LAYOUT_PATH);

		foreach($this->columns as $col) {
			if(!empty($col[2])) {
				if($col[2] == 'dateTime') {
					foreach($tableData as &$row) {
						$row[$col[1]] = It6_Date::fromDb($row[$col[1]]);
					}
				}
				elseif($col[2] == 'date') {
					foreach($tableData as &$row) {
						$row[$col[1]] = It6_Date::fromDbAsDate($row[$col[1]]);
					}
				}
				elseif($col[2] == 'time') {
					foreach($tableData as &$row) {
						$row[$col[1]] = It6_Date::fromDbTime($row[$col[1]]);
					}
				}
			}
		}
		
		
		$layout->columns = $this->columns;
		$layout->data = $tableData;
		if (!empty($additionalData)) {
			foreach ($additionalData as $name => $value) {
				$layout->$name = $value;
			}
		}
		$layout->setLayout($layoutName);
		return $layout->render();
	}



	public function getOrderExtension($orderData, &$extensions) {
		$newOrderData = array();

		foreach($orderData as $order) {
			if ( strrpos($order, '_ASC') === strlen($order)-strlen('_ASC') ) {
				$newOrderData[] = substr($order,0,-strlen('_ASC'))  . " ASC";
			}
			else if ( strrpos($order, '_DESC') === strlen($order)-strlen('_DESC') ) {
				$newOrderData[] = substr($order,0,-strlen('_DESC')) . " DESC";
			}
		}
		foreach($orderData as $order) {
			if ( strrpos($order, ':asc') === strlen($order)-strlen(':asc') ) {
				$newOrderData[] = substr($order,0,-strlen(':asc'))  . " ASC";
			}
			else if ( strrpos($order, ':desc') === strlen($order)-strlen(':desc') ) {
				$newOrderData[] = substr($order,0,-strlen(':desc'))  . " DESC";
			}
		}
		foreach($orderData as $order) {
			if ( strrpos($order, ' ASC') === strlen($order)-strlen(' ASC') ) {
				$newOrderData[] = substr($order,0,-strlen(' ASC'))  . " ASC";
			}
			else if ( strrpos($order, ' DESC') === strlen($order)-strlen(' DESC') ) {
				$newOrderData[] = substr($order,0,-strlen(' DESC'))  . " DESC";
			}
		}
		
		
		$extensions[$this->extName.'order'] = new It6_WsExtension_Client_Order($this->extName.'order', $newOrderData);
	}



	public function getColumnsExtension(&$extensions, $additionalColumns = null) {
		/* temporarily disabled, because callers of this were used to nonfunctional columns extension
		$columns = array();
		foreach ($this->columns as $col) {
			if (!empty($col[1]))
				$columns[] = $col[1];
		}
		if (is_array($additionalColumns)) {
			foreach ($additionalColumns as $col) {
				if (!empty($col) && !in_array($col, $columns))
					$columns[] = $col;
			}
		}
		if (!empty($columns))
			$extensions[$this->extName.'columns'] = new It6_WsExtension_Client_Columns($this->extName.'columns', $columns);
		*/
	}
}
