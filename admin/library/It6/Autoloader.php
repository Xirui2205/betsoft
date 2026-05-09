<?php

//class It6_Autoloader extends Zend_Loader
class It6_Autoloader implements Zend_Loader_Autoloader_Interface
{
	public function loadClass($class, $dirs = null) {
//		try {
			//$paths = Zend_Loader::explodeIncludePath();
			$fileName = str_replace('_', '/', $class, $count) . '.php';
			$loaded = false;
			if (!$count)
				$loaded = Zend_Loader::loadFile('class.' . $fileName, $dirs, true);
			if (!$loaded)
				$loaded = Zend_Loader::loadFile($fileName, $dirs, true);
			
//		}
//		catch (Exception $e) {
//			Zend_Loader::loadClass($class, $dirs);
//		}
	}

	public function autoload($class) {
		try {
			$this->loadClass($class);
			return $class;
		} catch (Exception $e) {
			return false;
		}
	}
}
