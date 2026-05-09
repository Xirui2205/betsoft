<?php
/**
 * @package    main
 */

 
class UzivatelHandle{
	

/**
 * Vytvoreni uzivatele
 * @param array $data pole dat
 * @param array $money pole dat do tab zustatku
 * @param int $pobocka_id id pobocky
 * @return int  id uzivatele
 */
	public static function insertUser($data,$money,$pobocka_id=null){
		
	 try{

         Zend_Registry::get('zdb_game')->insert('uzivatel', $data);

     }catch(Zend_Exception $e){
     	
          throw new ExHandler($e->getMessage()  . __LINE__,"admin_ex_db");

     }
		
     $user_id = Zend_Registry::get('zdb_game')->lastInsertId();
		
     $money['user_id'] = $user_id;

     /*
  	 try{
  	 	Zend_Registry::get('zdb_game')->insert('uzivatel_im_data', 0);
       //TODO tohle by se asi melo vyhodit, nedava to smysl
         	 	
		if ( $money > 0 ) {			
			$transaction['value'] = $money;
			$transaction['userId'] = $user_id;
			$transaction->typeId = 8;
			$transaction->currencyId = 8;
			
        	Zend_Registry::get('ws')->Transaction->make($transaction);	
		}
		
     }catch(Zend_Exception $e){
     	
          throw new ExHandler($e->getMessage()  . __LINE__,"admin_ex_db");

     }
     */
     
     if($pobocka_id != null){
     	
     	
     try{

         Zend_Registry::get('zdb_game')->insert('uzivatel_pobocka', array("pobocka_id"=>$pobocka_id,"user_id"=>$user_id));

     }catch(Zend_Exception $e){
     	
          throw new ExHandler($e->getMessage()  . __LINE__,"admin_ex_db");

     }
     	
     }
     
     return $user_id;
     
	}
	
	
}