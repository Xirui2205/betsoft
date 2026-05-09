<?


if (is_local())
{
  //define('XMLAPI_URL',"http://smsgw.cz/api/xmlapi.xp");
} else
{
  //define('XMLAPI_URL',"http://api.mobilem.cz/xmlapi.xp");
}
  


//
// POZOR!!! - pokud pouzivate knihovnu na platforme windows, musite opravit tuto fci - respektive vymazat, a nechat pouze obsah:
//
//  function is_local()
//  {
//    return 0;
//  }
//
function is_local()
{
  /* IT6:
  if ((strpos($_SERVER["SERVER_SOFTWARE"],'(Win32)')) or
      (substr($_SERVER["SERVER_SOFTWARE"],0,18)=="PinkNet Web Server"))
  {           
    return 1;                                                                  
  } else
  {
    return 0;
  }
  */
  return 0;
}


//
// pouze pomocna fce na vypis pole
//
function printr($val)
{
  echo "<hr><pre>";
  print_r($val);
  echo "</pre><hr>";
}


//
// otevre vzdaleny soubor -> stahne ho pomoci curl knihovny
//
function new_getremotefile($file)
{
  //echo "<b>$file</b><br>";
  $ch = curl_init($file);
  curl_setopt ($ch, CURLOPT_HEADER, 0);
  curl_setopt ($ch, CURLOPT_VERBOSE, 0);
  //curl_setopt ($ch, CURLOPT_NOBODY, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $body=curl_exec($ch);

  $err=curl_errno($ch);
  if ($err==0)
  {
    $err=curl_getinfo($ch,CURLINFO_HTTP_CODE);    
  }
  if (!$err) $err=0;
  
  curl_close ($ch);

  //echo "CURLBODY:<br>$body<br>";
  return $body;
}


//
// odstraneni cestiny
//
function cs2cesky($txt)
{
  $txt=strtr($txt,"��������������������؊���ݎ������������","acdeeiorstuuyznnACDEEIORSTUUYZNNOoAaUuEeOo");
  return $txt;
}


//
// opravit tvar cisla na nejaky pouzitelny :)
//
function fix_phone_number($word1)
{
  $word1=trim($word1);
  $word1=str_replace(" ","",$word1);
  $word1=str_replace("(","",$word1);
  $word1=str_replace(")","",$word1);
  $word1=str_replace(",","",$word1);
  $word1=str_replace("/","",$word1);  
  if (substr($word1,0,1)=="+") $num=$word1; else
  if (strlen($word1)==13) $num=$word1; else //+420 602 860 704
  if ((strlen($word1)==12) && (substr($word1,0,1)=="+")) $num=$word1; else //+32 478 489 463
  if (strlen($word1)==10) $num="+42$word1"; else
  if (strlen($word1)== 9) $num="+420$word1"; else $num=$word1;
  
  if (substr($num,0,2)=="00") $num="+".substr($num,2,strlen($num)); 
  return $num;
}

//
// overeni platnosti ceskeho cisla
//
function CheckNumber($num)
{
   $call=substr($num,0,6);
   if ((($call=="+42060") or ($call=="+42072") or ($call=="+42073") or ($call=="+42077")) && (preg_match("/^\s*\+?\d+\s*$/", $num)) &&
      (strlen($num)>7) and (((substr($num,0,2)!="+9") and (substr($num,0,1)!="9")) or 
      ((substr($num,0,2)=="+9") and (substr($num,0,3)!="+90") and (substr($num,0,3)!="+99") and (strlen($num)>=9))))
   {
     return 1;
   } else
   {
     return 0;
   }
}

//
// overeni platnosti ceskeho i zahranicniho cisla
//
function CheckNumber2($num)
{ 
  $num=trim($num);
  $res=1;
  if (((substr($num,0,2)=="+9") or (substr($num,0,1)=="9")) and 
      (!((substr($num,0,2)=="+9") and (substr($num,0,3)!="+90") and (substr($num,0,3)!="+99") and (strlen($num)>=9))))
  {
    $res=0;
    //premium sms!!!
  } else
  if ((!is_numeric($num)) or (substr($num,0,1)=="+"))
  {
    if (!((substr($num,0,1)=="+") && (is_numeric(substr($num,1,strlen($num)))))) $res=0;
  } else
  {
    if (strlen($num)!=9) $res=0;
  }
  if ($res)
  {
    if (substr($num,0,4)=="+420") $res=CheckNumber($num);
  }
  return $res;
}


//
// generuje kod o zadane delce
//
function gen_code($length)
{
  $sid="";
  while ($sid=="")
  {
    mt_srand ((double) microtime() * 1000000);
    $Puddle = 'abcdefghijkmnpqrstuvwxyz23456789';

    for($index=0; $index < $length - 1; $index++)
    {
      $sid .= substr($Puddle, (mt_rand()%(strlen($Puddle))), 1);
    }
    //echo "SID: ".$sid;
  }
  return $sid;
}

?>