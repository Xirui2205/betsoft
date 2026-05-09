<?php

interface It6_Cron_Job {

	/**
	 * Method to be overriden in descendant classes.
	 * @param array $params Job specific parameters. Each key is array of values.
	 * @param string $errorMessage [optional] returned error message in case of errorMessage
	 * @returns int Job execution result: 0 ... OK; <>0 ... error code
	 */
	public function execute(array $params = array(), &$errorMessage = null);

}






