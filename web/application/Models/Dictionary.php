<?php

/*
 Description


 */


class Models_Dictionary{


	/**
	 * pole textu a pismen
	 * @access private
	 * @var array
	 */
	public static $data;

	 
	/**
	 * Vraci aktivni pismena
	 * @return array
	 */
	public static function getData(){

			

		$select = Zend_Registry::get('db')->select()->from(array('a'=>'jazyky'),array('a.lc_local'))
		->where('a.iso=?',$_SESSION['lang']);
		$stm  = $select->query();
		$row = $stm->fetchAll();

		if(isset($row[0])) $lc_local = $row[0]['lc_local'];

		$translate = Zend_Registry::get('translate');
		$p = explode('[block]',$translate['dictionary']);

		$x = 0;
		foreach($p as $h){
				
			$p2 = split("\n+",$h,3);
				
			$poleDat = array();$first = '';
			foreach($p2 as $h2){

				if(mb_strlen(trim($h2)) == 0) continue;

				if(count($poleDat) == 0) $first = mb_strtoupper(mb_substr($h2,0,1));

				$poleDat[] = $h2;
				self::$data[$first][$x][] = ucfirst($h2);

			}
				
				
			$x++;
		}

		setlocale(LC_COLLATE, $lc_local);
		uksort (self::$data, "strcoll");

		//echo '<pre>';print_r(self::$data);exit;

		return self::$data;

	}






}
