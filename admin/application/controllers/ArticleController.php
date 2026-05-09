<?php
class ArticleController extends It6_Controller_Abstract {
    
	public $indexSectionId = 365; // výpis tabulky
    public $insertSectionId = 367; // přidat
	public $updateSectionId = 366; // upravit
	public $deleteSectionId = 368; // smazat
    public $loadBetByAliasSectionId = 369;
	public $getOddsByBetId = 370;

    var $orderColumns = array(
		'id' => array('order'=>'asc', 'active' => '')
	);

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array('articleId DESC');

	private $TBODY_LAYOUT = 'article-tbody';
    
    public function init() {
        
		parent::init();
        $this->ws = Zend_Registry::get('ws');
        
        if(!isset($this->articleId)) {
			$this->articleId = $this->getRequest()->getParam('articleId');
		}
        
        // js
        $this->jsIncludes->articleAjax = true;
        $this->jsIncludes->commonAjax = true;
        
        $this->view->articleId = $this->articleId;
        
        // actions ids
        $this->view->indexSectionId      = $this->indexSectionId;
		$this->view->insertSectionId     = $this->insertSectionId;
		$this->view->updateSectionId     = $this->updateSectionId;
		$this->view->deleteSectionId     = $this->deleteSectionId;

        $this->articleDetail = null;
        
		//ordering columns
		$col = $this->getRequest()->getPost('column');
		$ord = $this->getRequest()->getPost('order');
		$this->order = array($ord);
        
		$this->view->order = $this->order;

		$this->orderColumns[$col]['order'] = $ord;
		$this->view->orderButton = Models_Utils::getOrderButtons($this->orderColumns, $this->indexSectionId,$col);
	}
    
    public function indexAction() {
        $inData			= $this->getRequest()->getParams();
		$extensions		= array();
        
        // set priority (from table)
        if (isset($inData['setPriority0'])) {
			$article = array_keys($inData['setPriority0']);
			$articleId = reset($article);
			if ($this->ws->Article->update(array(
                                'articleId' => $articleId,
                                'articlePriority' => 0))) {
				$this->removeArticlesCache();
				$this->view->feedbackMsg = UiUtil::printMessages('ticket_set_priority_ok');
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors('ticket_set_priority_error');
			}
		} else if (isset($inData['setPriority1'])) {
			$article = array_keys($inData['setPriority1']);
			$articleId = reset($article);
			if ($this->ws->Article->update(array(
                                'articleId' => $articleId,
                                'articlePriority' => 1))) {
				$this->removeArticlesCache();
				$this->view->feedbackMsg = UiUtil::printMessages('ticket_set_priority_ok');
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors('ticket_set_priority_error');
			}
		}
        
		// process user actions performed from the user lisiting table
		// (no actions)
		
		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		// make possible to select user from GET param
		if (!isset($this->filterData['id'])) {
			$id = intval($this->getRequest()->getParam('id'));
			if (!empty($id)) {
				$this->filterData['id'] = $inData['filter']['id'] = $id;
			}
		}

        /*
         *  filtry:                     system names:
         * ---------------------------------------------------------------------
         * Article id                   articleId
         * Article title                articleTitle
         * Perex (like)                 articlePerex
         * Obsah (like)                 articleContent
         * Publikováno od               articlePublishedFrom
         * Publikováno do               articlePublishedTo
         * 
         */
        
		//create filter
        
		$filterCfg = array(
            array(i18n::tr('id'), 'articleId', 'text', array(array('articleId', '=')), 'Zend_Validate_Int'),
            array(i18n::tr('article_title'), 'articleTitle', 'text', array(array('articleTitle', 'LIKE', '%?%'))),
            array(i18n::tr('article_perex'), 'articlePerex', 'text', array(array('articlePerex', 'LIKE', '%?%'))),
            array(i18n::tr('article_content'), 'articleContent', 'text', array(array('articleContent', 'LIKE', '%?%'))),
            array(i18n::tr('article_sazka_alias_inserted'), 'articleBetAliasInserted', 'text', array(array('articleBetAliasInserted', '=')), 'Zend_Validate_Int'),
            array(i18n::tr('article_sazka_id'), 'articleBetId', 'text', array(array('articleBetId', '=')), 'Zend_Validate_Int'),
            array(i18n::tr('article_published_from'), 'articlePublishedFrom', 'dateTime', array(array('articlePublished', '>=', '?')), 'It6_Validate_Date', null, 'start'),
            array(i18n::tr('article_published_to'), 'articlePublishedTo', 'dateTime', array(array('articlePublished', '<', '?')), 'It6_Validate_Date', null, 'to')
		);
		
		$filter = new It6_WsForm_Filter($filterCfg);
        
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$tableCfg = array(
			array(null, null),
			array('id', 'articleId'),
            array('article_title', 'articleTitle'),
            array('article_priority', 'articlePriority'),
			array('article_published', 'articlePublished', 'dateTime'),
		);
		
		$table = new It6_WsForm_Table($tableCfg);
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);

