<?php
/**
where_definition:
	group_definition
	operand_definition

group_definition:
	array(
		['OP' => <'AND'> | 'OR',]
		['NEG' => <FALSE> | TRUE,]
		(group_definition | condition_definition)+
	)

condition_definition:
	array(
		'?' => string|objName|array(objName => [string|number|array,]), // valid value type depends on OP; default NULL
		'EXPR' => string // SQL expression used instead of column, use ? as placoholder for column name
		['reqName' => string,] // default NULL
		['OP' => string,] // operator: < '=' >, '<', '>', 'IN(?)', 'IS NULL', 'IS NOT NULL' ...; operator can contain placeholder
		['NEG' => <FALSE>  | TRUE,]
		['optional' => <FALSE> | TRUE,]
		['validator' => string|array|Zend_Validate,] // string is class name, array(class_name=>config),default NULL
	)

'?' defines objName (object space name for field) or (objName => explicit value) pair.
If 'reqName' is not empty, value is read from request (overrides explicit value only if explicit value is empty). Default value is equal to objName.
If value is not set and it is required, then value is read from request automatically.
If 'validator' is not empty, value is validated at first.
If 'optional' is not empty, empty value will surpress the use of this field.
For all operators except 'IN' scalar value is required, for 'IN' when array is passed, comma separated list of values will be produced.

examples (MySQL identifier quotations):
	// SQL: "`username`='user1'"
	$where = array('?' => array('username' => 'user1'))
	// value is read from $_REQUEST['from'], validated by given Zend_Validate_Date, SQL produced will be "`validFrom`>'_parsed_date_'"
	$where = array( '?' => array('validFrom', 'reqName' => 'from', 'OP' => '>', 'validator' => array('Zend_Validate_Date'=>array('format'=>'d.m.YYYY'))) )
	// SQL: "NOT(`username`='user1' AND `passwd`='mypass')"
	$where = array( 'NEG' => true, array('?' => array('username', 'user1')), array('?' => array('passwd', 'mypass')) )
	// SQL: "NOT(`username`='user1' AND `passwd`='mypass' AND (`banned`=1 OR NOT(`refused`=0)))"
	$where = array('NEG' => true,
		array( '?' => array('username' => 'user1') ),
		array( '?' => array('passwd', 'mypass') ),
		array('OP' => 'OR',
			array('?' => array('banned', 1) ),
			array('?' => array('refused', 0, 'NEG' => true) ),
		)
	)
	// SQL: `username` IS NULL
	$where = array('?' => 'username', 'OP' => 'IS NULL')
*/

class It6_WsExtension_Client_Filter extends It6_WsExtension_Client_Abstract implements It6_WsExtension_Filter {

protected $_definition;
protected $_filter;

public function __construct($id, array $definition, Zend_Form $form = null) {
	parent::__construct(
		$id,
		array(static::PARAM_FILTER => $this->_createFilter($definition))
	);

}

/**
 * Creates WS parameter data from definition @see documentation of this class for definition description
 * @param array $definition Filter definition
 */
protected function _createFilter(array $definition) {
	$this->_definition = $definition;
	$filter = array();
	$this->_walkDefinition($definition, $filter);
	$this->_filter = (empty($filter) ? $filter : $filter[0]);
	return $this->_filter;
}

public static function isGroupDefinition(array $definition) {
	return !array_key_exists('?', $definition);
}

protected function _walkDefinition(array $definition, array &$filter) {
	if (static::isGroupDefinition($definition)) {
		$grpFilter = array();
		foreach ($definition as $key => $value) {
			if (is_numeric($key)) {
				$this->_walkDefinition($value, $grpFilter);
			}
			else
				$grpFilter[$key] = $value;
		}
		$filter[] = $grpFilter;
	}
	else {
		//TODO: process value, optional etc.
		$filter[] = $definition;
	}
}

} // class It6_WsExtension_Client_Filter
