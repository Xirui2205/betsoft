<html><body>
<?

require_once("mobilem_api_func.inc");
require_once("xmlparser.class.php");
require_once("mobilem_api.class.php");


$mobilem=new MobilemAPI('mujlogin','mojeheslo');

$msg="Ahoj svete jak se mas? Ahoj svete jak se mas? Ahoj svete jak se mas? Ahoj svete jak se mas? Ahoj svete jak se mas? Ahoj svete jak se mas? Ahoj svete jak se mas? Ahoj svete jak se mas? ";
$num='+420602860704';

$res=$mobilem->Send($msg, $num);

printr($res);

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