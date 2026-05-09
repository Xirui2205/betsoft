<?php

class It6_Acl_Factory_Confirmd extends It6_Acl_Factory {

/**
 * @param array $param [optional]
 *    array(
 *       ['adminDb' => Zend_Db_Adapter for admin database,]
 *    )
 * @returns It6_Acl_Admin instance
 */
public static function createNewAcl($param = null) {
	$dbAdmin = null;
	$identity = array(
		It6_Acl::IDNAME_ADMIN => It6_Models_Admin::ID_INTERNET,
		It6_Acl::IDNAME_BRANCH => It6_Models_Branch::ID_INTERNET,
		It6_Acl::IDNAME_HOST => It6_Models_Host::ID_INTERNET,
	);
	$trees = array(
		array('name' => It6_Acl_Admin::TREE_WS),
	);
	if (is_array($param)) {
		$dbAdmin = (array_key_exists('adminDb', $param) ? $param['adminDb'] : null);
	}
	return It6_Acl_Admin::initFromCache($identity, $trees, $dbAdmin);
}


} // class It6_Acl_Factory_Web
