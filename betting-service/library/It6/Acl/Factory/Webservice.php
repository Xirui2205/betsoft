<?php

class It6_Acl_Factory_Webservice extends It6_Acl_Factory {

const CN_TYPE_BRANCH = 'branch';
const CN_TYPE_CLIENT = 'client';

/**
 * Parses common name in our format and return particular parts from it
 * @param string $cn Common Name from certificate's subject
 * @returns array|boolean FALSE on error or array with keys: type, branchId, hostId, environment
 */
public static function parseSslCertCommonName($cn) {
	static $partsDef = array(
		self::CN_TYPE_BRANCH => array('type', 'branchId', 'hostId', 'environment'),
		self::CN_TYPE_CLIENT => array('type', 'clientId', 'environment'),
	);

	if ('/' == $cn[0]) {
		if (1 == preg_match('!/CN=([^/]*)(?:/|$)!', $cn, $matches))
			$cn = $matches[1];
	}

	$parts = explode('.', $cn);
	if (1 > count($parts))
		return false;
	$type = $parts[0];
	if (!array_key_exists($type, $partsDef)) {
		return false;
	}
	$result = array();
	$i = 0;
	foreach ($partsDef[$type] as $def) {
		$part = $parts[$i++];
		if (!empty($def)) {
			if (empty($part))
				return false;
			$result[$def] = $part;
		}
	}
	return $result;
}

/**
 * Fecthes all needed SSL data from HTTP request
 * @return struct Empty or containing fields: clientVerify, clientIssuerDN, clientSubjectDN_CN
 */
public static function getSslData() {
	$sslData = array();
	if (!empty($_SERVER['SSL_CLIENT_VERIFY'])) {
		$sslData = array(
				'clientVerify' => $_SERVER['SSL_CLIENT_VERIFY'],
				'clientIssuerDN' => (isset($_SERVER['SSL_CLIENT_I_DN']) ? $_SERVER['SSL_CLIENT_I_DN'] : ''),
				'clientSubjectDN_CN' => (isset($_SERVER['SSL_CLIENT_S_DN_CN']) ? $_SERVER['SSL_CLIENT_S_DN_CN'] : ''),
		);
	}
	else if (
		defined('ACL_ALLOW_SSL_DATA_FROM_HTTP_HEADERS')
		&& ACL_ALLOW_SSL_DATA_FROM_HTTP_HEADERS
		&& !empty($_SERVER['HTTP_CLIENT_VERIFY'])
	) {
		$sslData = array(
				'clientVerify' => $_SERVER['HTTP_CLIENT_VERIFY'],
				'clientIssuerDN' => (isset($_SERVER['HTTP_CLIENT_ISSUER']) ? $_SERVER['HTTP_CLIENT_ISSUER'] : ''),
				'clientSubjectDN_CN' => (isset($_SERVER['HTTP_CLIENT_SUBJECT']) ? $_SERVER['HTTP_CLIENT_SUBJECT'] : ''),
		);
	}
	return $sslData;
}

/**
 * Checks if branch app version string match allowed versions
 * @param string $versionString Expected in format "V major.minor" where major and minor are integers
 * @return boolean TRUE if version string is OK, FALSE otherwise
 */
public static function checkBranchAppVersion($versionString) {
	if (preg_match('/^ *[a-zA-Z]+ +(\d+)\.(\d+) *$/', $versionString, $matches)) {
		$major = $matches[1];
		$minor = $matches[2];
	}
	else {
		return false;
	}
	$param = It6_Models_Parameter::getDataByName(It6_Models_Parameter::NAME_BRANCH_APP_ALLOWED_VERSIONS);
	$allowedVersions = array(); 
	if (!empty($param['value'])) {
		foreach (preg_split('/[,;]/', $param['value']) as $token) {
			if (preg_match('/^(\d+)\.(\d+)(?:\s*-\s*(\d+)\.(\d+))?$/', trim($token), $matches)) {
				$allowedVersions[] = array_slice($matches, 1);
			}
		}
	}
	if (empty($allowedVersions)) {
		// restricting only when we have non-empty list
		return true;
	}
	$allowed = false;
	foreach ($allowedVersions as $version) {
		if (2 == count($version)) {
			// one major minor pair
			if ($major == $version[0] && $minor == $version[1]) {
				$allowed = true;
				break;
			}
		}
		else if (4 == count($version)) {
			// interval given by two major minor pairs
			if (
				($major > $version[0] || ($major == $version[0] && $minor >= $version[1]))
				&& ($major < $version[2] || ($major == $version[2] && $minor <= $version[3]))
			) {
				$allowed = true;
				break;
			}
		}
	}
	return $allowed;
}

/**
 * TODO: if WS Wrapper is in DIRECT mode, use alternative way for credentials (there is no SSL certificate)
 * @param array $param [optional]
 *    array(
 *       ['adminDb' => Zend_Db_Adapter for admin database,]
 *       ['webDb' => Zend_Db_Adapter for web database,]
 *    )
 * @returns It6_Acl_Admin instance
 */
public static function createNewAcl($param = null) {
	$dbAdmin = null;
	$dbWeb = null;
	$identity = array();
	$trees = array( array('name' => It6_Acl_Admin::TREE_WS) );
	if (is_array($param)) {
		$dbAdmin = (array_key_exists('adminDb', $param) ? $param['adminDb'] : null);
		$dbWeb = (array_key_exists('webDb', $param) ? $param['webDb'] : null);
		// ...
	}
	$authenticateAdmin = true;
	$sslData = static::getSslData();
	if (defined('LIVE_CLIENT')) {
		$identity[It6_Acl::IDNAME_BRANCH] = It6_Models_Branch::ID_INTERNET_LIVE;
		$identity[It6_Acl::IDNAME_HOST] = It6_Models_Host::ID_INTERNET_LIVE;
		$identity[It6_Acl::IDNAME_ADMIN] = It6_Models_Admin::ID_INTERNET_LIVE;
		$authenticateAdmin = false;
	}
	else if (!empty($sslData['clientVerify']) && 'SUCCESS' == $sslData['clientVerify']) {
		if (defined('WS_SSL_TRUSTED_CA_DN')) {
			if ( !in_array($sslData['clientIssuerDN'], explode("\0", WS_SSL_TRUSTED_CA_DN)) )
				throw new Exception('Unapproved issuer certificate DN: ' . $sslData['clientIssuerDN']);
		}
		$cnData = static::parseSslCertCommonName($sslData['clientSubjectDN_CN']);
		if (false !== $cnData) {
			if (static::CN_TYPE_BRANCH == $cnData['type']) {
				$branchId = $cnData['branchId'];
				$hostId = $cnData['hostId'];
				$identity[It6_Acl::IDNAME_BRANCH] = $branchId;
				$identity[It6_Acl::IDNAME_HOST] = $hostId;
				$systemBranchAdmins = array(
					It6_Models_Branch::ID_INTERNET => It6_Models_Admin::ID_INTERNET,
					It6_Models_Branch::ID_INTERNET_LIVE => It6_Models_Admin::ID_INTERNET_LIVE,
				);
				if (array_key_exists($branchId, $systemBranchAdmins)) {
					$authenticateAdmin = false;
					$identity[It6_Acl::IDNAME_ADMIN] = $systemBranchAdmins[$branchId];
				}
				else
					$trees[] = array('name' => It6_Acl_Admin::TREE_BRANCH);
			}
			else if (static::CN_TYPE_CLIENT == $cnData['type']) {
				$authenticateAdmin = false;
				$identity[It6_Acl::IDNAME_CLIENT] = $cnData['clientId'];
			}
			else
				throw new Exception('Unknown type of SSL certificate CN');
		}
		else
			throw new Exception('Unknown format of SSL certificate\'s subject CN');
	}
	if ($authenticateAdmin) {
		if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']))
			throw new Exception('Insufficient user credentials to authenticate');
		$username = $_SERVER['PHP_AUTH_USER'];
		$passwd = $_SERVER['PHP_AUTH_PW'];
		$user = It6_Models_Admin::readDataByUsername($username, $dbAdmin);
		if (!empty($user)) {
			$user = $user[0];
			if (It6_Models_Admin::passwdMatches($passwd, $user['passwd']))
				$identity[It6_Acl::IDNAME_ADMIN] = $user['id'];
			else
				$user = null;
		}
		if (empty($user))
			throw new Exception('Invalid username and/or password', It6_XmlRpc_Exception::CODE_WRONG_LOGIN_PASSWORD);
	}
	if (empty($trees))
		$trees[] = array('name' => It6_Acl_Admin::TREE_ADMIN);
	$acl = It6_Acl_Admin::initFromCache($identity, $trees, $dbAdmin);
	// test aditional access costraints using our ACL
	if ($authenticateAdmin && isset($identity[It6_Acl::IDNAME_BRANCH])) {
		if (
			!$acl->userHasRole(It6_Acl_Admin::ROLE_BRANCH_ADMIN)
			&& !$acl->userHasRole(It6_Acl_Admin::ROLE_BRANCH_TECHNICIAN)
		) {
			$headers = getallheaders();
			if (!isset($headers['X-Version'])) {
				$version = true; // for backward compatibility
			}
			else {
				$version = static::checkBranchAppVersion($headers['X-Version']);
			}
			if (!$version) {
				throw new Exception(
					'Branch application version is not allowed',
					It6_XmlRpc_Exception::CODE_BRANCH_APP_VERSION_NOT_ALLOWED
				);
			}
			if (!$acl->userHasRole(It6_Acl_Admin::ROLE_BRANCH_EMPLOYEE)
				&& !$acl->userHasRole(It6_Acl_Admin::ROLE_BRANCH_OWNER)
			)
				throw new Exception('Access denied: no role for branch tree');
			else if ($user['branchId'] != $identity[It6_Acl::IDNAME_BRANCH]) {
				$secondaryBranches = It6_Models_AdminSecondaryBranch::getAdminBranches($user['id']);
				if (!in_array($identity[It6_Acl::IDNAME_BRANCH], $secondaryBranches))
					throw new Exception('Access denied: no role for this branch');
			}
		} 
	}
	return $acl;
}

} // class It6_Acl_Factory_Webservice
