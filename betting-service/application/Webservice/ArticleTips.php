<?php
class Webservice_ArticleTips extends Webservice_AbstractWebService  {
    public static $TABLE      			= "article_tips";
    public static $TABLE_PREFIX         = "arttips";
    
    public static $ENTITY_NAME = "Entities_article";
	protected static $CONV = array(
        'arttips.article_id'           => 'articleTipsArticleId',
        'arttips.sazka_id'             => 'articleTipsBetId'
	);
    
    public static function save($articleId, $tipsData) {
        try {
            $db = static::getDb();
            $db->delete(self::$TABLE, array('article_id=?' => $articleId));
            foreach ($tipsData as $betId => $columnId) {
                /*parent::insert(array(
                    'article_id' => $articleId,
                    'sazka_id' => $betId));*/
                $db->insert(self::$TABLE, array('article_id' => $articleId, 'sazka_id' => $betId, 'sloupec_id' => $columnId));
            }
            return true;
        } catch (Exception $e) {
            It6_Log::err('Article tips not set', It6_Log::TAG_DEFAULT, $e);
            return false;
        }
    }
    
}