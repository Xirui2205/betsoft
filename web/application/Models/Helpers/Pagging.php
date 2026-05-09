<?php


class Models_Helpers_Pagging{


	 

	/**
	 * Vraci strankovaci data
	 * @param int $count  pocet zaznamu v db
	 * @param int $num pocet zaznamu na strance
	 * @param int $page  na jake stranc jsme
	 * @return array
	 */

	public static function page($count,$num,$page){
		 
		$ret = array();

		if($page != 0 ) $ret['prev'] = $page-1;
		if( floor($count/$num) > $page) $ret['next'] = $page+1;
		 
		for($x=0;$x<ceil($count/$num);$x++){

			$ret['page'][$x] = ($x+1);

			if(($x) == $page) $ret['act'] = ($x+1);

		}
		 
		if(!isset($ret['act'])) $ret['act'] = 1;
		if(!isset($ret['page'])) $ret['page'] = array();
		 
		return $ret;
		 
	}



}