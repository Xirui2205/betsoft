<?php
/**
 * @package    help
 */


 /**
 * Trida pro praci se konstantami
 *
 *
 * @package    main
 */

class Konstanty{

/**
 * vystupni XML response
 * @access private
 * @var int
 */
 private $vrat;

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 *ma se ukazat filtr formular
 * @access private
 * @var bool
 */
private  $nomenu=false;


/**
 * cas mezi zacatkem a konce scriptu
 * @access private
 * @var int
 */
private  $time;

/**
 * pole top vyhernich tiketu
 * @access private
 * @var array
 */
private  $vyhra_ar;

/**
 * pole top prohernich tiketu
 * @access private
 * @var array
 */
private  $prohra_ar;

/**
 * pole s informacemi
 * @access private
 * @var array
 */
public  $infoGlobal;

/**
 * identifikator zda vyheldavame podle parametru
 * @access private
 * @var bool
 */
private $dateBool = false;

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
* @param PEAR::DB $db objekt spojeni s databazi
*/
  public function __construct($section){

    $this->section =  $section;

    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");


  }

   /**
 * Metoda spousti jednitlove metody podle stavu

 * @return void
 */
  public  function runAction(){


   if(isset($_POST['new'])){

    $this->createConst();

    }
    else if(isset($_POST['edit'])){

    $this->editConst(intval(key($_POST['edit'])));

    }

    $this->showForm();



    $this->dbGame->disconnect();

  }


