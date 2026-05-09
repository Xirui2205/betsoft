<?php
class BranchMainController extends controllers_BranchAbstractController {

	protected $indexSectionId = 193;
	protected $viewSectionId = 190;
	protected $insertSectionId = 192;
	protected $updateSectionId = 191;

	protected $anonymousUser = array(
		'firstName' => 'BRANCH ',
		'lastName'=> 'BRANCH ',
		'username'=> 'BRANCH ',
		'sex' => 'm',
		'birthDate' => '0000-00-00',
		'email' => '',
		'phone' => '666',
		'street' => 'N/A',
		'town' => 'N/A',
		'zip' => 'N/A',
		'countryId' => 3,
		'branchId' => '',
		'anonymous' => 1,
		'userId' => 0,
		'zakazany' => 1
	);

	protected $orderColumns = array(
		'id' => array('order'=>'asc', 'active' => ''),
		'name' => array( 'order'=>'asc', 'active' => ''),
		'status_id' => array('order'=>'asc', 'active' => '')
	);

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array('branchId ASC');

	private static $TBODY_LAYOUT = 'branch-tbody';


	public function init() {
		parent::init();
		if (!isset($this->branchId)) $this->branchId = $this->getRequest()->getParam('branchId');
		$this->ws = Zend_Registry::get('ws');

		$this->jsIncludes->commonAjax = true;

		$this->view->indexSectionId	= $this->indexSectionId;
		$this->view->viewSectionId	= $this->viewSectionId;
		$this->view->insertSectionId = $this->insertSectionId;
		$this->view->updateSectionId = $this->updateSectionId;

		$this->view->branchId = $this->branchId;

		//ordering columns
		if ( !is_null($this->getRequest()->getPost('column')) && !is_null($this->getRequest()->getPost('order')) ) {
			$col = $this->getRequest()->getPost('column');
			$ord = $this->getRequest()->getPost('order');
			$order = $col.' '.$ord;
			$this->order = array($order);
			$this->view->order = $this->order;

			$this->orderColumns[$col]['order'] = $ord;
			$this->view->orderButton = Models_Utils::getOrderButtons($this->orderColumns, $this->indexSectionId, $col);
		}

		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_CALLCENTRUM)) $this->view->callcenterLogged = true;
		else $this->view->callcenterLogged = false;
	}



	public function indexAction() {
		$inData			= $this->getRequest()->getParams();
		$extensions		= array();

		$this->filterData['isActive'] = 1;
		//prepare wsForm data from $_GET
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		//create filter
		$initialsRaw	= $this->ws->Branch->getTownInitials();
		$initialsRaw	= It6_Models_Form_Util::getSelectOptions($initialsRaw, 'initial', 'initial');
		$initials[0]	= '--'.'Select'.'--';
		$initials		= array_merge($initials, $initialsRaw);

		$filter = new It6_WsForm_Filter(array(
			array('Town', 'town', 'select', array(array('town', 'LIKE', '?%')), null, null, $initials),
			array(i18n::tr('isActive'), 'isActive', 'checkbox', array(array('isActive', '='))),
		));
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array(null, null),
			array('branch_id', 'branchId'),
			array('branch id', 'handle'),
			array('Name', 'name'),
			array('branch_contract', 'contractName'),
			array('is_active', 'isActive'),
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);

		//get user list by WS
		if ( !empty($_REQUEST['paginator']) ) {
			$branches = $this->ws->ext($extensions)->Branch->getAll();
		} else {
			$branches = array();
			$this->view->notFilter = true;
		}

		$branches = It6_ArrayWrapper::toNativeArray($branches);

		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null, $this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $branches);
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$branch = $this->ws->Branch->getById((int)$this->branchId);
		$this->view->branch = $branch;
	}

	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_BranchMain($this->insertSectionId);

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {
				unset($data['submit']);
				$data['mp'] = floatval($data['mp']) / 100.0;
				$data['mpWin'] = floatval($data['mpWin']) / 100.0;
				//TODO: implement Branch::createNew() that inserts both new branch and anonymous user in one transaction
				if (empty($data['currencyId']))
					$data['currencyId'] = 8; //TODO: improve currency handling, now temporarily hardwired CZK
				if (empty($data['banned']))
					$data['banned'] = null;
				else
					$data['banned'] = It6_Date::toDbAsDate($data['banned']);

				if ($newBranchId = $this->ws->Branch->insert($data)) {

					$this->anonymousUser['firstName'] .= $data['name'];
					$this->anonymousUser['lastName'] .= $data['name'];
					$this->anonymousUser['username'] .= $data['name'];
					$this->anonymousUser['email'] = $data['email'];
					$this->anonymousUser['branchId'] = $newBranchId;

					if ($newUserId =$this->ws->User->insert($this->anonymousUser)) {
						$this->view->feedbackMsg = UiUtil::printMessages(array('notice_branch_created'));
						$this->view->feedbackMsg .= '<br/><button onclick="viewBranchDetail('.$newBranchId.')">'.i18n::tr('notice_cont_creating_branch').'</button>';
					}
					else {
						$this->view->feedbackMsg = UiUtil::printMessages(array('notice_branch_created'));
						$this->view->feedbackMsg = UiUtil::printErrors(array('error_branch_user_creating'));
					}
				}
				else
					$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('form_insert_error_general'));
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('error_form_not_valid_general'));
				$form->populate($data);
				$this->view->form = $form;
			}
		} else {
			$this->view->form = $form;
		}
	}


	public function updateAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_BranchMain($this->updateSectionId, $this->branchId);

		$bId = $form->createElement('hidden', 'branchId')->setValue($this->branchId);
		$form->addElement($bId);

		if ($this->branchId) {
			$data = $this->ws->Branch->getById( (integer)$this->branchId );
			$data = It6_ArrayWrapper::toNativeArray($data);
			if ( !empty($data['mpWin']) ) {
				$data['mp'] *= 100;
				$data['mpWin'] *= 100;
			}
			if (empty($data['banned'])) $data['banned'] = '';
			else $data['banned'] = It6_Date::fromDbAsDate($data['banned']);

			$data['longitude']	= It6_Locale_Format::toNumber($data['longitude']);
			$data['latitude']	= It6_Locale_Format::toNumber($data['latitude']);

			$form->populate(It6_ArrayWrapper::toNativeArray($data));
		}

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {
				$form->setAttrib(It6_Filter::ATTR_WEB2DB, 'true');
				$data = $form->getValues();

				$prevMp = $data['mp'];
				if ( floatval($prevMp) > 0 ) $data['mp'] /= 100;
				$prevMpWin = $data['mpWin'];
				if ( floatval($prevMpWin) > 0 ) $data['mpWin'] /= 100;

				if (empty($data['banned'])) $data['banned'] = null;
				else $data['banned'] = It6_Date::toDbAsDate($data['banned']);

			    if ($data['street'] != "" && $data['town'] != "") {
				    $address = urlencode($data['street'].",".$data['town'].",".Webservice_Parameter::getGlobalParameter('maps.region.name'));
				    $region = Webservice_Parameter::getGlobalParameter('maps.region.code');
				    $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");

				    $decoded = json_decode($json);
				    
                    if (count($decoded->{'results'}) != 0) {
                        $latitude = $decoded->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
				        $longitude = $decoded->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};		    	
				        if ($data['longitude'] == "") { $data['longitude'] = $longitude; }
					    if ($data['latitude'] == "") { $data['latitude'] = $latitude; }
                    }
			    }

				if (strpos ($data['longitude'],'.')) {
				  $data['longitude'] = str_replace(".", ",", $data['longitude']);
				}

				if (strpos ($data['latitude'],'.')) {
				  $data['latitude'] = str_replace(".", ",", $data['latitude']);
				}

				$data['longitude']	= It6_Locale_Format::getNumber($data['longitude']);
				$data['latitude']	= It6_Locale_Format::getNumber($data['latitude']);
				if ($this->ws->Branch->update($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages( I18n::tr('form_update_success_general') );
					$form->removeAttrib(It6_Filter::ATTR_WEB2DB);

					$branch = Zend_Registry::get('ws')->Branch->getById($this->branchId);
					$this->view->branch = $branch;
					$this->render('view');
				} else {
					$this->view->feedbackMsg = UiUtil::printErrors( I18n::tr('form_update_error_general') );
				}
				$data['mp'] = $prevMp;
				$data['mpWin'] = $prevMpWin;

				if (empty($data['banned'])) $data['banned'] = '';
				else $data['banned'] = It6_Date::fromDbAsDate($data['banned']);
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors( I18n::tr('error_form_not_valid_general') );
			}
		}

		$form->populate($data);

		$this->view->form = $form;
	}
}