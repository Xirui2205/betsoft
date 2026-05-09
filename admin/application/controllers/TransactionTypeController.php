<?php

class TransactionTypeController extends It6_Controller_Abstract {

	private $indexSectionId = 263;
	private $updateFeeSectionId = 264;
	private $updateLimitSectionId = 265;


	public function init() {
		parent::init();
		$this->jsIncludes->transactionTypeAjax	= true;
		$this->jsIncludes->commonAjax	= true;
		$this->view->updateFeeSectionId = $this->updateFeeSectionId;
		$this->view->updateLimitSectionId = $this->updateLimitSectionId;
	}

	public function indexAction() {
		$transactionTypes = Models_TransactionType::getEditableTypes();

		if (!empty($transactionTypes)) {
			if (!empty($transactionTypes['feeEditable']))
				$this->view->feeEditable = $transactionTypes['feeEditable'];
			if (!empty($transactionTypes['limitEditable']))
				$this->view->limitEditable = $transactionTypes['limitEditable'];
		}
	}

	public function updateFeeAction() {
		$this->_helper->_layout->setLayout('empty');

		$type = Models_TransactionType::getFeesById($this->getRequest()->getPost('transactionTypeId'));
		$form = new Models_Form_TransactionTypeFee($this->updateFeeSectionId, $this->indexSectionId);
		$values = $this->getRequest()->getPost();
		if(isset($values['save'])) {
			if($form->isValid($values)) {
				//attrib set so that the filter direction is web2db
				$form->setAttrib(It6_Filter::ATTR_WEB2DB, 'true');
				$values = $form->getValues();
				unset($values['save']);
				if(Models_TransactionType::updateFees($values)) {
					$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
					//attrib removed so that the filter direction is db2we
					$form->removeAttrib(It6_Filter::ATTR_WEB2DB);
				}
				else
					$this->view->feedbackMsg = UiUtil::printErrors( array('updateError') );
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors( array('form-not-valid') );
		}
		else
			$form->populate(It6_ArrayWrapper::toNativeArray($type));

		$this->view->form = $form;
		$this->view->type = $type;
		$this->render('update');
	}



	public function updateLimitAction() {
		$this->_helper->_layout->setLayout('empty');

		$type = Models_TransactionType::getFeesById($this->getRequest()->getPost('transactionTypeId'));
		$form = new Models_Form_TransactionTypeLimit($this->updateLimitSectionId, $this->indexSectionId);
		$values = $this->getRequest()->getPost();
		if(isset($values['save'])) {
			if($form->isValid($values)) {
				//attrib set so that the filter direction is web2db
				$form->setAttrib(It6_Filter::ATTR_WEB2DB, 'true');
				$values = $form->getValues();
				unset($values['save']);
				if(Models_TransactionType::updateLimits($values)) {
					$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
					//attrib removed so that the filter direction is db2we
					$form->removeAttrib(It6_Filter::ATTR_WEB2DB);
				}
				else
					$this->view->feedbackMsg = UiUtil::printErrors( array('updateError') );
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors( array('form-not-valid') );
		}
		else
			$form->populate(It6_ArrayWrapper::toNativeArray($type));

		if($type['highLimit'] == null) {
			$form->getElement('highLimitCheckbox')->setValue(1);
			$form->getElement('highLimit')->setAttrib('disabled', 'disabled');

		}
		if($type['lowLimit'] == null) {
			$form->getElement('lowLimitCheckbox')->setValue(1);
			$form->getElement('lowLimit')->setAttrib('disabled', 'disabled');
		}

		$this->view->form = $form;
		$this->view->type = $type;
		$this->render('update');
	}
}
