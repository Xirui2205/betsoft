<?php
/**
 * Stem related static methods.
 * @author Tomas Polz
 * @see Entities_Newspapers
 */
class Webservice_Document extends Webservice_AbstractWebService  {

	public static $ENTITY_NAME	= "Entities_Document";
	public static $TABLE		= "document";
	public static $TABLE_PREFIX	= "d";
	public static $IDENTITY		= "id";

	public static $CONV = array(
		'id'		=> 'documentId',
		'name'		=> 'name',
		'url'		=> 'url',
		'file_name'	=> 'fileName',
	);

	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query);

		return $query;
	}

	/**
	 * Returns all stams
	 * @return struc structure of the stams of branch
	 */
	public static function getAll($extensions = null) {
		$documents = It6_ArrayWrapper::toNativeArray(parent::getAll($extensions));

		$newspapersFileName = Webservice_Newspapers::getActualFilePath();
		$newspapersUrl = PROTOCOL . WEBHOST . "pdf/newspapers/" . $newspapersFileName;

		$documents[] = array (
			"documentId" => 0,
			"name" => It6_Models_Translator::translate("vt_newspapers", 1),
			"url" => $newspapersUrl,
			"fileName" => $newspapersFileName,
			"newspapers" => true
		);

		return $documents;
	}

	/**
	 * Returns stem with given Id
	 * @param array $stemId id of the stem we need info about
	 * @return struc structure of the stem with the given id
	 */
	public static function getById($documentId, $extensions = null){
		return parent::getById($documentId, $extensions);
	}

	/**
	 * Insert new stem. 
	 * @param struct $region structure of the region
	 * @return integer region identifier of the created region
	 * @see Entities_Stem
	 */
	public static function insert($document) {
		if (!empty($document["fileName"]) && file_exists(ROOT . 'web/www/pdf/' . $document["fileName"]))
		{
			$fileName = $document["fileName"];
			$fullPath = ROOT . 'web/www/pdf/' . $document["fileName"];
			$ftp = new It6_FtpSync_Client();
			$res = $ftp->upload(array($fullPath => "/pdf/$fileName"), false);
		}

		return parent::insert($document); 
	}

	/**
	 * Update stem.
	 * @param struct $stam structure of the stem
	 * @return true on success
	 * @see Entities_Stem
	 */
	public static function update($document) {
		return parent::update($document);
	}

	/**
	* Delete stem.
	* @param struct $stemId idenetifier of the stem.
	* @return true on success
	* @see Entities_Stem
	*/
	public static function delete($documentId) {
		return parent::delete($documentId);
	}
}
