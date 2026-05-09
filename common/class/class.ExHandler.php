<?php
/*

 
 Trida a funkce pro praci s chybami
 
 myErrorHandler je funkce, ktera se stara o systemove chyby,
 ktere vypise, zapise do error logu a  vpripade fatalni chyby posle adminovi.
 POZOR tato funkce nezachytava vsechny chyby viz. manual PHP.
 
 Trida exHandler dedi od defaul php fukce Exception
 Slouzi k zachytavani vyjimek podle try..catch 
 Umoznuje vypis chyby na obrazovku, do souboru, poslani na mail a zapis do databaze
 Jednotlive akce se daji provadet individualne

 Error log je zvlast pro systemove chyby (sys_) a pro vyjimky (ex_)
 
 Example how to use
 
 Trida ktera zachyti exception a obslouzi ji
 
 class test{

 public function __construct(){
  if(true){
   throw new ExHandler('chyba');
  }
 }
 
}

try{
  $foo = new test;
  // echo "Ahoj";
}
catch(ExHandler $e){
   $e->produceAllError();
}

*/


/*
Funkce pro zachytavani Fatalnich chyb, ktere nemohou byt zachyceny jinak
*/
/*function catchFatalError($buffer){
 
 $temp_buffer = $buffer;
 
 $text = strip_tags($temp_buffer);
 
 if(preg_match('/(Fatal error: .+ in .+? on line \d+)/',$text,$matches)){ // pokud doslo k fatal error
  
  $temp_buffer = '
  <h1>Sorry but page is temporary out of work</h1> 
  We apologize for the inconvenience';
  
  $fname = ROOT."errorlog/sys.log";
  $fp = fopen($fname,"a");
  
  if($fp){
   fputs($fp,$matches[0]);
   fclose($fp);
//   chgrp($fname,WEBMINS_GROUP); // TODO: nezabira, groupa zustava stale www-data
  }
       

  if($errno == "E_USER_ERROR"){
    $headers  = "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/plain; charset=iso-8859-1\n";
    $headers .= "X-Priority: 3\n";
    $headers .= "X-MSMail-Priority: Normal\n";
    $headers .= "X-Mailer: php\n";
    $headers .= "From: \"\" \n";

    //mail(ADMINERRORMAIL,"Fatal error ",$matches[0],$headers);
   
    exit;
   
   }
 
 }
$temp_buffer = "dsds";
 return strtolower($temp_buffer);
 
}*/



/*
 Trida pro praci s vyjimkami try..catch
*/
class ExHandler extends Exception{
 
 protected $code;                 //kod
 private $m = "";                //zprava chyby
 
 /*
  * @param 1 $m    chybova zprava
  * @param 2 $code chybovy kod
 */
 public function __construct($m="",$code=0){
  $this->code = $code;
  $this->m = $m;
  parent::__construct($m,0);
 }

 private function getStringCode(){
  return $this->code;
 }
 
 /*
  metoda posle zpravu na admin mail
 */
 public function produceMail(){
 
   $this->produceLog();
 
 }
 
 /*
  metoda posle zpravu do prohlizece
 */
 public function produceText(){
 
  $this->produceLog();
 
 }
 
 /*
  metoda posle zpravu do error logu na serveru
  pro kazdy den existuje jeden soubor
 */
 public function produceLog(){
 	It6_Log::err(
		$this->m,
		It6_Log::TAG_ADMIN,
		array('code' => $this->code),
		$this,
		$this->getFile(),
		$this->getLine());
 }
 
 /*
  metoda posle zpravu do databaze
 */
 public function produceDB(){

   $this->produceLog();
   
 }
 
 /*
  metoda provede vsechny predesle zapisy chyby
 */
 public function produceAllError(){
  //$this->produceMail();
  $this->produceLog();
  //$this->produceDB();
 // $this->produceText();
 }

}