 /**
 * Editace konstanty
 * @param int $id id konstanty
 * @return void
 */
  private function editConst($id){

   $status = true;


   if(!isset($_POST['nazev_t'][$id]) || mb_strlen(trim($_POST['nazev_t'][$id])) > 45 || mb_strlen(trim($_POST['nazev_t'][$id])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> musí mít 1-40 znaků</div><br />";$status = false;}
   if(!isset($_POST['hodnota_t'][$id]) || mb_strlen(trim($_POST['hodnota_t'][$id])) > 255 || mb_strlen(trim($_POST['hodnota_t'][$id])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Hodnota</strong> musí mít 1-255 znaků</div><br />";$status = false;}

   if($status){

     $sql = "SELECT nl_id FROM t_cfg where nl_id<>".$id." and s_nazev='".mb_strtoupper(Help::Slash($_POST['nazev_t'][$id]))."'";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

      if($res->numRows()>0) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> již existuje</div><br />";$status = false;}

   }


   #vsechno je  vporadku muzeme zapisovat#
   if($status){

        $sth = $this->dbGame->prepare("update  t_cfg set  dt_in=?,s_nazev=?,s_hodnota=?,s_popis=?,datovy_typ=? where nl_id=?");
        if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

        $res2 =& $this->dbGame->execute($sth,array(It6_Date::dbNow(),mb_strtoupper($_POST['nazev_t'][$id]),$_POST['hodnota_t'][$id],$_POST['popis_t'][$id],$_POST['type_t'][$id],$id));
        if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vlozit baner',"admin_ex_db");

        $this->vrat .= "<div class=\"okmsg\">Konstanta ".Help::Html($_POST['nazev_t'][$id])." byla úspěšně editována</div><br />";

		It6_Log::info(
			"Constant '%constant%' was updated.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('constant' => $_POST['nazev_t'][$id])
		);
        // obnovi cache
        Constant::get($_POST['nazev_t'][$id],true);
   }

  }


 /**
 * Vytvoreni konstanty
 * @return void
 */
  private function createConst(){

   $status = true;


   if(!isset($_POST['nazev']) || mb_strlen(trim($_POST['nazev'])) > 45 || mb_strlen(trim($_POST['nazev'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> musí mít 1-40 znaků</div><br />";$status = false;}
   if(!isset($_POST['hodnota']) || mb_strlen(trim($_POST['hodnota'])) > 255 || mb_strlen(trim($_POST['hodnota'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Hodnota</strong> musí mít 1-255 znaků</div><br />";$status = false;}

   if($status){

     $sql = "SELECT nl_id FROM t_cfg where s_nazev='".mb_strtoupper(Help::Slash($_POST['nazev']))."'";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

      if($res->numRows()>0) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> již existuje</div><br />";$status = false;}

   }


   #vsechno je  vporadku muzeme zapisovat#
   if($status){

        $sth = $this->dbGame->prepare("insert into t_cfg (dt_in,s_nazev,s_hodnota,s_popis,datovy_typ) values (?,?,?,?,?)");
        if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

        $res2 =& $this->dbGame->execute($sth,array(It6_Date::dbNow(),mb_strtoupper($_POST['nazev']),$_POST['hodnota'],$_POST['popis'],$_POST['type']));
        if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vlozit baner',"admin_ex_db");

        $this->vrat .= "<div class=\"okmsg\">Konstanta ".Help::Html($_POST['nazev'])." byla úspěšně vytvořena</div><br />";

		It6_Log::info(
			"Constant created: '%constant%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('constant' => $_POST['nazev'])
		);
        // obnovi cache
        Constant::get($_POST['nazev'],true);
   }

  }

   /**
 * Vypis formulare
 * @return void
 */
  public function showForm(){


   $sth = $this->dbGame->prepare("select * from t_cfg");
   if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

   $res2 =& $this->dbGame->execute($sth);
   if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vlozit baner',"admin_ex_db");

   $this->vrat .= '<h3>Konstanty</h3>';


   $this->vrat .= "<form method=\"post\" action=\"?section=".$this->section."\">";


   $this->vrat .= '<table class="table-default">';

   $this->vrat .= '<thead><tr><th class=\"span-1\">ID</th><th class=\"span-2\">Název</th><th class=\"span-2\">Hodnota </th><th class=\"span-2\"> Popis</th><th></th><th></th></tr></thead>';

   while ($row =& $res2->fetchRow()){


     $this->vrat .= '<tr>
                     <td>#'.$row['nl_id'].'</td>
                     <td> <input type="text"  name="nazev_t['.$row['nl_id'].']" class="input-medium" value="'.$row['s_nazev'].'" /></td>
                    <td> <input type="text"  name="hodnota_t['.$row['nl_id'].']" value="'.$row['s_hodnota'].'" /></td>
                    <td> <input type="text"  name="popis_t['.$row['nl_id'].']" class="input-long" value="'.$row['s_popis'].'" /></td>
                    <td> <select  name="type_t['.$row['nl_id'].']"><option value="string" '.($row['datovy_typ']=='string'?'selected="selected"':'').'>string</option><option value="integer" '.($row['datovy_typ']=='integer'?'selected="selected"':'').'>integer</option><option value="double" '.($row['datovy_typ']=='double'?'selected="selected"':'').'>double</option></select></td>
                    <td><input type="submit" name="edit['.$row['nl_id'].']" class="inputs" value="Edit" /></td>
                     </tr>';


   }

   $this->vrat .= '</table><br />';

   $this->vrat .= '<h3>Nová konstanta</h3>';

   $this->vrat .= '<table class="unitable" >';
   $this->vrat .= '<tr><thead><th class=\"span-2\">Název max. 40 znaků</th><th class=\"span-2\">Hodnota </th><th class=\"span-2\"> Popis</th></thead></tr>';
   $this->vrat .= '<tr><td> <input type="text"  name="nazev" class="" value="'.(isset($_POST['nazev'])?Help::Html($_POST['nazev']):"").'" /></td>
                    <td> <input type="text"  name="hodnota" class="" value="'.(isset($_POST['hodnota'])?Help::Html($_POST['hodnota']):"").'" /></td>
                    <td> <input type="text"  name="popis" class="" style="width:300px" value="'.(isset($_POST['popis'])?Help::Html($_POST['popis']):"").'" /></td>
                    <td> <select  name="type"><option value="string">string</option><option value="integer">integer</option><option value="double">double</option></select></td>
                    </tr>';
   $this->vrat .= '<tr><td colspan="4"><input type="submit" name="new" class="inputs" value="Create" /></td></tr>';
   $this->vrat .= '</table></form>';



  }

   /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
  public function setPrivileges($update,$delete){

   $this->update = $update;
   $this->delete = $delete;

  }

 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function getContent(){


    return $this->vrat;

  }

  public function __destruct(){



  }

}

?>
