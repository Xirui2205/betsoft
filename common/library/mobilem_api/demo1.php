<html><body>
<?

require_once("mobilem_api_func.inc");
require_once("xmlparser.class.php");
require_once("mobilem_api.class.php");


$mobilem=new MobilemAPI('mujlogin','mojeheslo');
$res=$mobilem->Info();

printr($res);
/*

ted je v $res pokud vse probehlo vporadku

Array
(
    [_attributes] => Array
        (
            [status] => ok
        )

    [sid] => 0VT67QserCm9Rt1g8YT
    [login] => trosa
    [name] => Tomáš
    [surname] => Rosa
    [email] => trosa@seznam.cz
    [credit] => 271.81
    [lastincome] => 2004-05-04 01:02:37
    [auth_phone] => 
    [auth_email] => 1
    [account_number] => 100000044
    [bank_number] => 49799028/2400
    [nick] => trosa
    [nick_active] => yes
)

*/

?>

Ahoj, tvoje jméno je <?=trim($res['name'].' '.$res['surname'])?> a tvùj kredit na mobilem.cz je <?=sprintf("%.2f",$res['credit'])?>Kè. A pokud je to máílo, 
pak jsou penízky vítány na úètì <?=$res['bank_number']?> pod variabilním sybolem <?=$res['account_number']?>.

</body></html>