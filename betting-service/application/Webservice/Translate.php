<?php

/**
 * Translate related static methods.
 * @see Entities_Translate
 *
 */
class Webservice_Translate extends Webservice_AbstractWebService  {

	public static $TABLE = "preklady";
	public static $TABLE_PREFIX = "tr";
	//there is no identity column here as there is no unique identifier in the table preklady.
	//each row is identified by preklad_id and lang_id

	protected static $ENTITY_NAME = "Entities_Translate";
	protected static $CONV = array(
		'lang_id'		=> 'langId',
		'index_pole'	=> 'index',
		'short_text'	=> 'shortText',
		'text'			=> 'text',
		'translate'		=> 'translate',
		'preklad_id'	=> 'translationId',
	);


	public static function insert($translation) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$translation = new It6_ArrayWrapper($translation);
			$data = static::removeTableNames(static::fromEntity($translation));
			unset($data['preklad_id']);
			$db->insert(static::$TABLE, $data);
			$ret = $db->lastInsertId();
			It6_DbTransaction::commit($db);
			return $ret;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not insert translation.", 0, $e);
		}
	}



	public static function update($translation) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$translation = new It6_ArrayWrapper($translation);
			$data = static::removeTableNames(static::fromEntity($translation));

			$translationId = $translation['translationId'];
			$langId = $translation['langId'];
			unset($data['translationId']);
			unset($data['langId']);

			$db->update(
				static::$TABLE,
				$data,
				array(
					'preklad_id = ?' => $translationId,
					'lang_id = ?' => $langId
				)
			);

			It6_DbTransaction::commit($db);
			return $translationId;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update translation.", 0, $e);
		}
	}
}
