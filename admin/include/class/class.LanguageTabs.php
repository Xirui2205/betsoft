<?php

class LanguageTabs {

	/**
	 * name: adminDefLangIso
	 * Default Language for admin determines which tab will be laoded first when using this class
	 */
	private $adminDefLangIso = 'cs';

	/**
	* pole vsech jazyku land_id => data
	*/
	protected static $languages = null;

	protected static $generatedJs = false;

	protected $db;
	protected $id;

	public function __construct($id = null, $db = null) {
		$this->id = $id;
		if (isset($db))
			$this->db = &$db;
		else
			$this->db = DbUtil::connectWebDb();
	}

	public function getId() {
		return $this->id;
	}

	public function getLanguages($cached = true) {
		if (!isset(self::$languages) || !$cached) {
			$res = $this->db->query('SELECT * FROM jazyky WHERE zobrazeno = 1');
			DbUtil::testResult($res);
			self::$languages = array();
			if (0 < $res->numRows()) {
				while ($row = $res->fetchRow()) {
					self::$languages[$row['lang_id']] = $row;
				}
			}
		}
		return self::$languages;
	}

	public function getLanguageIsoValues() {
		$values = array();
		foreach ($this->getLanguages() as $lang)
			$values[] = $lang['iso'];
		return $values;
	}

	public function getTabsIdPrefix() {
		return 'langTab';
	}

	public function getLinkIdPrefix() {
		return 'langTabLink';
	}

	public function getTabsIdInfix() {
		return (empty($this->id) ? '' : '_' . $this->id);
	}

	public function getTabId($lang) {
		return $this->getTabsIdPrefix() . $this->getTabsIdInfix() . '_' . $lang;
	}

	public function getLinkId($lang) {
		return $this->getLinkIdPrefix() . $this->getTabsIdInfix() . '_' . $lang;
	}

	/**
	 * @param $templateParams Additional params that should be passed to template.
	 * @param $template File path of PHTML template. (When empty, default will be used.)
	 */
	public function getOutputLinks(array $templateParams = null, $template = null) {
		if (empty($template))
			$template = 'Template/LanguageTabsLinks.phtml';
		if (!isset($templateParams))
			$templateParams = array();
		return Utils::processTemplate($template, array( 'tabs' => &$this, 'params' => $templateParams), true);
	}

	/**
	 * @param $tabData array('ISO of language' => 'content for tab')
	 * @param $templateParams Additional params that should be passed to template.
	 * @param $template File path of PHTML template. (When empty, default will be used.)
	 */
	public function getOutputTabs(array $tabData, array $templateParams = null, $template = null) {
		if (empty($template))
			$template = 'Template/LanguageTabs.phtml';
		if (!isset($templateParams))
			$templateParams = array();
		return Utils::processTemplate($template, array( 'tabs' => &$this, 'data' => $tabData, 'params' => $templateParams), true);
	}

	/**
	 * @param $activateTabs array(tabsId => 'ISO of language that tab should be activated for')
	 * @param $templateParams Additional params that should be passed to template.
	 * @param $template File path of PHTML template. (When empty, default will be used.)
	 */
	public function getOutputJs($activateTabs = null, array $templateParams = null, $template = null) {
		if($activateTabs == null)
			$activateTabs = array('' => $this->adminDefLangIso);

		if (self::$generatedJs)
			return;
		self::$generatedJs = true;
		if (empty($template))
			$template = 'Template/LanguageTabs.js.phtml';
		if (!isset($templateParams))
			$templateParams = array();
		return Utils::processTemplate($template, array( 'tabs' => &$this, 'activate' => $activateTabs,'params' => $templateParams), true);
	}
}
