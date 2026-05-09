<?php


final class Constant{

private static $dbGame;	
	
/**
 * spojeni s db
 * 
 * @return void
 */
 private static function dbConnect(){
 
 	self::$dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError(self::$dbGame)) {
      throw new ExHandler(self::$dbGame->getMessage(),"admin_ex_db");
    }
    self::$dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& self::$dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
    
    return true;
    
 }

}




?>
