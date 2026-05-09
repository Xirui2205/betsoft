<?php

class BranchContractController extends controllers_BranchAbstractController {

	var $viewSectionId = 165;
	var $updateSectionId = 168;



	public function init() {
		parent::init();
	}



	public function indexAction() {
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->contract = Models_Branch::getContractInfo($this->branchId);
		$this->view->branchId = $this->branchId;
	}



	public function updateAction() {

		$this->_helper->layout->setLayout('empty');
		$this->view->ws					= $this->ws;
		$indata							= $this->getRequest()->getPost();

		if(isset($indata['save']))
			$action						= $indata['save'];

		$this->view->branchId			= $this->branchId;
		$this->view->contractTemplates	= $this->ws->ContractTemplate->getAll();
		$openContractForms				= array();

		$contract 						= Models_Branch::getContractInfo($this->branchId);

		if(isset($contract['openContract'])) {
			$i = 0;
			foreach($contract['openContract'] as $cont){
				$openContractForms[$i] = new Models_Form_BranchContract($this->updateSectionId, $cont['templateId'], 'open'.$i, 'update');

				foreach($cont['parameters'] as $param){
					if($param['isDefault'] == 1){
						$openContractForms[$i]->getElement('isDefault'.$param['parameterId'])->setChecked(true);
						$openContractForms[$i]->getElement('param'.$param['parameterId'])->setAttrib('disabled','disabled');
					}
				}

				$openContractForms[$i]->populate($cont);
				$i++;
			}
		}

		if(isset($contract['activeContract'])) {
			$this->view->activeContractForm = new Models_Form_BranchActiveContract($this->updateSectionId);
			$this->view->activeContractForm->populate($contract['activeContract']);
		}
		else if (isset($contract['signedNAContract'])) {
			$this->view->signedNAContractForm = new Models_Form_BranchActiveContract($this->updateSectionId);
			$this->view->signedNAContractForm->populate($contract['signedNAContract'][0]);
		}

		$this->view->contract = $contract;
		$this->view->branchId = $this->branchId;



		if (isset($indata['save'])) {

			if(isset($indata['templateId']))
				$form = new Models_Form_BranchContract($this->updateSectionId, $indata['templateId'], $indata['formId'], $indata['save']);
			else
				$form = new Models_Form_BranchActiveContract($this->updateSectionId);

			if (!$form->isValid( $indata )){
				$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('error_form_not_valid_general'));
				if($indata['formId'] == 'new')
					$this->view->newFormFix = $form;
				else{
					$formKey = str_replace('open','',$indata['formId']);
					$openContractForms[$formKey] = $form;
				}
			}

			else {
				$values = $form->getValues();

				if(!isset($values['dateCanceled']))
					$values['dateCanceled'] = '';

				if(isset($values['contractId'])) {
					$outdata['contractId'] = $values['contractId'];
					unset($values['contractId']);
				}
				if(isset($values['dateValidFrom'])) {
					$outdata['dateValidFrom'] = It6_Date::toDbAsDate($values['dateValidFrom']);
					unset($values['dateValidFrom']);
				}
				if(isset($values['dateValidTo'])) {
					$outdata['dateValidTo'] = It6_Date::toDbAsDate($values['dateValidTo']);
					unset($values['dateValidTo']);
				}
				if(isset($values['dateSigned'])) {
					$outdata['dateSigned'] = It6_Date::toDbAsDate($values['dateSigned']);
					unset($values['dateSigned']);
				}
				if(isset($values['dateCanceled'])) {
					$outdata['dateCanceled'] = It6_Date::toDbAsDate($values['dateCanceled']);
					unset($values['dateCanceled']);
				}
				if(isset($values['templateId'])) {
					$outdata['templateId'] = $values['templateId'];
					unset($values['templateId']);
				}
				if(isset($values['branchId'])) {
					$outdata['branchId'] = $values['branchId'];
					unset($values['branchId']);
				}

				$i=0;
				$resetIds = array();
				foreach($values as $fieldName => $fieldValue){
					if(substr($fieldName, 0, 9) != 'isDefault') {
						$outdata['parameters'][$i]['parameterId'] = str_replace('param', '',$fieldName);
						$outdata['parameters'][$i]['value'] = $fieldValue;
						$i++;
					}
					else if(substr($fieldName, 0, 9) == 'isDefault'  &&  $fieldValue == 1) {
						$resetIds[] = str_replace('isDefault', '',$fieldName);
					}
				}


				if($action == 'insert'){
					$res = $this->ws->Contract->insert($outdata);
					if(is_numeric($res)){
						$this->view->feedbackMsg = UiUtil::printMessages(I18n::tr('form_insert_success_general'));
						It6_Log::info(
							"Branch contract inserted successfuly.",
							It6_Log::TAG_ADMIN_OPERATION,
							$outdata
						);
						$this->view->contract = Models_Branch::getContractInfo($this->branchId);
						echo $this->render('view');
						return;
					}
					$this->view->feedbackMsg = UiUtil::printErrors(array('insert-error - ' . i18n::tr($res)));
					It6_Log::warn(
						"Branch contract insertion failed.",
						It6_Log::TAG_ADMIN_OPERATION,
						$outdata
					);
				}

				else if($action == 'update'){
					$res = $this->ws->Contract->update($outdata);
					if(is_numeric($res)){
						if($this->ws->Contract->resetContractParameters( $resetIds, (integer)$outdata['contractId'] )) {
							$this->view->feedbackMsg = UiUtil::printMessages(I18n::tr('form_update_success_general'));
							It6_Log::info(
								"Branch contract updated successfuly.",
								It6_Log::TAG_ADMIN_OPERATION,
								$outdata
							);
							$this->view->contract = Models_Branch::getContractInfo($this->branchId);
							echo $this->render('view');
							return;
						}
					}
					else {
						$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('form_update_error_general'));
						It6_Log::warn(
							"Branch contract update failed.",
							It6_Log::TAG_ADMIN_OPERATION,
							$outdata
						);
					}
				}


				$this->view->openContractForms = $openContractForms;
				if(isset($indata['formId'])) {
					if($indata['formId'] == 'new')
						$this->view->newFormFix = $form;
					else{
						$formKey = str_replace('open','',$indata['formId']);
						$openContractForms[$formKey] = $form;
					}
				}
			}
		}

		$this->view->openContractForms = $openContractForms;
	}




	public function cancelContractAction(){
		$this->_helper->layout->setLayout('empty');

		$data = $this->ws->Contract->getActiveByBranch( (integer)$this->branchId );
		$data = It6_ArrayWrapper::toNativeArray($data);
		$data[0]['dateCanceled']	= It6_Date::dbNowAsDate();
		$data[0]['parameters']		= array();


		if($this->ws->Contract->update($data[0])) {
			$this->view->feedbackMsg = UiUtil::printMessages(I18n::tr('contract_cancel_ok'));
			It6_Log::info(
				"Branch contract canceled successfuly.",
				It6_Log::TAG_ADMIN_OPERATION,
				$outdata
			);
		}
		else {
			$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('contract_cancel_error'));
			It6_Log::warn(
				"Branch contract cancelation failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				$outdata
			);
		}


		$this->view->contract = Models_Branch::getContractInfo($this->branchId);
		$this->view->branchId = $this->branchId;

		echo $this->render('view');
	}



	public function getNewFormAction(){
		$this->_helper->layout->setLayout('empty');
		$this->getHelper('ViewRenderer')->setNoRender();

		$templateId = $this->getrequest()->getPost('templateId');
		if(is_numeric($templateId)){
			$form = new Models_Form_BranchContract($this->updateSectionId, $templateId, 'new', 'insert');
			$form->populate(array('branchId'=>$this->branchId));

			echo $form;
		}
	}
}
