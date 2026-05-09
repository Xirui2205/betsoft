<?php

class AdminMainController extends controllers_AdminAbstractController {

	const MODE_DEFAULT = 'default';
	const MODE_SALES = 'sales'; // for role "sales"

	public $indexSectionId = 281;
	public $viewSectionId = 282;
	public $insertSectionId = 283;
	public $updateSectionId = 284;

	var $orderColumns = array(
		'id' => array('order'=>'asc', 'active' => ''),
		'name' => array( 'order'=>'asc', 'active' => ''),
		'status_id' => array('order'=>'asc', 'active' => '')
	);

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array('adminId ASC');

	private static $TBODY_LAYOUT = 'admin-tbody';

	protected $mode = self::MODE_DEFAULT;

	public function init() {

		parent::init();
		$this->view->adminId = $this->adminId;

		//ordering columns
		$col = $this->getRequest()->getPost('column');
		$ord = $this->getRequest()->getPost('order');
		$this->order = array($ord);
		$this->view->order = $this->order;

		$this->orderColumns[$col]['order'] = $ord;
		$this->view->orderButton = Models_Utils::getOrderButtons($this->orderColumns, $this->indexSectionId,$col);

		$acl = Zend_Registry::get('acl');
		$this->mode = ($acl->userHasRole(It6_Acl_Admin::ROLE_SALES) ? self::MODE_SALES : self::MODE_DEFAULT);
	}

	public function indexAction() {
		$inData			= $this->getRequest()->getParams();
		$extensions		= array();

		//prepare wsForm data from $_GET
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);


		//create filter
		$exts = array(
			new It6_WsExtension_Client_Columns('columns', array('branchId', 'name')),
			new It6_WsExtension_Client_Order('order', array('(name COLLATE utf8_czech_ci)')),
		);
		$branches = Zend_Registry::get('ws')->ext($exts)->Branch->getAll();
		$branches = It6_ArrayWrapper::toAssocArray($branches,'branchId','%name%',array(0 => '--Select--'));

		if (self::MODE_SALES == $this->mode && !isset($this->filterData['role'])) {
			$this->filterData['role'] = It6_Acl_Admin::ROLE_BRANCH_EMPLOYEE;
		}

