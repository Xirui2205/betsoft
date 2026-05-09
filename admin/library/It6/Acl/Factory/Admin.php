<?php

class It6_Acl_Factory_Admin extends It6_Acl_Factory {

/**
 * TODO: if WS Wrapper is in DIRECT mode, use alternative way for credentials (there is no SSL certificate)
 * @param array $param [optional]
 *    array(
 *       ['adminDb' => Zend_Db_Adapter for admin database,]
 *       ['adminId' => admin ID for identity,]
 *    )
 * @returns It6_Acl_Admin instance
 */
public static function createNewAcl($param = null) {
	$dbAdmin = null;
	$identity = array(
		It6_Acl::IDNAME_BRANCH => It6_Models_Branch::ID_INTERNET,
		It6_Acl::IDNAME_HOST => It6_Models_Host::ID_INTERNET,
	);
	$trees = array(
		array('name' => It6_Acl_Admin::TREE_ADMIN),
		array('name' => It6_Acl_Admin::TREE_WS),
	);
	if (is_array($param)) {
		$dbAdmin = (array_key_exists('adminDb', $param) ? $param['adminDb'] : null);
		if (array_key_exists('adminId', $param))
			$identity[It6_Acl::IDNAME_ADMIN] = $param['adminId'];
	}
	return It6_Acl_Admin::initFromCache($identity, $trees, $dbAdmin);
}

} // class It6_Acl_Factory_Admin
