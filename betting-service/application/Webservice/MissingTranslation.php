<?php
/**
 * Missing translations related static methods.
 * @author Martin Bohal
 * @see Entities_MissingTranslation
 */
class Webservice_MissingTranslation extends Webservice_AbstractWebService  {

	public static $TABLE				= "translate_missing";
	public static $TABLE_PREFIX			= "tm";
	public static $ENTITY_NAME			= "Entities_Translation";
	public static $IDENTITY				= "translate_key";

	public static $CONV = array(
		'translate_key'		=> 'translateKey',
		'tm.lang_id'			=> 'languageId',
		'controller'		=> 'controller',
		'action'			=> 'action',
		'status'			=> 'status',
		'lang.alt_text'		=> 'languageName'
	);



	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query);
		$query
			->join(
				array(Webservice_Language::$TABLE_PREFIX => Webservice_Language::$TABLE),
				static::$TABLE_PREFIX.'.lang_id = '.Webservice_Language::$TABLE_PREFIX.'.lang_id',
				null);

		return $query;
	}


	/**
	 * Returns all missing translated grouped by translate_key.
	 * @return struct translates structure
	 * @see Entities_MissingTranslate
	 */
	public static function getAllGrouped($extensions = null) {
		try {
			$input = array('where' => array());

			$extensions = static::createExtensions($extensions);
			$metadata = array(
					It6_WsExtension_Server_Query::META_COL_CONV
						=> get_called_class().'::convQuery',
					It6_WsExtension_Server_Columns::META_CONV
						=> static::$CONV);

			$select = static::defaultQuery(static::getDb()->select());

			static::preprocessExtensions($extensions, $input, $metadata, $select);

			//$columns = array_key_exists(It6_WsExtension_Server_Columns::INPUT_COLUMNS, $input)
			//		? $input[It6_WsExtension_Server_Columns::IMPUT_COLUMNS] : null;

			$select->group('translate_key');
			$output = static::fetchAllEntities($select->query()); //, $columns);

			static::postprocessExtensions($extensions, $output, $metadata, $select);

			return $output;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception(get_called_class().'::getAllGrouped(\''.Zend_Json::encode($where).'\',\''.Zend_Json::encode($extensions).'\')', 0, $e);
		}
	}


	public static function delete($translateKey) {
		return parent::delete($translateKey);
	}
}
