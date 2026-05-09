<?php

// article insert / update form
class Models_Form_ArticleDetail extends It6_Models_DecoratedForm_Table {

	//private $auxBetsOptions = array();
	
	public function __construct($sectionId) {
		parent::__construct();
		$this->setName('articleDetailForm');

		$id = $this->createElement('hidden', 'articleId');
		$this->addElement($id);

		$langIso = $this->createElement('hidden', 'langIso');
		$this->addElement($langIso);
		
		$title = $this
				->createElement('text', 'articleTitle')
				->setLabel(i18n::tr('article_title'))
				->addValidator('stringLength', array('min' => 2, 'max' => 190, 'encoding'=>'UTF-8'))
				->setRequired(true);
		$this->addElement($title);

		$imageId = $this
				->createElement('text', 'articleImageId')				
				->setRequired(true)
				->setLabel(i18n::tr('article_image'));
		$imageId->class = 'hidden';
		$this->addElement($imageId);
		
		$imageName = $this->createElement('hidden', 'imageName');
		$this->addElement($imageName);

		$perex = $this
				->createElement('textarea', 'articlePerex')
				->setLabel(i18n::tr('article_perex'))
				->setRequired(true)
				->addValidator('stringLength', false, array('min' => 20, 'max' => 240, 'encoding'=>'UTF-8'));
		
		$this->addElement($perex);

		$content = $this
				->createElement('textarea', 'articleContent') // to-do: html editor
				->setLabel(i18n::tr('article_content'))
				->addValidator('stringLength', false, // zde se custom hlaska hodi (aby se pri prekroceni neopakoval text)
						array('min' => 30, 'max' => 64564, 'encoding'=>'UTF-8',
					'messages' => i18n::tr('Vložte minimálně %min%, maximálně %max% znaků.')))
				->setRequired(true);
		$content->class = 'hidden tinymce';
		$this->addElement($content);

		$priority = $this
				->createElement('checkbox', 'articlePriority')
				->setLabel(i18n::tr('article_priority'));
		$this->addElement($priority);

		// sazka alias
		$betAlias = $this
				->createElement('text', 'articleBetAliasInserted')
				->setLabel(i18n::tr('article_sazka_alias_inserted'))
				->addValidator('alnum')
				->setAttrib('onchange', 'setArticleBetId(this.value)')
				->setRequired(false);
		$this->addElement($betAlias);

		// sazka id - defaultne pri insertu prazdne, nacteni hodnoty ajaxem podle zadaneho aliasu
		$betId = $this
				->createElement('hidden', 'articleBetId') // hidden
				/*->setLabel(i18n::tr('article_sazka_id'))*/
				->addValidator('alnum')
				->setRequired(false);
		$this->addElement($betId);

		$tips = $this
				->createElement('multiselect', 'articleBetAux')
				->setDecorators($this->getElementDecorator(true, false))
				->setLabel(i18n::tr('article_sazka_tips'))
				->addMultiOptions(array()) // nacteni hodnot pres setBetTipsOptions() nebo ajaxem
				->setRequired(false);
		$tips->setRegisterInArrayValidator(false);
		$tips->class = 'left';
		$tips->onchange = 'loadOddTips();';
		$this->addElement($tips);

		$public = $this
				->createElement('checkbox', 'articlePublic')
				->setLabel(i18n::tr('article_public'));
		$this->addElement($public);

		$published = $this
				->createElement('text', 'articlePublished')
				->setLabel(i18n::tr('article_published'))
				->setDecorators($this->getDateTimeDecorator())
				->setAttrib('class', 'dateTime')
				->setRequired(false); // null -> not published
		$this->addElement($published);

		$languages = Zend_Registry::get('ws')->Language->getAllActive();
		$languages = Models_Utils::getSelectOptions($languages, 'languageId', 'name');
		$languageEl = $this
			->createElement('select', 'languageId')
			->setLabel(i18n::tr('Language'))
			->addMultiOptions($languages);
		$this->addElement($languageEl);
		
		$submit = $this
				->createElement('button', 'button')
				->setLabel(i18n::tr('Submit'))
				->setAttrib('onClick', "submitAndReloadGeneric('" . $sectionId . "', '365&', '" . $this->getName() . "', 'article-detail', '&submit=1','#formTypeFilter');return false;");
		$this->addElement($submit);
	}
	
	public function setBetTipsOptions($auxBetsOptions) {
		$this->getElement('articleBetAux')->addMultiOptions($auxBetsOptions);
	}
}