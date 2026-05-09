<html><body>
<?

require_once("mobilem_api_func.inc");
require_once("xmlparser.class.php");
require_once("mobilem_api.class.php");


$mobilem=new MobilemAPI('mujlogin','mojeheslo');

$msg="Ahoj svete jak se mas?";
$num=Array('602860704','+420777777269');
$recack=1;
$recackaddr='tomas.rosa@dccimobile.com';
$delay=''; 
$waitfordelivery=1;
$nosave=1;
$split='concat';

$res=$mobilem->Send($msg, $num, $recack, $recackaddr, $delay, $waitfordelivery, $nosave, $split);

if ($res['_attributes']['status']=="ok")
{
  echo "Vse je <b>OK</b>, sms je zaslana<br>";
  printr($res);
} else
{
  echo "<b>Chyba cislo:</b> {$res['error']['code']}<br>";
  echo "<b>Popis chyby:</b> {$res['error']['message']}<br>";
}



/*

ted je v $res pokud vse probehlo vporadku

Array
(
    [_attributes] => Array
        (
            [status] => ok
        )

    [sid] => Dgi1B0bDOtJufGxtfs2
    [smsid] => 27006
    [price] => 
    [credit] => 271.81
    [parts] => 1
    [recackaddr] => 
    [gwref] => -1
)

*/

?>

</body></html>