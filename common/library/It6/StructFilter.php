<?php
class It6_StructFilter {

/**
 * Filter data from given context exposing only data as defined $dataDescr variable
 * @param struct $dataDescr Struct elements are handled as (key => value):<br/>
 *    numeric => string ... content of member variable (in class context) or element value (in array context); string is variable name/array key<br/>
 *                          string can be wrapped by predicate (must be true to be exposed), supported are: It6_NotEmpty, It6_NotNull<br/>
 *    numeric => array() ... each element with numeric key in currenct context is exposed with same numeric key and is processed recursively
 *                           (current context must be an array)<br/>
 *    string => mixed ... variable content is recursively processed
 * @param Object|array $context Source of data to be filtered
 * @param array $exposed Output structure with filtered data having same hierarchy as context
 */
public static function filter(array $dataDescr, $context, array &$exposed) {
	foreach ($dataDescr as $key => $value) {
		if (is_numeric($key)) {
			if (is_array($value)) {
				// this implies that context is array
				foreach ($context as $subkey => $subvalue) {
					if (is_numeric($subkey)) {
						$data = array();
						static::filter($value, $context[$subkey], $data);
						$exposed[$subkey] = $data;
					}
				}
			}
			else {
				$exposeEmpty = true;
				$exposeNull = true;
				if ($value instanceof It6_NotEmpty) {
					$exposeEmpty = false;
					$value = $value->value;
				}
				else if ($value instanceof It6_NotNull) {
					$exposeNull = false;
					$value = $value->value;
				}
				if (is_array($context))
					$data = (array_key_exists($value, $context) ? $context[$value] : null);
				else
					$data = (isset($context->$value) ? $context->$value : null);
				$visible = true;
				if (!isset($data))
					$visible = $exposeNull;
				else if (empty($data))
					$visible = $exposeEmpty;
				if ($visible)
					$exposed[$value] = $data;
			}
		}
		else {
			if (is_array($context))
				$subcontext = $context[$key];
			else
				$subcontext = $context->$key;
			$exposed[$key] = array();
			if (!empty($subcontext)) {
				static::filter($value, $subcontext, $exposed[$key]);
			}
		}
	}
}

} // class It6_StructFilter