		$acl = Zend_Registry::get('acl');
		if ($acl->userHasRole(It6_Acl_Admin::ROLE_SALES)) {
			$roles = array(
					It6_Acl_Admin::ROLE_BRANCH_EMPLOYEE => It6_Acl_Admin::ROLE_BRANCH_EMPLOYEE,
					It6_Acl_Admin::ROLE_BRANCH_OWNER => It6_Acl_Admin::ROLE_BRANCH_OWNER
				);
		}
		else {
			$roles = It6_Models_Acl::getAssignableRoles();
			array_unshift($roles,0);
			$roles = array_combine($roles,$roles);
			$roles[0] = '--Select--';
		}
		$filter = new It6_WsForm_Filter(array(
			array('nick_name', 'loginName', 'text', array(array('loginName', '=', '?')), null, null, null),
			array('Branch_name', 'branchId', 'select', array() /* array(array('branchId', '=', '?')) */, null, null, $branches),
			array('First name', 'firstName', 'text', array(array('firstName', '=', '?')), null, null, null),
			array('Last name', 'lastName', 'text', array(array('lastName', '=', '?')), null, null, null),
			array('Role', 'role', 'select', array(array('role2', '=', '?')), null, null, $roles),
		));
		$filter->getExtension($this->filterData, $extensions);


		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);


		//create table
		$table = new It6_WsForm_Table(array(
			array(null, null),
			array('id','adminId'),
			array('username','loginName'),
			array('branch_name','branchName'),
			array('First name','firstName'),
			array('Last name','lastName'),
			array('Phone','phone'),
			array('Email','email'),
			array('Banned','isBanned'),
			array('Block','block'),
			array('Block ip','blockIp'),
			array('Last login','lastLogin'),
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);


		//get user list by WS
		if ( !empty($_POST) ) {
			if (!empty($this->filterData['branchId']))
				$admins = $this->ws->ext($extensions)->Admin->getAllWithBranch($this->filterData['branchId']);
			else
				$admins = $this->ws->ext($extensions)->Admin->getAll();
		}
		else {
			$admins = array();
			$this->view->notFilter = true;
		}
		
			
		$admins = It6_ArrayWrapper::toNativeArray($admins);

		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= (
			empty($admins)
			? ''
			: $paginator->getLayout(null, null,  $extensions['paginator']->getResponse())
		);
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $admins);
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$exts = array();
		if (self::MODE_SALES == $this->mode) {
			$exts[] = new It6_WsExtension_Client_Filter(
				'filter1',
				array('?' => array('role2' => It6_Acl_Admin::ROLE_BRANCH_EMPLOYEE))
			);
		}
		$admin = $this->ws->ext($exts)->Admin->getById( (integer)$this->adminId );
		if (!empty($admin)) {
			$admin['secondaryBranches'] = $this->ws->Admin->getSecondaryBranches($admin['adminId'], true);
		}
		$this->view->admin = $admin;
	}

	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_AdminMain($this->insertSectionId);

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {
				unset($data['submit']);
				if ( $data['password'] == $data['passwordAgain'] ) {
					try {
						unset($data['passwordAgain']);
						if ( empty($data['branchId']) )
							$data['branchId'] = null;
						$newAdminId = $this->ws->Admin->insert($data);
						$this->view->feedbackMsg = UiUtil::printMessages(I18n::tr('form_insert_success_general'));
						$this->view->feedbackMsg .= '<br/><button onclick="viewAdminDetail('.$newAdminId.')">'.i18n::tr('Continue creating admin').'</button>';
						unset($data['password']);
						It6_Log::info("Admin '%loginName%' created.",It6_Log::TAG_ADMIN_OPERATION, $data);
					}
					catch ( Exception $e ) {
						if (empty($created)) {
							unset($data['password']);
							It6_Log::notice('Insert admin failed: %message%',It6_Log::TAG_ADMIN_OPERATION, array('data' => $data,'message' => $e->getMessage()));
							$this->view->feedbackMsg = UiUtil::printErrors(array($e->getMessage()));
						}
						else {
							It6_Log::notice(
								'Admin role not set: %message%',
								It6_Log::TAG_ADMIN_OPERATION,
								array('newAdminId' => $newAdminId, 'role' => $role, 'message' => $e->getMessage())
							);
							$this->view->feedbackMsg = UiUtil::printErrors(array($e->getMessage()));
						}
					}
				}
				else {
					unset($data['password']);
					It6_Log::notice('Insert admin failed: Password is not equal to password again.',It6_Log::TAG_ADMIN_OPERATION, $data);
					$this->view->feedbackMsg = UiUtil::printErrors(array('Password is not equal to password again.'));
				}
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
		$form = new Models_Form_AdminMain($this->updateSectionId, $this->adminId);


		if ($this->adminId) {
			$data = $this->ws->Admin->getByIdWithSecondaryBranches( (integer)$this->adminId );
			$data = It6_ArrayWrapper::toNativeArray($data);
			$form->populate(It6_ArrayWrapper::toNativeArray($data));
		}


		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {

				unset($data['submit']);
				if ( empty($data['password']) )
					unset($data['password']);

				if ( empty($data['password']) || $data['password'] == $data['passwordAgain'] ) {
					try  {
						unset($data['passwordAgain']);
						$data['admin_id'] = $this->adminId;
						if ( empty($data['branchId']) )
							$data['branchId'] = null;
						
						$this->ws->Admin->update($data);
						unset($data['password']);
						$this->view->feedbackMsg = UiUtil::printMessages( I18n::tr('form_update_success_general') );
						It6_Acl_Admin::invalidatePersistentCache();
					} 
					catch ( Exception $e ) {
						unset($data['password']);
						It6_Log::notice('Admin update failed: %message%',It6_Log::TAG_ADMIN_OPERATION, array('data' => $data,'message' => $e->getMessage()));
						$this->view->feedbackMsg = UiUtil::printErrors( array($e->getMessage()));
					}
				}
				else {
					unset($data['password']);
					It6_Log::notice('Admin update failed: Password is not equal to password again.',It6_Log::TAG_ADMIN_OPERATION, $data);
					$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('Password is not equal to password again.'));
				}
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors( I18n::tr('error_form_not_valid_general') );
		}

		$form->populate($data);

		$this->view->form = $form;
	}
}
