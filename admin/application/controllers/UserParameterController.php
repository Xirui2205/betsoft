<?php

class UserParameterController extends controllers_UserAbstractController {

	protected $viewSectionId = 239;
	protected $updateSectionId = 223;
	protected $userId;
	protected $ws;


	public function init() {
		parent::init();

		$this->jsIncludes->parameterAjax = true;
		
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_SUPERVISION))
			$this->view->supervisionLogged = true;
		else
			$this->view->supervisionLogged = false;
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$parameter = $this->ws->Parameter->getByUserId( (integer)$this->userId );
		$this->view->parameter = $parameter;
	}



	public function updateAction() {

		$this->_helper->layout->setLayout('empty');
		$this->view->ws			= $this->ws;
		$this->view->userId		= $this->userId;


		$parameters			= $this->ws->Parameter->getByUserId( (integer)$this->userId );
		$userParameterForm	= new Models_Form_UserParameter($this->updateSectionId);


		foreach($parameters as $param){
			if($param['isDefault'] == 1){
				$userParameterForm->getElement('isDefault'.$param['parameterId'])->setChecked(true);
				$userParameterForm->getElement('param'.$param['parameterId'])->setAttrib('disabled','disabled');
			}
		}


		$parameters					= It6_ArrayWrapper::toNativeArray($parameters);
		$parametersParsed			= Models_Utils::parseParameters($parameters, 'parameterId');
		$parametersParsed['userId']	= $this->userId;


		$userParameterForm->populate($parametersParsed);
		$this->view->userParameterForm = $userParameterForm;


		if ($this->getRequest()->getPost('save')) {

			if (!$userParameterForm->isValid( $this->getRequest()->getPost() ))
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));

			else {
				$values = $userParameterForm->getValues();

				foreach($values as $valName => $valVal){
					if(substr($valName, 0, 5) == 'param'  &&  !empty($valVal)){
						$paramValues[str_replace('param','',$valName)] = $valVal;
					}
					else if(substr($valName, 0, 9) == 'isDefault'  &&  $valVal==1)
						$resetValues[] = str_replace('isDefault','',$valName);
				}


				//get local params for branch as reference to see what has changed
				$userLocalParams = $this->ws->Parameter->getLocalUserParametersByUserId($this->userId);
				$userLocalParams = It6_ArrayWrapper::toNativeArray($userLocalParams);
				foreach($parameters as $param) {
					$parameterNames[$param['parameterId']] = $param['name'];
				}
				if(!empty($paramValues)) {
					if ( $this->ws->Parameter->updateUserParameters($paramValues, (integer)$this->userId)) {
						//prepare values to be loged for parameters that were changed to a value
						foreach($paramValues as $key => $param) {
							if(!isset($userLocalParams[$key])) {
								$parameters4log[$key]['old value'] = 'default';
								$parameters4log[$key]['new value'] = $param;
								$parameters4log[$key]['param name'] = $parameterNames[$key];
							}
							else if($userLocalParams[$key]['value'] != $param) {
								$parameters4log[$key]['old value'] = $userLocalParams[$key]['value'];
								$parameters4log[$key]['new value'] = $param;
								$parameters4log[$key]['param name'] = $parameterNames[$key];
							}
						}
					}
					else
						$actionFail = true;
				}

				if(!empty($resetValues)) {
					if ( $this->ws->Parameter->resetUserParameters($resetValues, (integer)$this->userId)) {
						//prepare values to be loged for parameters that were changed to default
						$toDefParams = array_intersect($resetValues, array_keys($userLocalParams));
						foreach($toDefParams as $toDefId) {
							$parameters4log[$toDefId]['old value'] = $userLocalParams[$toDefId]['value'];
							$parameters4log[$toDefId]['new value'] = 'default';
							$parameters4log[$toDefId]['param name'] = $parameterNames[$toDefId];
						}
					}
					else
						$actionFail = true;
				}

				if(empty($actionFail)) {
					if(!empty($parameters4log)){
						ksort($parameters4log);
						It6_Log::info(
							'User parameters updated',
							It6_Log::TAG_ADMIN_OPERATION,
							$parameters4log
						);
					}
					$this->view->feedbackMsg	= UiUtil::printMessages(array('update-ok'));
					$this->view->parameter 		= $this->ws->Parameter->getByUserId( (integer)$values['userId'] );

					return $this->render('view');
				}
			}

			$this->view->feedbackMsg = UiUtil::printErrors(array('update-error'));
			$this->view->userParameterForm = $userParameterForm;
		}
	}
}
