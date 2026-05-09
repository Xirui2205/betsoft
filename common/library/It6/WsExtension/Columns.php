<?php
interface It6_WsExtension_Columns {
	/**
	 * PARAM_COLUMNS is array that can consist of:
	 * string ... just WS field
	 * returned_key => ws_field
	 *    ... renamed WS field
	 * returned_ws_field => expression
	 *    ... in expression placeholder '?' will replaced by db column translated from WS field (so key should be just WS field)
	 * returned_key => ws_field => expression
	 *    ... in expression placeholder '?' will be replaced by db column translated from WS field, result can be renamed
	 *        eg. array('namex' => array('name' => '(? COLLATE utf8_czech_ci)'))
	 *       with $CONV['name']='col_name' will have result SQL: '(col_name COLLATE utf8_czech_ci) AS namex'
	 * Note that the complex types of values implies that you must use PARAM_NO_CONV_MATCH too.
	 */
	const PARAM_COLUMNS = 'columns';
	const PARAM_COUNT = 'count';
	const PARAM_ADD = 'add'; // default FALSE; if TRUE, columns are not replaced but appended
	const PARAM_NO_CONV_MATCH = 'noConvMatch'; // default FALSE; if TRUE columns are not intersected with $CONV
}