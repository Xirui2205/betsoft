<?php

class VoucherController extends It6_Controller_Abstract {

	protected $viewSectionId		= 306;
	protected $indexSectionId		= 307;
	protected $insertSectionId		= 308;

	protected $voucherId;
	protected $ws;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array('voucherId ASC');

	private static $TBODY_LAYOUT = 'voucher-tbody';

	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		if(!isset($this->voucherId))
			$this->voucherId = $this->getRequest()->getParam('voucherId');

		$this->jsIncludes->voucherAjax		= true;
		$this->jsIncludes->commonAjax		= true;

		$this->view->viewSectionId		= $this->viewSectionId;
		$this->view->indexSectionId		= $this->indexSectionId;

		$this->view->voucherId			= $this->voucherId;
	}



	public function indexAction() {
		$inData			= $this->getRequest()->getParams();
		$extensions		= array();


		if(isset($inData['delete'])) {
			$voucher = array_keys($inData['delete']);
			$voucherId = reset($voucher);

			if ($this->ws->Vaucher->delete(array($voucherId))) {
				$this->view->feedbackMsg = UiUtil::printMessages('voucher_delete_ok ', implode(',',$voucher), TRUE);
					It6_Log::info(
						"voucher '%voucher%' was successfully deleted.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('voucher' => $voucherId)
					);
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors( 'voucher_delete_error', implode(',',$voucher), TRUE);
				It6_Log::warn(
					"Error while deleting voucher '%voucher%'.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('voucher' => $voucherId)
				);
			}
		}
		if(isset($inData['show'])) {
			$voucher = array_keys($inData['show']);
			$voucherId = reset($voucher);

			if ($this->ws->voucher->show(array($voucherId))) {
				$this->view->feedbackMsg = UiUtil::printMessages('voucher_show_ok', implode(',',$voucher), TRUE);
				It6_Log::info(
					"voucher '%voucher%' was set to be visible.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('voucher' => $voucherId)
				);
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors( 'voucher_show_error', implode(',',$voucher), TRUE);
				It6_Log::warn(
					"Error setting voucher '%voucher%' to visible.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('voucher' => $voucherId)
				);
			}
		}
		if(isset($inData['hide'])) {
			$voucher = array_keys($inData['hide']);
			$voucherId = reset($voucher);

			if ($this->ws->voucher->hide(array($voucherId))) {
				$this->view->feedbackMsg = UiUtil::printMessages('voucher_hide_ok', implode(',',$voucher), TRUE);
				It6_Log::info(
					"voucher '%voucher%' was set to be hidden.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('voucher' => $voucherId)
				);
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors( 'voucher_hide_error', implode(',',$voucher), TRUE);
				It6_Log::warn(
					"Error setting voucher '%voucher%' to be hidden.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('voucher' => $voucherId)
				);
			}
		}

		if(isset($inData['changeOrder'])) {
			$voucher = array_keys($inData['changeOrder']);
			$voucherId = reset($voucher);

			$data = array(
				'navigationOrder'			=> $inData['navigationOrder'][$voucherId],
				'navigationOrderOffergen'	=> $inData['navigationOrderOffergen'][$voucherId],
				'voucherId'					=> $voucherId,
			);

			$this->ws->voucher->update($data, true);
		}




		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);


		$setOptions = Models_Utils::getSelectOptions($this->ws->VoucherSet->getAllOrder(array('name')), 'voucherSetId', 'name');
		
		$filter = new It6_WsForm_Filter(array(
			array('Handle', 'handle', 'text', array(array('handle', '='))),
			array('User nick', 'user', 'text', array(array('userNick', '='))),
			array('Set', 'set', 'select', array(array('setId', '=')), null, null, $setOptions),
		));
		$filter->getExtension($this->filterData, $extensions);


		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);		
		
		//create table
		$table = new It6_WsForm_Table(array(
			array('Id', 'voucherId'),
			array('Set', 'setName'),
			array('Handle', 'handle'),
			array('Created', 'created'),
			array('Valid to', 'validTo'),
			array('Valid from ', 'validFrom'),
			array('Used', 'used'),
			array('Used time', 'validFromTime'),
			array('Amount', 'amount'),
			array('Point type id', 'pointTypeId'),
			array('User nick', 'userNick'),
			array('Point transaction id', 'pointTransactionId'),
		));
		$table->getOrderExtension($this->orderData, $extensions);


		//get voucher list by WS
		$vouchers = $this->ws->ext($extensions)->Voucher->getAll();
		$vouchers = It6_ArrayWrapper::toNativeArray($vouchers);


		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $vouchers);
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');

		$voucher 					= $this->ws->voucher->getById( (integer)$this->voucherId );
		$voucher					= It6_ArrayWrapper::toNativeArray($voucher);
			
		$this->view->voucher	= $voucher;
	}

	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$voucherMainForm = new Models_Form_VoucherMain($this->insertSectionId, 0, true);
		
		if ($this->getRequest()->getPost('submit')) {
			if (!$voucherMainForm->isValid( $this->getRequest()->getPost() ))
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			else {
				$values = $voucherMainForm->getValues();
				unset($values['save']);
				$values['validFrom'] = It6_Date::toDb($values['validFrom']);
				$values['validTo'] = It6_Date::toDb($values['validTo']);

				try {
					if ($this->voucherId = $this->ws->Voucher->insert($values)) {
						$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );
						It6_Log::info(
							"Voucher '%voucherId%' was inserted.",
							It6_Log::TAG_ADMIN_OPERATION,
							array('voucherId' => $this->voucherId, 'newData'=>  Zend_Json::encode($values))
						);
	
						$this->_forward('view', null, null, array('voucherId' => $this->voucherId));
					}
					else
						throw new Exception('');
				}
				catch ( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error: ' . $e->getMessage()) );
					It6_Log::notice(
						"Voucher insert error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values), 'message' => $e->getMessage())
					);
				}
			}
		}
		
		$this->view->voucherMainForm = $voucherMainForm;
	}
}
