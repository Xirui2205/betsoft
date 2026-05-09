<?php

class Webservice_UserTracking extends Webservice_AbstractWebService {

public static $TABLE = 'user_tracking';
public static $TABLE_PREFIX = 'utr';
public static $ENTITY_NAME = 'Entities_UserTracking';
public static $IDENTITY = "ut_id";

public static $CONV = array(
	'ut_id' => 'trackingId',
	'ts' => 'time',
	'persistent_id' => 'persistentId',
	'session_id' => 'sessionId',
	'remote_ip' => 'remoteIp',
	'xforward' => 'xForward',
	'useragent' => 'userAgent',
	'user_id' => 'userId',
	'nick' => 'username',
);

/**
 * Returns all records in DB.
 * @return array Tracking entities
 * @see Entities_UserTracking
 */
public static function getAll($extensions = null) {
	return parent::getAll($extensions);
}



/**
 * Find record by given identifier.
 * @param integer $trackingId
 * @return struct tracking data entity
 * @see Entities_UserTracking
 */
public static function getById($trackingId, $extensions = null) {
	return parent::getById($trackingId, $extensions);
}



/**
 * Always throws an exception
 * @param struct $trackingId
 * @return true on success
 */
public static function delete($trackingId) {
	throw new It6_XmlRpc_Exception('Forbidden');
}



/**
 * Get tracking data for given user
 * @param integer $userId
 * @return array Tracking entities
 * @see Entities_UserTracking
 */
public static function getAllByUserId($userId, $extensions = null) {
	return parent::getAllWhere(array('userId=?' => $userId), $extensions);
}

/**
 * Return records that match given user
 * @param integer $userId
 * @param boolean $persitentId
 * @param boolean $ip
 * @return array|NULL Tracking entities
 * @see Entities_UserTracking
 */
public static function getMatchedTracks($userId, $persitentId, $ip, $limit = null, $offset = null) {
	if (empty($persitentId) && empty($ip))
		return null;
	$db = static::getDb();
	$join = array();
	$group = array();
	$order = array();
	$columns = array();
	if (!empty($persitentId)) {
		$join[] = 'ut.persistent_id=mt.persistent_id';
		$columns[] = 'ut.persistent_id AS persistentId';
		$group[] = 'ut.persistent_id';
		$order[] = 'ut.persistent_id';
	}
	if (!empty($ip)) {
		$join[] = 'ut.remote_ip=mt.remote_ip';
		$columns[] = 'ut.remote_ip AS remoteIp';
		$group[] = 'ut.remote_ip';
		$order[] = 'ut.remote_ip';
	}
	$join = implode(' AND ', $join);
	$columns = implode(',', $columns);
	$group = implode(',', $group);
	$order[] = 'lastMatchTime DESC';
	$order = implode(',', $order);

	$sql =	
"SELECT
	mt.user_id AS userId,
	COUNT(mt.user_id) AS matchCount,
	MIN(mt.ts) AS firstMatchTime,
	MAX(mt.ts) AS lastMatchTime,
	$columns
FROM (
	SELECT $group
	FROM user_tracking ut
	WHERE user_id=?
	GROUP BY $group
) ut
JOIN user_tracking mt
ON mt.user_id<>? AND $join
GROUP BY $group, mt.user_id";

	$sqlCount =
"SELECT COUNT(*) AS c FROM (
$sql
) c";

	$params = array($userId, $userId);

	$rows = $db->query($sqlCount, $params)->fetchAll();
	$total = $rows[0]['c'];

	if (isset($limit)) {
		if (isset($offset)) {
			if ($offset > $total)
				$offset = 0;
			$limit = 'LIMIT ' . intval($offset) . ',' . intval($limit);
		}
		else
			$limit = 'LIMIT ' . intval($limit);
	}
	else
		$limit = '';
	$sql .= " ORDER BY $order $limit";

	$rows = $db->query($sql, $params)->fetchAll();

	if (!empty($rows)) {
		$users = array();
		foreach ($rows as $row)
			$users[$row['userId']] = true;
		$rows2 = $db->select()
			->from(Webservice_User::$TABLE, array('userId' => 'user_id', 'username' => 'nick', 'name' => 'jmeno', 'surname' => 'prijmeni'))
			->where('user_id IN (?)', array_keys($users))
			->query()
			->fetchAll();
		foreach ($rows2 as $row2)
			$users[$row2['userId']] = $row2;
		foreach ($rows as &$row) {
			$user = $users[ $row['userId'] ];
			$row['username'] = $user['username'];
			$row['name'] = $user['name'];
			$row['surname'] = $user['surname'];
		}
	}
	$result = array(
		'total' => $total,
		'offset' => $offset,
		'data' => $rows,
	);
	return new It6_ArrayWrapper($result);
	//->join(array('mu' => 'uzivatel'), 'mu.user_id=mt.user_id', array('name' => 'jmeno', 'surname' => 'prijmeni'))
		
/*
	$tracks = static::getAllWhereInjected(
		array('userId'=>$userId),
		array(
			'group' => function($query) { return $query->groupBy(); }
		)
	);
	$_conv = array_flip(static::$CONV);
	$filter = array('userId' => $userId);
	if (isset($persistentId))
		$filter['persistentId'] = $persitentId;
	if (isset($ip))
		$filter['remoteIp'] = $ip;
	if (isset($extensions))
		$extensions = array();
	$injections = array(
		'orders' => function($query) {
			return $query->order(array("{$_conv['persistentId']} ASC", "{$_conv['time']} DESC"));
		}
	);
	return static::getAllWhereInjected($filter, $injections, $extensions);
*/
}

} // class Webservice_UserTracking
