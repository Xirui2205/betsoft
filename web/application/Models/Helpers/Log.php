<?php

/**
 * @deprecated [deprekejtid]
 */
class Models_Helpers_Log{




	/**
	 * Loguje do souboru
	 * @param string $type  typ zaznamu
	 * @param int $num pocet zaznamu na strance
	 * @param int $page  na jake stranc jsme
	 * @return bool
	 */

	public static function addFileRecord($type='',$head='',$text=''){
		 
		$date = date('Y_m_d');
		 
		$data = "\n#".$head ."# \n";
		 
		$data .= $text ."\n\n";
		 
		$data .= "========================== \n\n";
		 
		 
		$fp = fopen(LOG.$type .'_'. $date .".log","a");

		fwrite($fp,$data);
		 
		fclose($fp);

		return true;
		 
	}






}