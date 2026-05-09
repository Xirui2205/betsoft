<?php

class It6_WsForm_Filter {


	private $filterFields;
	private $filterForm;
	private $extName;

	private static $LAYOUT_FOLDER	= 'wsForm/';
	private static $LAYOUT			= 'standard-filter';
	private static $TABLE_CLASS		= 'Table';



	/**
	 * name: __construct
	 * @param array $filterFields elements:
	 * 0 (string)label The label that descibes the filter form element to the user. It gets translated automaticaly by Zend.
	 * 1 (string)name Sets the value of the name parameter of the filter form element.
	 * 2 (mixed)type - Sets which is the type of filter form element and optinaly how data should be processed before being passed to WS.
	 *   If array is passed then the key defines the type and the value is a lambda function.
	 *   Field types can take following values:
	 *  - input
	 *  - dateTime = text input with dateTime picker
	 *  - date = text input with date picker
	 *  - select
	 *  - checkbox
	 *  - multiselect
	 * 3 (array) of arrays where each element defines one where part of the sql query.
	 *   EXAMPLES:
	 *    - array('name', 'LIKE', '%?%') - WHERE name LIKE %value_set_by_user%
	 *    - array(array('DATE(?)' => 'registrationTime'), '>= DATE(?)') - WHERE DATE(registartionTime) >= DATE(value_set_by_user)
	 *  0 (string|array) columnName or array("expression with ? as placeholder" => "column name")
	 *  1 (string)comparisonOperation
	 *  2 (string | null)value to compare against, if null use value from input, can use placeholder ?
	 * 4 (string)validator - optional, name of the validator class that implements Zend_Validate_Interface
	 * 5 (array)validatorParams - optional, array of the parameters that get passed to the validator constructor
	 * 6 (mixed)option - special option
	 *  - for select element its array of option_value=>option_label pairs
	 *    -can take keyword 'none' as key for zero.
	 *    -can take keyword 'null' as key fro NULL values
	 * 7 (array) to identify a subfilter, useful ie for multiselect where some querries have to be joined by OR and others by AND
	 *   EXAMPLES:
	 *    - array('statusSubFilter', 'OR') - takes the selected options of the multiselect, joins them by OR and the joisn this subquery to the rest of the filter by AND
	 *  0 subfilterName
	 *  1 opperand
	 * 8 field class (html element class)
	 */
	public function __construct(array $filterFields, $decoratedFormClass=null, $extName=null, $tr = true) {
		if($decoratedFormClass == null)
			$decoratedFormClass = self::$TABLE_CLASS;

		$this->filterFields	= $filterFields;
		$this->filterForm	= It6_Models_Form_FilterFactory::getForm($decoratedFormClass, $filterFields, $tr);
		if($extName == null)
			$this->extName = 'filter';
	}



	public function getLayout($layout = null, $filterData) {
		if($layout == null)
			$layout = self::$LAYOUT;

		$layoutName = self::$LAYOUT_FOLDER.$layout;
		$layout = new Zend_Layout();
		$layout->setLayoutPath(LAYOUT_PATH);

		$layout->filterForm = $this->filterForm;
		$layout->setLayout($layoutName);
		return $layout->render();
	}

	public function getFilterForm() {
		return $this->filterForm;
	}

