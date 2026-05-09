<?php
/**
 * @package    main
 */

//TODO:ADMIN cela trida refaktorovat => pouzit misto toho It6_Models_Admin

 
class Administrator{

public $jmeno;
public $prijmeni;
public $nick;
public $zakazany;
public $email;
public $telefon;
public $superadmin;
private $adminId;

private $db;

  public function __construct($adminId,$db){
  
    $this->db =  $db;
    $this->adminId = $adminId;
	
  }

  /*Povoleni administratora*/
  public function Povolit(){
  
    $sql = "update admin set access=0 where admin_id=".$this->adminId;
    $res =& $this->db->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: povoleni administratora',"admin_ex_db");
  
    return true;
	
  }
  
  /*Povoleni administratora*/
  public function Zakazat(){
  
    $sql = "update admin set zakazany=1 where admin_id=".$this->admin_id;
    $res =& $this->db->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: zakazani administratora',"admin_ex_db");
  
    return true;
  
  }
  
  public function selectData(){
  
    $sql = "select admin_id,jmeno,prijmeni,nick,email,heslo,zakazany,telefon,superadmin from admin where admin_id=".$this->admin_id;
    $res =& $this->db->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani administratora',"admin_ex_db");
  
    if($row =& $res->fetchRow()){
	
	  $this->jmeno = $row['jmeno'];
	  $this->prijmeni = $row['prijmeni'];
	  $this->email = $row['email'];
	  $this->nick = $row['nick'];
	  $this->telefon = $row['telefon'];
	  $this->zakazany = $row['zakazany'];
	  $this->superadmin = $row['superadmin'];
	
	}
	
	
  }
  
}

?>