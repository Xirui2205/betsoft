<?php

class SoundsSettingsController extends It6_Controller_Abstract {

	protected $_redirector = null;

	public function init(){
		parent::init();
		$this->_redirector = $this->_helper->getHelper('Redirector');
	}

	public function indexAction() {
		$inData	= $this->getRequest()->getParams();
		
		$form = new Models_Form_SoundsSettings();
        $formValues = $form->getValues();

 		if (isset($inData["save"])) {
			if (!$form->isValid( $this->getRequest()->getPost() )) {
				$this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr('error_form_not_valid_general')));
			} else {
		        $i = 1;
		        foreach ($formValues as $filename) {
		        	if ($filename) {
		        	  $fname = 'fileName'.$i;
		              ${"fullPathLevel".$i} = $form->$fname->getFileName();
		              Zend_Registry::get('zdb_admin')->query('update parameter set value="'.$formValues[$fname].'" where name=?',"confirmation.sound.level".$i);
		            }
		        	$i++;
		        }
      		    $this->_redirect('?section=352');
			}
        }

		$this->view->form = $form;
	}

}