<?php

class Webservice_Article extends Webservice_AbstractWebService {

	public static $TABLE = "article";
	public static $IDENTITY = "id";
	public static $TABLE_PREFIX = "art";
	public static $TABLE_TIPS = "article_tips";
	public static $TABLE_TIPS_PREFIX = "arttips";
	public static $TABLE_GALLERY = "galerie";
	public static $TABLE_GALLERY_PREFIX = "gal";
	public static $TABLE_LANG = "jazyky";
	public static $TABLE_LANG_PREFIX = "lang";
	public static $ENTITY_NAME = "Entities_article";
	const PARAM_HOMEPAGE_COUNT = 'web.articlesHomepageCount';
	protected static $CONV = array(
		'art.id' => 'articleId',
		'art.title' => 'articleTitle',
		'art.perex' => 'articlePerex',
		'art.content' => 'articleContent',
		'art.priority' => 'articlePriority',
		'art.public' => 'articlePublic',
		'art.published' => 'articlePublished',
		'art.sazka_alias_inserted' => 'articleBetAliasInserted',
		'art.sazka_id' => 'articleBetId',
		'art.lang_id' => 'languageId',
		'art.image_id' => 'articleImageId',
		'art.url' => 'articleUrl',
		'gal.image_id' => 'imageId',
		'gal.name' => 'imageName',
		'art.admin_id' => 'adminId',
		'lang.iso' => 'langIso'
	);

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
						->joinLeft(
								array(static::$TABLE_GALLERY_PREFIX => static::$TABLE_GALLERY), static::$TABLE_PREFIX . '.image_id = gal.image_id', null)
						->join(
								array(static::$TABLE_LANG_PREFIX => static::$TABLE_LANG), static::$TABLE_PREFIX . '.lang_id = lang.lang_id', null);
	}

	/**
	 * Returns all article data in the system.
	 * @return struct article data structure
	 * @see Entities_Article
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Find article by given identifier.
	 * @param integer $articleId identifier of the article
	 * @return struct article structure
	 * @see Entities_Article
	 */
	public static function getById($articleId, $extensions = null) {
		$retdata = parent::getById($articleId, $extensions);
		$retdata = self::convertValues($retdata, 'fromDb');
		return $retdata;
	}

	public static function insert($values) {
		$values['articleUrl'] = It6_Uri_Http::friendlyUrl($values['articleTitle']);
		$result = parent::insert(self::convertValues($values, 'toDb'));
		return $result;
	}

	public static function update($values) {
		$values['articleUrl'] = It6_Uri_Http::friendlyUrl($values['articleTitle']);
		$result = parent::update(self::convertValues($values, 'toDb'));
		return $result;
	}

	public static function getSelectedTipsData($articleId, $selectColumn = false) {
		$columns = !$selectColumn ? array('sazka_id') : array('sazka_id', 'sloupec_id');
		$res1 = static::getDb()->select()
						->from('article_tips', $columns)
						->where('article_id = ?', $articleId)
						->query()->fetchAll();
		$res = array();
		if (!empty($res1))
			foreach ($res1 as $r) {
				if (!$selectColumn) {
					$res[] = $r['sazka_id'];
				} else {
					$res[$r['sazka_id']] = $r['sloupec_id'];
				}
			}
		return $res;
	}

	public static function getPublished($limit = null, $langId = DEFAULT_LANG_ID, $returnSelect = false) {
		$select = static::getDb()->select()->from('article AS art', array('id AS articleId',
																	'title AS articleTitle',
																	'url AS articleUrl',
																	'perex as articlePerex',
																	'published as articlePublished'))
				->joinLeft('galerie AS gal', 'art.image_id = gal.image_id', array('gal.image_id AS imageId', 'gal.name AS imageName'))
				->where('public = 1')
				->where('lang_id = ?', $langId)
				->where('published <= ?', It6_Date::dbNow())
				->order(array('priority DESC', 'published DESC'));

		if ($limit) {
			$select->limit($limit);
		}

		return !$returnSelect ? $select->query()->fetchAll() : $select;
	}
	public static function getPublishedCount($langId = DEFAULT_LANG_ID) {
		$res = static::getDb()->select()->from('article AS art', array('count(*)'))
				->where('public = 1')
				->where('lang_id = ?', $langId)
				->where('published <= ?', It6_Date::dbNow())
			->query()->fetch();
		$res = It6_ArrayWrapper::toNativeArray($res);
		return $res['count(*)'];
	}
	
	public static function getPublisherKey($articleId) {
		return sha1($articleId . ' a nějaký text (^&%_)');
	}

	private static function convertValues($values, $direction) {
		if ($direction == 'toDb') {
			if (isset($values['articlePublished']))
				$values['articlePublished'] = It6_Date::toDb($values['articlePublished']);

			if ((isset($values['articleImageId']) && !is_numeric($values['articleImageId']))) {
				$values['articleImageId'] = new Zend_Db_Expr('NULL');
			}
		} else if ($direction == 'fromDb') {
			$values['articlePublished'] = It6_Date::fromDb($values['articlePublished']);
		}

		return $values;
	}

	public static function getBookmakerTipsByArticleId($articleId) {
		$langId = intval($_SESSION['lang_id']);
		return static::getDb()->select()
						->from('podtyp')
						->join('podtyp_sloupce', 'podtyp_sloupce.podtyp_id = podtyp.podtyp_id', array('sloupec_id as columnId',
							'TRANSLATE(podtyp_sloupce.nazev,'.$langId.') as columnName'))
						->join('sazka_kurz_aktualni', 'sazka_kurz_aktualni.sloupec_id = podtyp_sloupce.sloupec_id', array('kurz as rate'))
						->join('sazky', 'sazky.sazka_id = sazka_kurz_aktualni.sazka_id', array('sazky.sazka_id as betId',
							'IF(sazky.ticket_text IS NULL OR sazky.ticket_text=\'\',sazky.text,sazky.ticket_text) as ticketName',
							'text_note as betNote',
							'sazky.typ_id', 'alias', 'jednoducha as simple',
							'sazky.status',
							'(platna_do > \''.It6_Date::dbNow().'\') as current',
							'sazky.alias_new'))
						->joinLeft('typ', 'typ.typ_id = sazky.real_typ_id', array('typ.typ_alias_id', 'TRANSLATE(typ.nazev,'.$langId.') as typeName'))
						->joinLeft('typ AS t2', 't2.typ_id = sazky.typ_id', array('t2.typ_alias_id AS typ_alias_id_2', 'TRANSLATE(t2.nazev,'.$langId.') as typeName2'))
						->join('article_tips', '(sazky.sazka_id = article_tips.sazka_id
						and article_tips.sloupec_id = podtyp_sloupce.sloupec_id)')
						->where('article_id = ?', $articleId)
						->order(array('sazky.sazka_id'))
						->query()->fetchAll();
	}

}