	public function getExtension($filterData=null, &$extensions) {

		if($filterData == null)
			$filterData = array();

		foreach($this->filterFields as $field) {

			if(empty($field[7]) || !is_array($field[7])) {
				$field[7][0] = 'defSubfilter';
				$field[7][1] = 'AND';
			}

			foreach($field[3] as $queryDef) {
				$filterDef[$field[7][0]]['OP'] = $field[7][1];

				if(is_array($queryDef[0])) {
					$columnExpression['EXPR'] = key(reset($queryDef));
					$queryDef[0] = reset($queryDef[0]);
				}
				else
					$columnExpression = array();

				if(!empty($filterData[$field[1]])) {
					if(
						$filterData[$field[1]] == 'none'
						&& ($field[2] == 'select' || $field[2] == 'multiselect')
					) {
						$fieldData = '0';
					}
					else {
						if ($field[2] == 'multiselect') $fieldData = trim($filterData[$field[1]][0]);
						else $fieldData = trim($filterData[$field[1]]);
					}

					if(is_array($field[2])) {
						$arrKeys = array_keys($field[2]);
						$fieldType = reset($arrKeys);
						$func = reset($field[2]);
					}
					else {
						$fieldType = $field[2];
						$func = null;
					}

					if($fieldType == 'date') {
						if(isset($field[6]) && $field[6] == 'start') {
							$dateStart = $fieldData;
							list($dbData,$_) = It6_Date::dateToDbInterval($dateStart);
							unset($dateStart);
						}
						elseif(isset($field[6]) && $field[6] == 'end') {
							$dateEnd = $fieldData;
							list($_,$dbData) = It6_Date::dateToDbInterval($dateEnd,$dateEnd);
							unset($dateEnd);
						}
						else {
							$dbData = It6_Date::toDbAsDate($fieldData);
						}
					}
					elseif($fieldType == 'dateTime')
						$dbData = It6_Date::toDb($fieldData);
					elseif(!empty($queryDef[2]))
						$dbData = str_replace('?', $fieldData, $queryDef[2]);
					else
						$dbData = $fieldData;
					
					if($func !== null) {
						$dbData = call_user_func($func, $dbData);
					}

					if($fieldType == 'multiselect' && is_array($dbData)) {						
						foreach($dbData as $dat) {
							if($queryDef[1] == 'IS')
								$queryParams = array('?'=> $queryDef[0], 'OP' => $queryDef[1].' '.$dat);
							else
								$queryParams = array('?'=> array($queryDef[0] => $dat), 'OP' => $queryDef[1]);
						}
					}
					else if(
						$filterData[$field[1]] == 'null'
						&& ($field[2] == 'select' || $field[2] == 'multiselect')
					) {
						$queryParams = array('?' => $queryDef[0], 'OP' => 'IS NULL');
					}
					else {
						$queryParams = array()	;
							//if($queryDef[1] == 'IS' || $queryDef[1] == 'IS NOT') {
							//	$queryParams = array('?'=> $queryDef[0], 'OP' => $queryDef[1].' '.$dbData);
							//} else {
							//	$queryParams = array('?'=> array($queryDef[0] => $dbData), 'OP' => $queryDef[1]);
							//}

							$_qqueryDef0 = isset($queryDef[0]) ? $queryDef[0] : '';
							$_qqueryDef1 = isset($queryDef[1]) ? $queryDef[1] : '';
							if($_qqueryDef1 == 'IS' || $_qqueryDef1 == 'IS NOT') {
								$queryParams = array('?'=> $_qqueryDef0, 'OP' => $_qqueryDef1.' '.$dbData);
							} else {
								$queryParams = array('?'=> array($_qqueryDef0 => $dbData), 'OP' => $_qqueryDef1);
							}
					}
					$queryParams = array_merge($queryParams, $columnExpression);
					$filterDef[$field[7][0]][] = $queryParams;

		            if ($field[2] == 'multiselect') {
					  if (isset($filterData[$field[1]])) {
					  	foreach ($filterData[$field[1]] as $value) {
					        $queryParams = array('?'=> array($field[1] => $value));
						    $queryParams = array_merge($queryParams, $columnExpression);
							$filterDef[$field[7][0]][] = $queryParams;
					  	}
					  }
		            }

				}
			}
		}

		$this->filterForm->populate($filterData);
		if(!empty($filterDef)) {
			if($this->filterForm->isValid($filterData)) {

				foreach($filterDef as $subFilterName => $def) {//
					if(count($def) > 1)
						$extensions[$this->extName.'-'.$subFilterName] = new It6_WsExtension_Client_Filter($this->extName.'-'.$subFilterName, $def);
				}

				return true;
			}
		}

		return false;
	}
}
