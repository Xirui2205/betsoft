<?php

class ParametersSettingsController extends controllers_ParamsAbstractController {

	const MODE_DEFAULT = 1;
	const MODE_BOOKMAKER = 2;

	protected $updateSectionId		    = 357; // tamok - pridano
	protected $viewSectionId		    = 356; // tamok - pridano
	protected $indexSectionId		    = 354;
	protected $insertSectionId		    = 358; // tamok - pridano
	protected $exportSectionId		    = 299;
	protected $updateBmSectionId	    = 320;
	
	protected $id;
	protected $ws;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array('nazev ASC');

	private static $TBODY_LAYOUT = 'params-tbody';

	public function init() {
		parent::init();
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_CALLCENTRUM))
			$this->view->callcenterLogged = true;
		else 
			$this->view->callcenterLogged = false;
		
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_SUPERVISION))
			$this->view->supervisionLogged = true;
		else
			$this->view->supervisionLogged = false;
	}



	public function indexAction($mode = self::MODE_DEFAULT) {
		$inData			= $this->getRequest()->getParams();
		$extensions		= array();
		
		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		// make possible to select user from GET param
		if (!isset($this->filterData['id'])) {
			$id = intval($this->getRequest()->getParam('user_id'));
			if (!empty($id)) {
				$this->filterData['id'] = $inData['filter']['id'] = $id;
			}
		}

		//create filter
		$sexOptions		= array('0'=>i18n::tr('Any'), 'm'=>i18n::tr('Male'), 'f'=>i18n::tr('Female'));
		$langOptions	= Models_Utils::getSelectOptions($this->ws->Language->getAll(), 'languageId', 'name');
		$langOptions[0]	= i18n::tr('Any');
		ksort($langOptions);
				
		$filterCfg = array(
			array(i18n::tr('nazev parametru'), 'nazev', 'text', array(array('nazev', 'LIKE','%?%')), 'Zend_Validate_Alnum', array('allowWhiteSpace' => true)),
		);
		$filter = new It6_WsForm_Filter($filterCfg);
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$tableCfg = array(
			array(null, null),
			//array('id', 'id'),
			array('nazev', 'nazev'),
			array('mandatory', 'mandatory'),
			array('nasobnost', 'nasobnost'),
			array('dt_nazev', 'dt_nazev'),
		);
		if (self::MODE_BOOKMAKER == $mode) {
			array_splice($tableCfg, 7, 1);
			array_shift($tableCfg);
		}
		$table = new It6_WsForm_Table($tableCfg);
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);		
		if ( !empty($inData) ) {
			$params = $this->ws->ext($extensions)->Params->getAll();
			$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		}
		else {
			$params = array();
			$this->view->notFilter = true;
			$this->view->paginator = false;
		}
		$params = It6_ArrayWrapper::toNativeArray($params);

		$privileges = array();
		//$this->view->shoda		= new UserTrack();
		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(
			self::$TBODY_LAYOUT, $params,  array('modeBookmaker' => (self::MODE_BOOKMAKER == $mode))
		);
	}

	public function viewAction($mode = self::MODE_DEFAULT) {
		$this->_helper->layout->setLayout('empty');
		$this->view->params = It6_ArrayWrapper::toNativeArray($this->ws->Params->getById( (integer)$this->paramId ));
		$this->view->privileges = It6_ArrayWrapper::toNativeArray($this->ws->Params->getParameterPrivileges( (integer)$this->paramId ));
	}

	public function updateAction($mode = self::MODE_DEFAULT) {
		$this->_helper->layout->setLayout('empty');
        
		$form = new Models_Form_ParametersSettings($this->updateSectionId, $this->paramId);

		$pId = $form->createElement('hidden', 'id')->setValue($this->paramId);
		$form->addElement($pId);

		if ($this->paramId) {
			$data = $this->ws->Params->getById( (integer)$this->paramId );
			$data = It6_ArrayWrapper::toNativeArray($data);
			$form->populate(It6_ArrayWrapper::toNativeArray($data));
		}

		if ($this->getRequest()->getPost('submit')) {
			
			$data = $this->getRequest()->getPost();
            $this->paramId = $data['id'];

			if ($form->isValid($data)) {
				$data = $form->getValues();
				if ($this->ws->Params->update($data)) {
	  	            $this->ws->Params->updateParameterPrivileges( (integer)$this->paramId, $data );
					$this->view->feedbackMsg = UiUtil::printMessages( I18n::tr('form_update_success_general') );
					$form->removeAttrib(It6_Filter::ATTR_WEB2DB);
					$params = Zend_Registry::get('ws')->Params->getById($this->paramId);
					$this->view->params = $params;
					$this->render('view');
				} else {
					$this->view->feedbackMsg = UiUtil::printErrors( I18n::tr('form_update_error_general') );
				}
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors( I18n::tr('error_form_not_valid_general') );
			}
		}

		$form->populate($data);

		$this->view->form = $form;
	}



	public function insertAction() {
		$this->_helper->layout->setLayout('empty');

		$paramsForm	= new Models_Form_ParametersSettings($this->insertSectionId);

		if ($this->getRequest()->getPost('submit')) {

			if (!$paramsForm->isValid( $this->getRequest()->getPost() )) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));	
			} else {
				$values = $paramsForm->getValues();
				unset($values['save']);
				if ($paramsForm = $this->ws->Params->insert($values)) {
	  	            $this->ws->Params->insertParameterPrivileges($values, $paramsForm);
					$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );
					$this->view->feedbackMsg .= '<br/><button onclick="editParamDetail('.$newParamId.')">'.i18n::tr('Continue creating user').'</button>';
					It6_Log::info(
						"Param '%newParamId%' was inserted.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('newParamId' => $newParamId, 'data'=>  Zend_Json::encode($values))
					);
				} else {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error') );
					It6_Log::err(
						"Param insert error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			}
		}	
		$this->view->paramsForm = $paramsForm;
	}

	public function exportAction() {
		$this->_helper->layout->setLayout('empty');

		$userExportForm	= new Models_Form_UserExport($this->exportSectionId);


		if ($this->getRequest()->getPost('submit')) {
			$values	= $userExportForm->isValid($this->getRequest()->getPost()); //zend does not support running getValues() without runing isValid() first
			$values = $userExportForm->getValues();

			if(!empty($values['filter']['active'])) {
				$filterSubDef = array('OP' => 'OR');
				if(in_array('active', $values['filter']['active']))
					$filterSubDef[] = array('?'=> 'activationTime', 'OP' => 'IS NOT NULL');
				if(in_array('notActive', $values['filter']['active']))
					$filterSubDef[] = array('?'=> 'activationTime', 'OP' => 'IS NULL');
				$filterDef[] = $filterSubDef;
			}
			if(!empty($values['filter']['testing'])) {
				$filterSubDef = array('OP' => 'OR');
				if(in_array('testing', $values['filter']['testing']))
					$filterSubDef[] = array('?'=> array('isTesting' => 'test'), 'OP' => '=');
				if(in_array('notTesting', $values['filter']['testing']))
					$filterSubDef[] = array('?'=> array('isTesting' => 'ne'), 'OP' => '=');
				$filterDef[] = $filterSubDef;
			}
			if(!empty($values['filter']['forbiden'])) {
				$filterSubDef = array('OP' => 'OR');
				if(in_array('forbiden', $values['filter']['forbiden']))
					$filterSubDef[] = array('?'=> array('isForbiden' => '1'), 'OP' => '=');
				if(in_array('notForbiden', $values['filter']['forbiden']))
					$filterSubDef[] = array('?'=> array('isForbiden' => '1'), 'OP' => '!=');
				$filterDef[] = $filterSubDef;
			}
			if(!empty($values['filter']['activatedFrom']))
				$filterDef[] = array('?'=> array('activationTime' => It6_Date::toDbAsDate($values['filter']['activatedFrom'])), 'OP' => '>=');
			if(!empty($values['filter']['activatedTo']))
				$filterDef[] = array('?'=> array('activationTime' => It6_Date::toDbAsDate($values['filter']['activatedTo'])), 'OP' => '<=');
			if(!empty($values['filter']['branchText']))
				$filterDef[] = array('?' => array('branchHandle' => $values['filter']['branchText']), 'OP' => '=');
			if(!empty($values['filter']['branchHandle']))
				$filterDef[] = array('?' => array('branchHandle' => $values['filter']['branchHandle']), 'OP' => '=');
			
			$selectedCols = array();
			foreach($values['columns'] as $colName => $colValue) {
				if($colValue == 1)
					$selectedCols[] = $colName;
			}


			$extensions[] = new It6_WsExtension_Client_Filter('filter', $filterDef);
			$extensions[] = new It6_WsExtension_Client_Columns('columns', $selectedCols);
			
			$params	= $this->ws->ext($extensions)->User->getAll();
			$params	= It6_ArrayWrapper::toNativeArray($params);
			
			$scvFile	= fopen('php://output', 'w');
			$colNames	= implode(',', $selectedCols);
			
			ob_start();
			fwrite($scvFile, $colNames.PHP_EOL);
			foreach($params as $param) {
				fputcsv($scvFile, $param);
			}
			$csvOutput = ob_get_clean();


			//ca not use zend headers as it send malformed Content-Disposition and it causes errors in chromium 16
			//$this
				//->getResponse()
				//->clearAllHeaders()
				//->setHeader('Content-Disposition: attachment; filename="users.csv"')
				//->sendHeaders()
				//->setBody($csvOutput)
				//->sendResponse();
				
			header('Content-type: application/force-download');
			header('Content-Disposition: attachment; filename="users.csv"');
			header('Content-Transfer-Encoding: binary');
			echo $csvOutput;
			


			It6_Log::info(
				"User list was exported.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('data'=>  Zend_Json::encode($values))
			);

			exit(1);
		}

		$this->view->userExportForm = $userExportForm;
	}

	public function indexBmAction() {
		$this->indexAction(self::MODE_BOOKMAKER);
	}

	public function viewBmAction() {
		$this->viewAction(self::MODE_BOOKMAKER);
	}

	public function updateBmAction() {
		$this->updateAction(self::MODE_BOOKMAKER);
	}

}
