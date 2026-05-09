<?php
class TypeController extends It6_Controller_Abstract {

	protected $editDetailSectionId		= 328;
	protected $indexSectionId			= 138;
	protected $insertSectionId			= 332;

	protected $ws;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 5);
	private $orderData		= array('typeId');

	private $TBODY_LAYOUT = 'type-tbody';


	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		if(!isset($this->typeId)) {
			$this->typeId = $this->getRequest()->getParam('typeId');
		}

		$this->jsIncludes->typeAjax	= true;
		$this->jsIncludes->commonAjax = true;

		$this->view->editDetailSectionId = $this->editDetailSectionId;
		$this->view->indexSectionId = $this->indexSectionId;

		$this->view->typeId	= $this->typeId;
	}


	public function indexAction() {
		$inData			= $this->getRequest()->getParams();
		$extensions		= array();

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);


		//create filter
		$offerCategories = It6_ArrayWrapper::toNativeArray($this->ws->OfferCategory->getAll());
		$offerCategories = It6_ArrayWrapper::toMultioption($offerCategories, 'offerCategoryId', 'name');
		$offerCategories = array('0' => I18n::tr('Any')) + $offerCategories;

		$filter = new It6_WsForm_Filter(array(
			array('id', 'typeId', 'text', array(array('typeId', '='))),
			array('type_alias_id', 'typeAliasId', 'text', array(array('typeAliasId', '='))),
			array('name', 'name', 'text', array(array('name', '='))),
			array('offer_category', 'offerCategory', 'select', array(array('offerCategoryId', '=')), null, null, $offerCategories),
		));
		$filter->getExtension($this->filterData, $extensions);


		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);


		//create table
		$table = new It6_WsForm_Table(array(
			array(null, null),
			array('id', 'typeId'),
			array('type_alias_id', 'typeAliasId'),
			array('name', 'name'),
			array('offer_category', 'offerCategoryName'),
			array('order', 'order'),
			array('visible', 'visible'),
			array('typ_alais_group', 'typeAliasGroup'),
			array('order_type', 'orderTypeName'),
			array('group_master', 'groupMaster'),
		));
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);


		//get type list by WS
		$types = $this->ws->ext($extensions)->Type->getAll();
		$types = It6_ArrayWrapper::toNativeArray($types);

		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout($this->TBODY_LAYOUT, $types);
	}


	public function editDetailAction() {
		$this->_helper->layout->setLayout('empty');

		$typeDetailForm = new Models_Form_TypeDetail($this->editDetailSectionId);
		$isSubmit = $this->getRequest()->getPost('submit');
		if(!empty($isSubmit)) {
			$inData = $this->getRequest()->getPost();
		}

		if(!empty($inData)) {
			if($typeDetailForm->isValid($inData)) {
				$values = $typeDetailForm->getValues();
				try {
					$this->ws->Type->update($values);
					$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
					It6_Log::info(
						"Type  was updated.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
				catch( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('update-error: '.$e->getMessage()) );
					It6_Log::err(
						"Type update error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			}
			$type = $inData;
		} else {
			$type = Zend_Registry::get('ws')->Type->getById($this->typeId);
			$type = It6_ArrayWrapper::toNativeArray($type);
		}
		$typeDetailForm->populate($type);
		$this->view->typeDetailForm = $typeDetailForm;
	}


	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$typeForm = new Models_Form_TypeDetail($this->insertSectionId);

		if ($this->getRequest()->getPost('submit')) {
			if (!$typeForm->isValid( $this->getRequest()->getPost() )) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			} else {
				$values = $typeForm->getValues();
				try {
					if ($this->typeId = $this->ws->Type->insert($values)) {
						$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );
						It6_Log::info(
							"Type '%typeId%' was inserted.",
							It6_Log::TAG_ADMIN_OPERATION,
							array(
								'typeId' => $this->typeId,
								'newData' =>  Zend_Json::encode($values),
								'adminId' => intval($_SESSION['bookmaker'])
							)
						);

						$values['typeId'] = $this->typeId;
						$this->view->typeDetailForm = new Models_Form_TypeDetail($this->editDetailSectionId);
						$this->view->typeDetailForm->populate($values);
						$this->render('edit-detail');
					}
					else {
						throw new Exception('');
					}
				}
				catch ( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error: ' . $e->getMessage()) );
					It6_Log::notice(
						"Type insert error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values), 'message' => $e->getMessage())
					);
				}
			}
		}
		
		$this->view->typeForm = $typeForm;
	}


	public function deleteAction() {
		$this->_helper->layout->setLayout('empty');	
		$typeId = $this->getRequest()->getPost('typeId');
		$exts = array();
		$exts[] = new It6_WsExtension_Client_Filter(
			'filter',
			array('?' => array('typeId' => $typeId), 'OP' => '=')
		);
		$exts[] = new It6_WsExtension_Client_Columns('columns', array('betId'));
		$bets = Zend_Registry::get('ws')->ext($exts)->Bet->getAll();

		if(empty($bets->collection)) {
			try {	
				if ($this->ws->Type->delete($typeId)) {
					$this->view->feedbackMsg = UiUtil::printMessages( array('delete-ok') );
					It6_Log::info(
						"Type '%typeId%' was deleted.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'typeId' => $this->typeId,
							'adminId' => intval($_SESSION['bookmaker'])
						)
					);
				}
				else
					throw new Exception('');
			}
			catch ( Exception $e ) {
				$this->view->feedbackMsg = UiUtil::printErrors( array('delete-error: ' . $e->getMessage()) );
				It6_Log::notice(
					"Type delete error.",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'typeId'=> intval($typeId),
						'message' => $e->getMessage(),
						'adminId' => intval($_SESSION['bookmaker'])
					)
				);
			}
		}
		else {
			$this->view->feedbackMsg = UiUtil::printErrors( array('delete-error: '.'bets-exist') );
		}
	}
}