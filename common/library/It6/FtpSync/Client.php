<?php

/**
 * Using these defined constants for default values in constructor:
 *  FTPSYNC_HOSTS ... semicolon separated list of hosts (IPs or hostnames), hosts can contain port number separated by colon (eg. "127.0.0.1:21")
 *  FTPSYNC_USER
 *  FTPSYNC_PASSWD
 */

class It6_FtpSync_Client {

const CONN_TIMEOUT = 60; // seconds

protected $_debug = false;
protected $_hosts = array();
protected $_user = null;
protected $_passwd = null;
protected $_connected = false;
protected $_conns = array();
protected $_errors = array();

public function __construct($hosts = null, $user = null, $passwd = null) {
	if (!isset($hosts))
		$hosts = FTPSYNC_HOSTS;
	if (!isset($user))
		$user = FTPSYNC_USER;
	if (!isset($passwd))
		$passwd = FTPSYNC_PASSWD;
	$this->_hosts = (is_array($hosts) ? $hosts : explode(';', $hosts));
	foreach ($this->_hosts as &$host)
		$host = trim($host);
	$this->_user = $user;
	$this->_passwd = $passwd;
	if ($this->_debug)
		It6_Log::debug("FtpSync client initialized {hosts:[" . implode(',', $this->_hosts) . "]}");
}

public function __destruct() {
	$this->disconnect();
}

public function getErrors() {
	return $this->_errors;
}

public function clearErrors() {
	$this->_errors = array();
}

public function connect($forceReconnect = false) {
       return;
	if ($this->_connected && !$forceReconnect)
		return true;
	$err = false;
	foreach ($this->_hosts as $host) {
		$host = explode(':', $host);
		$port = 21;
		if (1 < count($host)) {
			$_port = intval($host[1]);
			if (!empty($_port))
				$port = $_port;
		}
		$host = $host[0];
		if (!empty($host) && empty($this->_conns["$host:$port"])) {
			if ($this->_debug)
				It6_Log::debug("Connecting via FTP: $host:$port");
			$conn = ftp_connect($host, $port, self::CONN_TIMEOUT);
			if (!$conn) {
				$err = "Cannot connect via FTP: $host:$port";
				$this->_errors[] = $err;
				It6_Log::err($err);
			}
			else if (!ftp_login($conn, $this->_user, $this->_passwd)) {
				$err = "Cannot login to FTP account: {$this->_user} on $host:$port";
				$this->_errors[] = $err;
				It6_Log::err($err);
			}
			else {
				$this->_conns["$host:$port"] = $conn;
			}
		}	
	}
	$this->_connected = true;
	if ($this->_debug)
		It6_Log::debug('Connected: {hosts=[' . implode(',', array_keys($this->_conns)) . ']}');
	return (false === $err);
}

public function disconnect() {
	foreach ($this->_conns as $conn) {
		ftp_close($conn);
	}
	$this->_connected = false;
	$this->_conns = array();
}

/**
 * Takes anonymous function as argument and calls it for each connection passing these arguments:
 *   connection, host, array for error messages
 * @param closure $closure
 * @return array (host => result) map of results for each connection
 */
protected function _forEachConn($closure) {
	$this->connect();
	$result = array();
	foreach ($this->_conns as $host => $conn)
		$result[$host] = $closure($conn, $host, $this->_errors);
	return $result;
	
}

/**
 * Checks if all results in given associative array format have all subresults success value.
 * @param array $result (host => result_value)
 * @param integer $level Level 1 means array (host => result), level 2 means (group => host => result) and so on... 
 * @return boolean
 */
public static function allResultsOK($result, $level = 1) {
	if (1 >= $level) {
		foreach ($result as $hostResult) {
			if (true !== $hostResult)
				return false;
		}
		return true;
	}
	else {
		foreach ($result as $groupedResult) {
			if (true !== static::allResultsOK($groupedResult, $level - 1))
				return false;
		}
		return true;
	}
}

public function ls($dir = null) {
	$fn = function ($conn) use ($dir) {
		$_dir = (isset($dir) ? $dir : ftp_pwd($conn));
		return ftp_nlist($conn, $_dir);
	};
	return $this->_forEachConn($fn);
}

public function pwd() {
	return $this->_forEachConn(function($conn) { return ftp_pwd($conn); });
}

/**
 * @param array $files (local_path => remote_path); local paths are recommended to be absolute, remote path root is relative to local web/www/
 *                     In fact remote_path can be string with path or list with elements (path, [chmod [, overwrite_override]]) where chmod can be FALSE
 * @param boolean $overwrite Set to FALSE if existing target file should not be overwritten
 * @return array (local_path => (host => TRUE|FALSE|NULL)) NULL is for skipped existing file when files should not be overwritten
 */
public function upload($files, $overwrite = true) {
	$this->connect();
	$result = array();
	foreach ($files as $local => $remote) {
		if (empty($local) || empty($remote))
			continue;
		$result[$local] = $this->_forEachConn(function ($conn) use ($local, $remote, $overwrite) {
			if (is_array($remote)) {
				$path = $remote[0];
				$chmod = (isset($remote[1]) ? $remote[1] : false);
				$effOverwrite = (isset($remote[2]) ? $remote[2] : $overwrite);
			}
			else {
				$path = $remote;
				$chmod = false;
				$effOverwrite = $overwrite;
			}
			if (!$effOverwrite) {
				$list = ftp_nlist($conn, $path);
				if (is_array($list)) {
					foreach ($list as $file) {
						if (false !== $file)
							return null;
					}
				}
			}
			if (!ftp_put($conn, $path, $local, FTP_BINARY))
				return false;
			if (false !== $chmod) {
				if (false === ftp_chmod($conn, $chmod, $path))
					return false;
			}
			return true;
		});
	}
	return $result;
}

/**
 * Deletes one or more remote files (remote path root is relative to local web/www/)
 * @param string|array $file
 * @return array For one file (host => TRUE|FALSE), for more files (file => array(host => TRUE|FALSE))
 */
public function delete($file) {
	$files = (is_array($file) ? $file : array($file));
	$result = array();
	foreach ($files as $_file) {
		$result[$_file] = $this->_forEachConn(function ($conn) use ($_file) {
			return ftp_delete($conn, $_file);
		});
	}
	return (is_array($file) ? $result : $result[$file]);
}

/**
* Download file from ftp to $filepath or To temporary locations. Return full path
* @param string file
* @param string filePath
* @return string
*/

public function download($file, $filePath = null) {
	

	if (!empty($file)) {
		$this->connect();

		if (!isset($filePath))
			$filePath = uniqid("/tmp/");

		$result = array();
		foreach ($this->_conns as $host => $conn){
			ftp_get($conn, $filePath, $file,  FTP_BINARY);
			return $filePath;
		}
	}
}

} // class
