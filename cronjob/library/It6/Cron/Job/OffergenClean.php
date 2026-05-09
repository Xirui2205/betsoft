<?php

class It6_Cron_Job_OffergenClean extends It6_Cron_Job_Abstract {

	public function execute(array $params = array(), &$errorMessage = null) {

		$db = Zend_Registry::get('db');

		$db->delete("offergen_tiskopis",array(
				"DATEDIFF(DATE(NOW()), DATE(pozadavek)) > 30"
			)
			);

		$pathToOutput = ROOT . "offergen/output/";
		exec("find " . $pathToOutput . "*.pdf -mtime +30 -exec rm {} \;");

		exec("find " . $pathToOutput . "*.tex -mtime +30 -exec rm {} \;");
		
		exec("find " . $pathToOutput . "*.txt -mtime +30 -exec rm {} \;");

		$pathToWorkdir = ROOT . "offergen/workdir/";
		exec("find " . $pathToWorkdir . "*.tex -mtime +30 -exec rm {} \;");

		}

}
