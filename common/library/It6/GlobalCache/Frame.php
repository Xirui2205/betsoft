<?php
class It6_GlobalCache_Frame {

public static function render($path, $asString = false) {
	$uriBackup = $_SERVER['REQUEST_URI'];
	$_SERVER['REQUEST_URI'] = $path;
	$lock = null;
	if ($asString) {
		ob_start();
	}
	if ( !It6_GlobalCache_Starter::begin($lock) ) {
		require_once(ROOT.'web/config_local.php');
		require_once(ROOT.'common/includes.inc.php');
		require_once ROOT.'web/application/bootstrap.php';
		require_once ROOT.'common/library/It6/GlobalCache.php';
		It6_GlobalCache::reset();
		Bootstrap::run();
		It6_GlobalCache::end($lock);
	}
	$_SERVER['REQUEST_URI'] = $uriBackup;
	if ($asString) {
		return ob_get_clean();
	}
}

} // It6_GlobalCache_Frame