		//get article list by WS
		if ( !empty($inData) ) {
			$articles = $this->ws->ext($extensions)->Article->getAll();
			$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		} else {
			$articles = array();
			$this->view->notFilter = true;
			$this->view->paginator = false;
		}
		
		$articles = It6_ArrayWrapper::toNativeArray($articles);
                
		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
        $this->view->tBody      = $table->getTbodyLayout($this->TBODY_LAYOUT, $articles);
    }
    
    public function updateAction() {
		$this->_helper->layout->setLayout('empty');

		$updateForm = new Models_Form_ArticleDetail($this->updateSectionId);
        
        $article = array();
        
		$this->view->tipsDataJson = '';
		
		$isSubmit = $this->getRequest()->getPost('submit');
		if(!empty($isSubmit)) {
			$inData = $this->getRequest()->getPost();
		}

		if(!empty($inData)) {
			if($updateForm->isValid($inData)) {
                $values = $updateForm->getValues();
				unset($values['imageName']);
				unset($values['langIso']);
                $values['adminId'] = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN);
                try {
					$this->ws->Article->update($values);
					
					$tipsData = array();
					if(!empty($values['articleBetAux'])) foreach($values['articleBetAux'] as $betId){
						 if (isset($inData['odds-'.$betId])) {
							 //       sazka_id => sloupec_id
							 $tipsData[$betId] = $inData['odds-'.$betId];
						 }
					}
					
					$this->ws->ArticleTips->save($values['articleId'], $tipsData);
					
                    $this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
                    $this->removeArticlesCache();
					
					It6_Log::info(
						"Article was updated.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
				catch( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('update-error: '.$e->getMessage()) );
					It6_Log::err(
						"Article update error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			}
			$article = $inData;
			
			$article['imageId'] = $article['articleImageId'];
			$article['imageName'] = $article['imageName'];
			
			
			// posted multiselect values
			// (in $article)
			
			foreach ($inData as $k => $postedColumnId) {
				// posted odds data
				if (strpos($k, 'odds-') !== false) {
					$keyParts = explode('-', $k);
					$postedBetId = intval($keyParts[1]);
					$this->view->tipsDataJson[$postedBetId] = $postedColumnId;
				}
			}
		} else {
            $article = $this->ws->Article->getById($this->articleId);
			$article = It6_ArrayWrapper::toNativeArray($article);
			
			if (!empty($article['articleBetId'])) {
				// saved multiselect values
				$article['articleBetAux'] = It6_ArrayWrapper::toNativeArray($this->ws->Article->getSelectedTipsData($article['articleId'],false));
				
				// saved odds data
				$this->view->tipsDataJson = It6_ArrayWrapper::toNativeArray($this->ws->Article->getSelectedTipsData($article['articleId'],true));
			}
		}
		
		// all multiselect values for bet id (value => optionName)
		$betTipsOptions = $this->setBetTipsOptionsData($article['articleBetId']);
		
		$this->view->tipsDataJson = json_encode($this->view->tipsDataJson);
		
		$updateForm->setBetTipsOptions($betTipsOptions);
		
        $updateForm->populate($article);
		$this->view->updateForm = $updateForm;
        
        $this->view->imageId = isset($article['imageId']) ? $article['imageId'] : null;
        $this->view->imageName = isset($article['imageName']) ? $article['imageName'] : null; 
		$this->view->publisherKey = $this->ws->Article->getPublisherKey($article['articleId']);
	}
    
    public function insertAction() {
        $this->_helper->layout->setLayout('empty');
		$insertForm = new Models_Form_ArticleDetail($this->insertSectionId);
        
        $values = array(
            'articlePublic' => 1,
            /*'articlePublished' => It6_Date::nowAsDate().' '.It6_Date::nowAsTime()*/
        );
        
		$this->view->tipsDataJson = '';
		$betTipsOptions = array();
		
		if ($this->getRequest()->getPost('submit')) {
			$inData = $this->getRequest()->getPost();
			
			// posted multiselect values
			// in $values[]

			foreach ($inData as $k => $postedColumnId) {
				// posted odds data
				if (strpos($k, 'odds-') !== false) {
					$keyParts = explode('-', $k);
					$postedBetId = intval($keyParts[1]);
					$this->view->tipsDataJson[$postedBetId] = $postedColumnId;
				}
			}
			
			// all multiselect values for bet id (value => optionName)
			if($inData['articleBetId']>0) $betTipsOptions = $this->setBetTipsOptionsData($inData['articleBetId']);
			
			$this->view->imageId = isset($inData['imageId']) ? $inData['imageId'] : null;
			$this->view->imageName = isset($inData['imageName']) ? $inData['imageName'] : null;
			
			if (!$insertForm->isValid( $inData )) {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			} else {
				$values = $insertForm->getValues();
				unset($values['imageName']);
				unset($values['langIso']);
				try {
					if ($this->articleId = $this->ws->Article->insert($values)) {
						
						$tipsData = array();
						if(!empty($inData['articleBetAux'])) foreach($values['articleBetAux'] as $betId){
							 if (isset($inData['odds-'.$betId])) {
								 //       sazka_id => sloupec_id
								 $tipsData[$betId] = $inData['odds-'.$betId];
							 }
						}
						
						$this->ws->ArticleTips->save($this->articleId, $tipsData);
						
						$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );
						$this->removeArticlesCache();
						It6_Log::info(
							"Article '%articleId%' was inserted.",
							It6_Log::TAG_ADMIN_OPERATION,
							array(
								'articleId' => $this->articleId,
								'newData' =>  Zend_Json::encode($values),
								'adminId' => intval($_SESSION['admin'])
							)
						);

						// inserted, show update now
						
						$article = $this->ws->Article->getById($this->articleId);
						$article = It6_ArrayWrapper::toNativeArray($article);

						$updateForm = new Models_Form_ArticleDetail($this->updateSectionId);
						
						$betTipsOptions = array();
						
						if (!empty($article['articleBetId'])) {
							// saved multiselect values
							$article['articleBetAux'] = It6_ArrayWrapper::toNativeArray($this->ws->Article->getSelectedTipsData($article['articleId'],false));

							// saved odds data
							$this->view->tipsDataJson = It6_ArrayWrapper::toNativeArray($this->ws->Article->getSelectedTipsData($article['articleId'],true));
							
							$this->view->tipsDataJson = json_encode($this->view->tipsDataJson);
							
							// all multiselect values for bet id (value => optionName)
							$betTipsOptions = $this->setBetTipsOptionsData($article['articleBetId']);
						}
						
						$updateForm->setBetTipsOptions($betTipsOptions);

						$updateForm->populate($article);
						$this->view->updateForm = $updateForm;

						$this->view->imageId = isset($article['imageId']) ? $article['imageId'] : null;
						$this->view->imageName = isset($article['imageName']) ? $article['imageName'] : null; 
						$this->view->publisherKey = $this->ws->Article->getPublisherKey($article['articleId']);
						
						$this->_helper->viewRenderer->setRender('update'); 
						
						return;
					}
					else {
						throw new Exception('');
					}
				}
				catch ( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error: ' . $e->getMessage()) );
					It6_Log::notice(
						"Article insert error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values), 'message' => $e->getMessage())
					);
				}
			}
		}
		
		// new insert or not valid
		$this->view->tipsDataJson = json_encode($this->view->tipsDataJson);
		$insertForm->setBetTipsOptions($betTipsOptions);
        $insertForm->populate($values);
		$this->view->insertForm = $insertForm;
	}
    
    public function deleteAction() {
        $this->_helper->layout->setLayout('empty');	
		$articleId = $this->getRequest()->getPost('articleId');
		$exts = array();
		$exts[] = new It6_WsExtension_Client_Filter(
			'filter',
			array('?' => array('articleId' => $articleId))
		);

        try {
            if ($this->ws->Article->delete($articleId)) {
				$this->removeArticlesCache();
                $this->view->feedbackMsg = UiUtil::printMessages( array('delete-ok') );
                It6_Log::info(
                    "Article '%articleId%' was deleted.",
                    It6_Log::TAG_ADMIN_OPERATION,
                    array(
                        'articleId' => $this->articleId,
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
                "Article delete error.",
                It6_Log::TAG_ADMIN_OPERATION,
                array(
                    'articleId'=> intval($articleId),
                    'message' => $e->getMessage(),
                    'adminId' => intval($_SESSION['admin'])
                )
            );
        }
    }
    
    // called by ajax onchange (on field articleBetAliasInserted)
    public function loadBetByAliasAction() {
        $this->_helper->layout->setLayout('empty');
        
        $alias = $this->getRequest()->getPost('alias');
        if (!$alias) {
            exit;
        }
        
		$bet = $this->ws->Bet->getByFullAlias($alias);
		$bet = It6_ArrayWrapper::toNativeArray($bet);

        // podpurne sazky vcetne hlavni sazky pro tuto sazku (pro naplneni multiselectu)
        $betTips = array();
        if (isset($bet['betId'])) {
            $betTips = $this->ws->Bet->getBetTips($bet['betId']);
            $betTips = It6_ArrayWrapper::toNativeArray($betTips);
        }
        
        echo json_encode( array("bet" => $bet, "aux" => $betTips));
        exit;
    }
	
	public function getOddsByBetIdAction() {
		$this->_helper->layout->setLayout('empty');
        
        $betId = $this->getRequest()->getParam('betId');
        $betOptionName = $this->getRequest()->getParam('betOptionName');
		
        if (!$betId) {
            exit;
        }
        
		$res = array(
			'betId' => $betId,
			'betOptionName' => $betOptionName,
			'odds' => array()
		);
		
		$odds = $this->ws->Bet->getOddsByBetId($betId);
		$odds = It6_ArrayWrapper::toNativeArray($odds);

		$res['odds'] = $odds;
		
        echo json_encode($res);
        exit;
	}
	
	private function setBetTipsOptionsData($betId) {
		$betTipsOptions = array();
		$aux = $this->ws->Bet->getBetTips($betId);
		
		$aux = It6_ArrayWrapper::toNativeArray($aux);
		if(!empty($aux)) foreach ($aux as $ab) {
			$note = !empty($ab['betNote']) ? ' '.$ab['betNote'] : '';
			$betTipsOptions[$ab['betId']] = $ab['typeName'] . $note . ', ' . $ab['name'];
		}
		return $betTipsOptions;
	}
	
	public function removeArticlesCache() {
		It6_GlobalCache_Invalidator::invalidateWebPage(107);
		It6_GlobalCache_Invalidator::invalidateWebPage(106);
		It6_GlobalCache_Invalidator::invalidateWebPage(105);
	}
}