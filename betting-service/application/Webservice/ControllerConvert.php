<?php

/**
 * Support for mapping "request data" -> "MVC data"
 * @author Petr Stastny
 * @see Entities_ControllerConvert
 */
class Webservice_ControllerConvert extends Webservice_AbstractWebService {

public static $TABLE = "controller_convert";
public static $TABLE_PREFIX	= "ccv";
public static $ENTITY_NAME = "Entities_ControllerConvert";
public static $IDENTITY	= "c_id";
	
protected static $CONV = array(
	'c_id' => 'controllerId',
	'lang_id' => 'langId',
	'req_controller' => 'requestController',
	'req_action' => 'requestAction',
	'real_controller' => 'realController',
	'real_action' => 'realAction',
	'title' => 'title',
	'description' => 'description',
	'keywords' => 'keywords',
	'after_login' => 'afterLogin',
	'actionless' => 'actionless',
	'nonassoc_params' => 'nonassocParams',
);

/**
 * Should be limited by pagination extension, limit count is for query grouped by controller id, other extensions are used too.
 * If column extension is used, it is used for second (data retrieving) query only.
 * @param array $where Standard WS where constraint @see Webservice_AbstractWebService::getAllWhere()
 * @param boolean $groupResult TRUE if returned rows should be grouped by controller id (controllerId => rows),
 *                             otherwise ungrouped rows data will be returned. 
 */
public static function getAllWhereLimitGroupedByControllerId($where, $groupResult, $extensions = null) {
	// grouped query for controller IDs only
	$extensions1 = array();
	foreach ($extensions as $id => &$ext) {
		if ('Columns' != $ext[It6_WsExtension::PARAM_CLASS])
			$extensions1[] = &$ext;
	}
	$dbColumnCid = static::getDbColumn('controllerId');
	$hookColumns = function($query) use ($dbColumnCid) {
		return $query->columns(array('controllerId' => $dbColumnCid));
	};
	$hookGroup = function($query) use ($dbColumnCid) {
		return $query->group($dbColumnCid);
	};
	$_ccIds = Webservice_ControllerConvert::getAllWhereInjected(
		$where,
		array(
			'columns' => $hookColumns,
			'group' => $hookGroup, 
		),
		$extensions1
	);
	list($ccIds, $extensionsOutput) = It6_WsExtension_Server_Abstract::extractOutputs($_ccIds);
	// second ungrouped query limited by previous query result
	$extensions2 = array();
	foreach ($extensions as &$ext) {
		// limiting replaced by WHERE constraints from first query
		if ('Pagination' != $ext[It6_WsExtension::PARAM_CLASS])
			$extensions2[] = &$ext;
	}
	if (empty($ccIds)) {
		$pages = array();
	}
	else {
		$where["$dbColumnCid IN (?)"] = array_map(function($i) { return $i['controllerId']; }, $ccIds);
		$pages = Webservice_ControllerConvert::getAllWhere($where, $extensions2);
	}
	It6_WsExtension_Server_Abstract::copyOutputs($_ccIds, $pages, false); // we want pagination and possibly others
	if ($groupResult) {
		list($_pages, $extensionsOutput) = It6_WsExtension_Server_Abstract::extractOutputs($pages);
		$pages = array();
		foreach ($_pages as $page)
			$pages[ $page['controllerId'] ][] = $page;
		It6_WsExtension_Server_Abstract::setOutputs($pages, $extensionsOutput);
	}
	return $pages;
}

public static function updateMetadata($entity) {
	$db = static::getDb();
	It6_DbTransaction::begin($db);

	try {
		$entity = new It6_ArrayWrapper($entity);
		$data = static::removeTableNames(static::fromEntity($entity));

		$cId = $data['c_id'];
		$langId = $data['lang_id'];
		$_data = array(
			'title' => $data['title'],
			'description' => $data['description'],
			'keywords' => $data['keywords'],
		);
		if (!empty($data['req_controller'])) {
			$_data['req_controller'] = $data['req_controller'];
		}
		if (!empty($data['req_action'])) {
			$_data['req_action'] = $data['req_action'];
		}

		$db->update(
			static::$TABLE,
			$_data,
			array('c_id=?' => $cId, 'lang_id=?' => $langId)
		);
		It6_DbTransaction::commit($db);
	}
	catch ( Exception $e ) {
		It6_DbTransaction::rollback($db);
		throw new It6_XmlRpc_Exception("Can not update page metadata. (Entity: '".get_called_class()."')", 0, $e);
	}
	return true;
}

} // class