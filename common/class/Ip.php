<?php
/**
 *
 */
class Ip{
	
	
		/**
	 * Ziskani iso kodu zeme podle IP
	 * WTF?! vraci to oblast.pozice, neni to ani ID zeme, ani ID oblasti
	 *  
	 * @param string|null $ipaddr
	 * @return integer
	 * @access public
	 * @static
	 */
	public static function getCountryId( $ipaddr=null ){
		if(!$longip = ip2long(empty($ipaddr)?self::getAddress():$ipaddr)) return false;
		try {
	
			$q = Zend_Registry::get('db')
				->select()
				->distinct()
				->from(array('ip2c'=>'ip2country'), array())
				->joinLeft(array('o'=>'oblast'),'ip2c.iso=o.iso',array('pozice'))
				->where( 'ip2c.lower_bound<=?', $longip)
				->where( 'ip2c.upper_bound>=?',$longip );
				
//				var_dump($q->__toString());
			$r =$q->query()->fetch();
			return $r['pozice'];
		} catch ( Exception $e ) {
			
			throw new ExHandler($e->getMessage()  . __LINE__,"web_ex_db");
		
		}
		return false; 
	}
	
	/**
	 * Ziska IP adresu klienta.
	 * 
	 * @return string
	 * @access public
	 * @static
	 */
	public static function getAddress(){
		$ip = It6_Php::getRemoteAddr();
		if ( !empty($ip) ) return $ip;
		return false;
		
	}
	
}