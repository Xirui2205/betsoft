<?php

class It6_View_Helper_FormSubmit extends Zend_View_Helper_FormSubmit {

public function formSubmit($name, $value = null, $attribs = null)
{
	$info = $this->_getInfo($name, $value, $attribs);
	extract($info); // name, value, attribs, options, listsep, disable

	// check if disabled
	$disabled = '';
	if ($disable) {
		$disabled = ' disabled="disabled"';
	}

	// check if value is reenderable
	$attrValue = '';
	if (!empty($value))
		$attrValue = ' value="' . $this->view->escape($value) . '"';

	// XHTML or HTML end tag?
	$endTag = ' />';
	if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
		$endTag= '>';
	}

	// Render the button.
	$xhtml = '<input type="submit"'
			. ' name="' . $this->view->escape($name) . '"'
			. ' id="' . $this->view->escape($id) . '"'
			. $attrValue
			. $disabled
			. $this->_htmlAttribs($attribs)
			. $endTag;

	return $xhtml;
}


} // class It6_View_Helper_FormSubmit