<?php

class BranchParameterController extends controllers_BranchAbstractController {

	var $viewSectionId = 166;
	var $updateSectionId = 167;
	var $deleteSectionId = 372;


	public function init() {
		parent::init();
	}

	public function indexAction() {
	}

	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->parameter = $this->ws->Parameter->getByBranchId( (integer)$this->branchId );
		$this->view->branchId = $this->branchId;
	}

	public function infoAction() {
		$this->_helper->layout->disableLayout();
		$paramId = $this->getRequest()->getPost('paramId');
		$branchId = $this->getRequest()->getPost('branchId');
		$result = array('errors' => array(), 'infos' => array());
		if (!empty($paramId)) {
				$result = It6_ArrayWrapper::toNativeArray(Zend_Registry::get('ws')->Parameter->info($paramId, $branchId));
		} else {
			$result['errors'][] = "no data"; 
		}
		$this->view->result = Zend_Json::encode($result);
	}

	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->branchId = $this->branchId;
        $data = $this->getRequest()->getPost();

        if (trim($data['name']) == '') {
	    	$this->view->feedbackMsg = UiUtil::printErrors( array('Parameter name requires a value') );
        } else {       
		 	// Insert parameter
		 	$insertedId = $this->ws->Parameter->newBranchParameter($data);
			if ((int)$insertedId > 0) {
			 	// Insert to branch_has_parameter
			 	$this->ws->Parameter->newBranchHasParameter($this->view->branchId , $insertedId, $data['value']);
				$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );			
				// Log
				$this->ws->Parameter->logParameters($insertedId, $this->view->branchId, null, null, 'Insert', 'New parameter "'.$data['name'].'" was inserted');
			} else {
			    $this->view->feedbackMsg = UiUtil::printErrors( array($insertedId) );
			}
	    }	        	 	
	    $this->view->parameter = $this->ws->Parameter->getByBranchId( (integer)$this->branchId );	        
		return $this->render('view');
	}

	public function updateAction() {

		$this->_helper->layout->setLayout('empty');
		$this->view->ws			= $this->ws;
		$this->view->branchId	= $this->branchId;

		$parameters				= $this->ws->Parameter->getByBranchId( (integer)$this->branchId );
		$branchParameterForm	= new Models_Form_BranchParameter($this->updateSectionId, (integer)$this->branchId);

		foreach($parameters as $param){
			if($param['isDefault'] == 1){
				$branchParameterForm->getElement('isDefault'.$param['parameterId'])->setChecked(true);
				$branchParameterForm->getElement('param'.$param['parameterId'])->setAttrib('disabled','disabled');
			}
		}

		$parameters					  = It6_ArrayWrapper::toNativeArray($parameters);
		$parametersParsed			  = Models_Utils::parseParameters($parameters, 'parameterId');
		$parametersParsed['branchId'] = $this->branchId;

		$aParametersOldValue = array();
		foreach($parameters as $param) {
			$aParametersOldValue[$param['parameterId']] = $param['value'];
		}

		$branchParameterForm->populate($parametersParsed);
		$this->view->branchParameterForm = $branchParameterForm;

		if ($this->getRequest()->getPost('save')) {

			if (!$branchParameterForm->isValid( $this->getRequest()->getPost() ))
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));

			else {
				$values			= $branchParameterForm->getValues();
				$paramValues	= array();
				$resetValues	= array();

				foreach($values as $valName => $valVal){
					if(substr($valName, 0, 5) == 'param'  &&  $valVal != null){
						$paramValues[str_replace('param','',$valName)] = $valVal;
					}
					else if(substr($valName, 0, 9) == 'isDefault'  &&  $valVal==1)
						$resetValues[] = str_replace('isDefault','',$valName);
				}

				//get local params for branch as reference
				$branchLocalParams = $this->ws->Parameter->getLocalBranchParametersByBranchId($this->branchId);
				$branchLocalParams = It6_ArrayWrapper::toNativeArray($branchLocalParams);
				foreach($parameters as $param) {
					$parameterNames[$param['parameterId']] = $param['name'];
				}

				foreach($paramValues as $key => $param) {
					if ( $param != $aParametersOldValue[$key] ) {
						if($this->ws->Parameter->updateBranchParameters(array($key => $param), (integer) $this->branchId)) {
							$parameters4log[$key]['old value'] = $aParametersOldValue[$key];
							$parameters4log[$key]['new value'] = $param;
							$parameters4log[$key]['param name'] = $parameterNames[$key];
							// Log
							$this->ws->Parameter->logParameters($key, $this->branchId, $aParametersOldValue[$key], $param, 'Change value', '<b style="color:yellow;">Changed</b> parameter value ( Branch ID: '.$this->branchId.' )');
						} else {
							$actionFail = true;
						}
					}
				}

				if(empty($actionFail)) {
					if(!empty($parameters4log)) {
						ksort($parameters4log);
						It6_Log::info(
							'Branch parameters updated',
							It6_Log::TAG_ADMIN_OPERATION,
							$parameters4log
						);
					}
					$this->view->feedbackMsg	= UiUtil::printMessages(array('update-ok'));
					$this->view->parameter 		= $this->ws->Parameter->getByBranchId( (integer)$values['branchId'] );

					return $this->render('view');
				}
			}

			$this->view->feedbackMsg = UiUtil::printErrors(array('update-error'));
			$this->view->$branchParameterForm = $branchParameterForm;
			return $this->render('view');
		}
	}

	public function deleteParamAction() {
		$this->_helper->layout->setLayout('empty');
		$paramId = $this->getRequest()->getPost('paramId');
		$this->branchId	= $this->getRequest()->getPost('branchId');
		// Delete parameter
		if ( $this->ws->Parameter->deleteBranchHasParameter($paramId, $this->branchId) ) {
			$this->view->feedbackMsg = UiUtil::printMessages( array('delete-ok') );			
			// Log
			$this->ws->Parameter->logParameters($paramId, $this->branchId, null, null, 'Delete', '<b style="color:red;">Delete</b> parameter ( Branch ID:'.$this->branchId.' )');
		}

		$this->view->parameter = $this->ws->Parameter->getByBranchId( (integer)$this->branchId );		
		return $this->render('delete');
	}

}