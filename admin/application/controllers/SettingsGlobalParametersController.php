<?php

class SettingsGlobalParametersController extends It6_Controller_Abstract {


	public function init() {
		$this->ws = Zend_Registry::get('ws');
		parent::init();
		$this->jsIncludes->parameterAjax = true;
		$this->jsIncludes->commonAjax = true;
	}

	public function indexAction() {
		$this->view->systemParameters = $this->ws->Parameter->getDefaultSystemParameters();
		$this->view->branchParameters = $this->ws->Parameter->getDefaultBranchParameters();
		$this->view->userParameters = $this->ws->Parameter->getDefaultUserParameters();
	}

	public function infoBranchAction() {
		$this->_helper->layout->disableLayout();
		$paramId = $this->getRequest()->getPost('paramId');
		$result = array('errors' => array(), 'infos' => array());
		if (!empty($paramId)) {
				$result = It6_ArrayWrapper::toNativeArray(Zend_Registry::get('ws')->Parameter->info($paramId, null));
		} else {
			$result['errors'][] = "no data"; 
		}
		$this->view->result = Zend_Json::encode($result);
	}

	public function viewSystemAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->systemParameters = $this->ws->Parameter->getDefaultSystemParameters();
	}

	public function viewBranchAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->branchParameters = $this->ws->Parameter->getDefaultBranchParameters();
	}

	public function viewUserAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->userParameters = $this->ws->Parameter->getDefaultUserParameters();
	}

	public function insertBranchAction() {
		$this->_helper->layout->setLayout('empty');
        $data = $this->getRequest()->getParams();
        $data['type'] = 0;
        if (trim($data['name']) == '') {
	    	$this->view->feedbackMsg = UiUtil::printErrors( array('Parameter name requires a value') );
        } else {       
		 	// Insert parameter
		 	$insertedId = $this->ws->Parameter->newBranchParameter($data);
			if ((int)$insertedId > 0) {
				$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );			
				// Log
				$this->ws->Parameter->logParameters($insertedId, null, null, null, 'Insert', 'New parameter "'.$data['name'].'" was inserted');
			} else {
			    $this->view->feedbackMsg = UiUtil::printErrors( array($insertedId) );
			}
	    }	        		 	
		$this->view->branchParameters = $this->ws->Parameter->getDefaultBranchParameters();
		return true;
	}

	public function deleteBranchAction() {
		$this->_helper->layout->setLayout('empty');
        $data = $this->getRequest()->getParams();
        $paramId = $data['paramId'];

		// Delete parameter
		if ($this->ws->Parameter->deleteBranchParameter($paramId)) {
			$this->view->feedbackMsg = UiUtil::printMessages( array('delete-ok') );			
			// Log
			$this->ws->Parameter->logParameters($paramId, null, null, null, 'Delete', '<b style="color:red;">Delete</b> parameter ID:'.$paramId);
		}
		 	
		$this->view->branchParameters = $this->ws->Parameter->getDefaultBranchParameters();
		return true;
	}

	public function updateBranchAction() {

		$parameters	= $this->ws->Parameter->getDefaultBranchParameters();
		$parameters = It6_ArrayWrapper::toNativeArray($parameters);
		$form		= new Models_Form_DefaultParameter('Branch',209, 'parameter-branch');

		if ($this->updateGeneric($parameters, $form)){
			$this->view->branchParameters = $this->ws->Parameter->getDefaultBranchParameters();
			return $this->render('view-branch');
		}

		return $this->render('update-branch');
	}



	public function updateUserAction() {
		$parameters	= $this->ws->Parameter->getDefaultUserParameters();
		$parameters = It6_ArrayWrapper::toNativeArray($parameters);
		$form		= new Models_Form_DefaultParameter('User', 210, 'parameter-user');

		if ($this->updateGeneric($parameters, $form)){
			$this->view->userParameters = $this->ws->Parameter->getDefaultUserParameters();
			return $this->render('view-user');
		}

		return $this->render('update-user');
	}



	public function updateSystemAction() {
		$parameters	= $this->ws->Parameter->getDefaultSystemParameters();
		$parameters = It6_ArrayWrapper::toNativeArray($parameters);
		$form		= new Models_Form_DefaultParameter('System', 251, 'parameter-system');

		if($this->updateGeneric($parameters, $form)){
			$this->view->systemParameters = $this->ws->Parameter->getDefaultSystemParameters();
			return $this->render('view-system');
		}

		return $this->render('update-system');
	}



	private function updateGeneric($parameters, $form) {
		$this->_helper->layout->setLayout('empty');
		$data = array();

		foreach($parameters as $key => $param) {
			$data['param'.$key] = $param['value'];
			if($param['isEditable'] == 0)
				$form->getElement('param'.$key)->setAttrib('disabled','disabled');
		}

		$form->populate($data);
		$this->view->form = $form;

		if($this->getRequest()->getPost('save')) {

			if(!$form->isValid($this->getRequest()->getPost()))
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));

			else {
				$values		= $form->getValues();
				$outdata	= array();

				foreach($values as $key => $val) {
					$outdata[str_replace('param','',$key)] = $val;
				}
				unset($outdata['save']);

				//this retrieves the old parameters, therefore it has to be before we update them
				$filterDef[] = array(array('?'=> array('parameterId' => array_keys($outdata)), 'OP' => 'IN (?)'));
				$filter = new It6_WsExtension_Client_Filter('filter', $filterDef);
				$columns = new It6_WsExtension_Client_Columns('columns', array('parameterId','name', 'value'));
				$extensions = array($filter, $columns);
				$parameters = $this->ws->ext($extensions)->Parameter->getAll();

				//update the params
				if ($this->ws->Parameter->updateDefaultParameters($outdata)){
					$this->view->feedbackMsg	= UiUtil::printMessages( array('update-ok') );

					//figure out what to log
					foreach($parameters as $key => $param) {
						if($param['value'] == $outdata[$param['parameterId']])
							unset($parameters[$key]);
						else {
							$parameters[$key]['new value'] = $outdata[$param['parameterId']];
							$parameters[$key]['old value'] = $param['value'];
             				$this->ws->Parameter->logParameters($param['parameterId'], null, $param['value'], $outdata[$param['parameterId']], 'Change value', '<b style="color:yellow;">Changed</b> parameter value');
							unset($parameters[$key]['value']);
						}
					}

					//log what was changed
					It6_Log::info(
						'Defualt gloabal parameters updated',
						It6_Log::TAG_ADMIN_OPERATION,
						$parameters
					);

					return true;
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('update-error'));
					return false;
				}
			}
		}
	}
}
