<?php

class It6_Models_DecoratedForm_Table extends It6_Models_DecoratedForm_Abstract {

	private $tableClass = 'form';


	/**
	 * Sets the html class attribute for the table
	 * 
	 * @param string|array $class the class or classes to be set
	 */
	public function __construct($class=null) {
		if (!empty($class)) {
			if(is_array($class))
				$this->tableClass = implode(' ', $class);
			else
				$this->tableClass = $class;
		} else if (empty($class) || $class == 'form') {
			$this->tableClass = 'table table-striped table-hover';
		}
		
		parent::__construct();
	}


	/**
	 * Returns decorators for Zend_Form
	 * 
	 * @param array $options elements can have values
	 * - 'no-table'
	 * - 'no-form'
	 * @return array Zend_Form decorators
	 */
	public function getFormDecorator($options=array()) {
		$description = $this->getDescription();

		if(!in_array('no-table', $options)) {
			$decorators[] = array(array('tableTagOpen' => 'HtmlTag'), array('tag' => 'table', 'openOnly'=>true, 'class' => $this->tableClass, 'placement'=>'append'));
			$decorators[] = array(array('tbodyTagOpen' => 'HtmlTag'), array('tag' => 'tbody', 'openOnly'=>true, 'placement'=>'append'));
		}

		if(!empty($description)) {
			$decorators[] = array(array('rowOpenTr' => 'HtmlTag'), array('tag' => 'tr', 'openOnly'=>true, 'placement' => 'append'));
			$decorators[] = array('Description', array('tag' => 'td', 'escape' => false, 'class' => 'description', 'colspan' => '2'));
			$decorators[] = array(array('rowCloseTr' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly'=>true, 'placement' => 'append'));
		}

		$decorators[] = 'FormElements';
		
		if(!in_array('no-table', $options)) {
			$decorators[] = array(array('tbodyTagClose' => 'HtmlTag'), array('tag' => 'tbody', 'closeOnly'=>true, 'placement'=>'append'));
			$decorators[] = array(array('tableTagClose' => 'HtmlTag'), array('tag' => 'table', 'closeOnly'=>true, 'placement'=>'append'));
		}
		
		if(!in_array('no-form', $options))
			$decorators[] = 'Form';

		return $decorators;
	}


	/**
	 * Returns decorators for Zend_Form_Element
	 * 
	 * @param boolean $openTr indicates whether this element should open a new table row
	 * @param boolean $closeTr indicates whether this element should close the table row
	 * @param integer $preSpacerColSpan number of padding columns to be added before the element
	 * @param integer $aftSpacerColSpan number of padding columns to be added after the element
	 * @param integer $elColspan number of columns the element td spans
	 * @return array Zend_Form_Element decorators
	 */
	public function getElementDecorator($openTr = true, $closeTr = true, $preSpacerColSpan = 0, $aftSpacerColSpan = 0, $elColspan=null) {
 		if(empty($elRowspan)) {
			$elRowspan = 1;
		}
		if(empty($elColspan)) {
			$elColspan = 1;
		}
		
		$decorator = array(
			array(array('cellLabelOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-label', 'openOnly'=>true, 'placement' => 'append')),
			array('Label', array('escape' => false, 'placement' => 'append' )),
			array(array('cellLabelCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
			array(
				array('cellElementOpenTd' => 'HtmlTag'),
				array(
					'tag' => 'td',
					'class' => 'cell-element',
					'openOnly' => true,
					'colspan' => $elColspan,
					'placement' => 'append',
				)
			),
			array(array('cellElementOpenDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			'ViewHelper',
			array('Errors', array('escape' => false)),
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description')),
			array(array('cellElementCloseDiv' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
		);

		return $this->addRowTags($decorator, $openTr, $closeTr, $preSpacerColSpan, $aftSpacerColSpan);
	}


	/**
	 * Returns decorators for It6_Form_Element_Captcha
	 * 
	 * @param boolean $openTr indicates whether this element should open a new table row
	 * @param boolean $closeTr indicates whether this element should close the table row
	 * @param integer $preSpacerColSpan number of padding columns to be added before the element
	 * @param integer $aftSpacerColSpan number of padding columns to be added after the element
	 * @return array It6_Form_Element_Captcha decorators
	 */
	public function getCaptchaDecorator($openTr = true, $closeTr = true, $preSpacerColSpan = 0, $aftSpacerColSpan = 0) {
		$decorator = array(
			array(array('cellLabelOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-label', 'openOnly'=>true, 'placement' => 'append')),
			array('Label', array('escape' => false, 'placement' => 'append' )),
			array(array('cellLabelCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			new It6_Form_Decorator_Captcha,
			array('Errors', array('escape' => false)),
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description')),
			array(array('cellElementCloseDiv' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
		);

		return $this->addRowTags($decorator, $openTr, $closeTr, $preSpacerColSpan, $aftSpacerColSpan);
	}


	/**
	 * Returns decorators for Zend_Form_Element_Button
	 * 
	 * @param boolean $openTr indicates whether this element should open a new table row
	 * @param boolean $closeTr indicates whether this element should close the table row
	 * @param integer $preSpacerColSpan number of padding columns to be added before the element
	 * @param integer $aftSpacerColSpan number of padding columns to be added after the element
	 * @return array Zend_Form_Element_Button decorators
	 */
	public function getButtonDecorator($openTr = true, $closeTr = true, $preSpacerColSpan = 0, $aftSpacerColSpan = 0) {
		$decorator = array(
			array(array('cellElementOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			'ViewHelper',
			'Errors',
			array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly'=>true, 'placement' => 'append')),
		);

		return $this->addRowTags($decorator, $openTr, $closeTr, $preSpacerColSpan, $aftSpacerColSpan);
	}


	/**
	 * Returns decorators for Zend_Form_Element_File
	 * 
	 * @param boolean $openTr indicates whether this element should open a new table row
	 * @param boolean $closeTr indicates whether this element should close the table row
	 * @param integer $preSpacerColSpan number of padding columns to be added before the element
	 * @param integer $aftSpacerColSpan number of padding columns to be added after the element
	 * @return array Zend_Form_Element_File decorators
	 */
	public function getFileDecorator($openTr = true, $closeTr = true, $preSpacerColSpan = 0, $aftSpacerColSpan = 0) {
		$decorator = array(
			array(array('cellLabelOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-label', 'openOnly'=>true, 'placement' => 'append')),
			array('Label', array('escape' => false, 'placement' => 'append')),
			array(array('cellLabelCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			'File',
			array('Errors', array('escape' => false)),
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description')),
			array(array('cellElementCloseDiv' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
		);

		return $this->addRowTags($decorator, $openTr, $closeTr, $preSpacerColSpan, $aftSpacerColSpan);
	}


	/**
	 * Returns decorators for Zend_Form_Element_Hidden. It is needed to produce valid html
	 * 
	 * @param boolean $openTr indicates whether this element should open a new table row
	 * @param boolean $closeTr indicates whether this element should close the table row
	 * @param integer $preSpacerColSpan number of padding columns to be added before the element
	 * @param integer $aftSpacerColSpan number of padding columns to be added after the element
	 * @return array Zend_Form_Element_Hidden decorators
	 */
	public function getHiddenDecorator($openTr = true, $closeTr = true, $preSpacerColSpan = 0, $aftSpacerColSpan = 0) {
		$decorator = array(
			array(array('cellLabelOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-label hidden', 'openOnly'=>true, 'placement' => 'append')),
			array(array('cellLabelCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element hidden', 'openOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			'ViewHelper',
			array(array('cellElementCloseDiv' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
		);

		return $this->addRowTags($decorator, $openTr, $closeTr, $preSpacerColSpan, $aftSpacerColSpan);
	}


	/**
	 * Returns decorators for Zend_Form_Element_Text that has a javascript date and time picker attached
	 * 
	 * @param boolean $openTr indicates whether this element should open a new table row
	 * @param boolean $closeTr indicates whether this element should close the table row
	 * @param integer $preSpacerColSpan number of padding columns to be added before the element
	 * @param integer $aftSpacerColSpan number of padding columns to be added after the element
	 * @return array Zend_Form_Element_Text decorators
	 */
	public function getDateTimeDecorator($openTr = true, $closeTr = true, $preSpacerColSpan = 0, $aftSpacerColSpan = 0) {
		$decorator = array(
			array(array('cellLabelOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-label', 'openOnly'=>true, 'placement' => 'append')),
			array('Label', array('escape' => false, 'placement' => 'append' )),
			array(array('cellLabelCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			'ViewHelper',
			array(array('Calendar' => 'HtmlTag'),array('tag'=>'img', 'src'=>'/images/ico/calendar.gif', 'alt'=>'calendar icon 88', 'class'=>'calendar-icon','placement' => 'append')),
			array('Errors', array('escape' => false)),
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description')),
			array(array('cellElementCloseDiv' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
		);

		return $this->addRowTags($decorator, $openTr, $closeTr, $preSpacerColSpan, $aftSpacerColSpan);
	}


	/**
	 * Returns decorators for Zend_Form_Element_Text that has a javascript date picker attached
	 * 
	 * @param boolean $openTr indicates whether this element should open a new table row
	 * @param boolean $closeTr indicates whether this element should close the table row
	 * @param integer $preSpacerColSpan number of padding columns to be added before the element
	 * @param integer $aftSpacerColSpan number of padding columns to be added after the element
	 * @return array Zend_Form_Element_Text decorators
	 */
	public function getDateDecorator($openTr = true, $closeTr = true, $preSpacerColSpan = 0, $aftSpacerColSpan = 0) {
		$decorator = array(
			array(array('cellLabelOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-label', 'openOnly'=>true, 'placement' => 'append')),
			array('Label', array('escape' => false, 'placement' => 'append' )),
			array(array('cellLabelCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			'ViewHelper',
			array(array('Calendar' => 'HtmlTag'),array('tag'=>'img', 'src'=>'/images/ico/calendar.gif', 'alt'=>'calendar icon', 'class'=>'calendar-icon','placement' => 'append')),
			array('Errors', array('escape' => false)),
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description')),
			array(array('cellElementCloseDiv' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append'))
		);

		return $this->addRowTags($decorator, $openTr, $closeTr, $preSpacerColSpan, $aftSpacerColSpan);
	}


	/**
	 * Returns decorators for Zend_Form_Element_Text that has a gallery image selector attached
	 * 
	 * @param boolean $openTr indicates whether this element should open a new table row
	 * @param boolean $closeTr indicates whether this element should close the table row
	 * @param integer $preSpacerColSpan number of padding columns to be added before the element
	 * @param integer $aftSpacerColSpan number of padding columns to be added after the element
	 * @return array Zend_Form_Element_Text decorators
	 */
	public function getGaleryDecorator($openTr = true, $closeTr = true, $preSpacerColSpan = 0, $aftSpacerColSpan = 0, $fieldId='imageName') {
		$decorator = array(
			array(array('cellLabelOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-label', 'openOnly'=>true, 'placement' => 'append')),
			array('Label', array('escape' => false, 'placement' => 'append' )),
			array(array('cellLabelCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			array(array('cellElementOpenDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
			'ViewHelper',
			array(array('openTrA' => 'HtmlTag'), array('tag' => 'a', 'openOnly'=>true, 'placement' => 'append', 'href' => "javascript:window.open('/galery.php?type=1&node=".$fieldId."','','width=640,height=480,scrollbars=yes');void(0);")),
			array(array('Gallery' => 'HtmlTag'),array('tag'=>'img', 'src'=>'_clip/image_aktivni.gif', 'alt'=>'galery icon', 'class'=>'calendar-icon','placement' => 'append')),
			array(array('closeA' => 'HtmlTag'), array('tag' => 'a', 'closeOnly'=>true, 'placement' => 'append')),
			array('Errors', array('escape' => false)),
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description')),
			array(array('cellElementCloseDiv' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
			array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
		);

		return $this->addRowTags($decorator, $openTr, $closeTr, $preSpacerColSpan, $aftSpacerColSpan);
	}


	/**
	 * Wrapps the decorated Zend_Form_Element in tr tags and adds spacer columns
	 * 
	 * @param boolean $openTr indicates whether this element should open a new table row
	 * @param boolean $closeTr indicates whether this element should close the table row
	 * @param integer $preSpacerColSpan number of padding columns to be added before the element
	 * @param integer $aftSpacerColSpan number of padding columns to be added after the element
	 * @return array Zend_Form_Element decorators
	 */
	private function addRowTags($content, $openTr = true, $closeTr = true, $preSpacerColSpan = 0, $aftSpacerColSpan = 0) {
		$preTags = array();
		$postTags = array();

		if($openTr === true)
			$preTags[] = array(array('rowOpenTr' => 'HtmlTag'), array('tag' => 'tr', 'openOnly'=>true, 'placement' => 'append'));
		if($preSpacerColSpan > 0)
			$preTags[] = array(array('spacer1' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'append', 'colspan' => $preSpacerColSpan));

		if($aftSpacerColSpan > 0)
			$postTags[] = array(array('spacer2' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'append', 'colspan' => $aftSpacerColSpan));
		if($closeTr === true)
			$postTags[] = array(array('rowCloseTr' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly'=>true, 'placement' => 'append'));

		return array_merge($preTags, $content, $postTags);
	}


	/**
	 * Returns decorators for Zend_Form_DisplayGroup
	 * 
	 * @return array Zend_Form_DisplayGroup decorators
	 */
	public function getDisplayGroupDecorator() {
		$decorators = array(
			'Description',
			array(array('tableTagOpen' => 'HtmlTag'), array('tag' => 'table', 'openOnly'=>true, 'class' => 'form', 'placement'=>'append')),
			array(array('tbodyTagOpen' => 'HtmlTag'), array('tag' => 'tbody', 'openOnly'=>true, 'placement'=>'append')),
			'FormElements',
			array(array('tbodyTagClose' => 'HtmlTag'), array('tag' => 'tbody', 'closeOnly'=>true,  'placement'=>'append')),
			array(array('tableTagClose' => 'HtmlTag'), array('tag' => 'table', 'closeOnly'=>true, 'class' => 'form',  'placement'=>'append')),
			'Fieldset',
		);

		return $decorators;
	}


	/**
	 * Returns decorators for Zend_Form_SubForm
	 * 
	 * @param array $options elements can have values
	 * - 'no-table'
	 * @return array Zend_Form_SubForm decorators
	 */
	public function getSubFormDecorator($options=array()) {
		if(!in_array('no-table', $options))
			$decorators[] = array(array('tableTagOpen' => 'HtmlTag'), array('tag' => 'table', 'openOnly'=>true, 'class' => $this->tableClass, 'placement'=>'append'));

		$decorators[] = array(array('rowOpenTr' => 'HtmlTag'), array('tag' => 'tr', 'openOnly'=>true, 'placement' => 'append'));
		$decorators[] = array('Description', array('tag' => 'th', 'escape' => false, 'class' => 'description', 'colspan' => '2'));
		$decorators[] = array(array('rowCloseTr' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly'=>true, 'placement' => 'append'));
		$decorators[] = 'FormElements';

		if(!in_array('no-table', $options))
			$decorators[] = array(array('tableTagClose' => 'HtmlTag'), array('tag' => 'table', 'closeOnly'=>true, 'class' => 'form',  'placement'=>'append'));

		return $decorators;
	}
}
