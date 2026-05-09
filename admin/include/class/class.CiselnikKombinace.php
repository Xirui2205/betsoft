<?php
/**
 * @package    ciselnik
 */



class CiselnikKombinace extends Ciselnik{
  
  /**
 * text ve fulltextovem vyhledavani
 * @access private
 * @var string
 */
private  $fulltext = "";
  
  public function __construct(){
   
   parent::__construct();
   
   $this->runAction();
   
  }
  
 /**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  protected function runAction(){
    
  	if(!isset($_GET['sazka'])) $_GET['sazka'] = 0;
  	
	$this->colNum = 4;
	if(isset($_GET['multiple'])) $multiple = 1;else $multiple = 0;
	$this->headLine = "Číselník Kombinace<br /><input type=\"button\" onclick=\"CheckAllComb();\" value=\"Vybrat vše\" />
         <script>
         var host=\"". $_SERVER["SERVER_NAME"] ."\"; 
            function CheckAllComb(){
                  
                 $(\"input[type='checkbox']\").each(function(){
                        
                        this.checked = (this.checked?false:true); 

                        SetComb(this,this.value,$(this).attr('value2'),$(this).attr('value3'),$(this).attr('value4'),$(this).attr('value5')); 
                  });
                   
 
            }
           </script>
";
	
	$this->opener = (isset($_GET['obj'])?trim($_GET['obj']):"");
	
	if(!isset($_GET['udalost'])) throw new ExHandler("Neni definovana udalost","admin_ex_page");
	
	$sql = "select * from sazky where platna_do>now() and typ_id in(22,19,32)  and udalost_id=".intval($_GET['udalost']);
    $res2 =& $this->dbGame->query($sql);
    if(DB::isError($res2)) throw new ExHandler($res->getMessage(),"admin_ex_db");
	
	$this->page = new Page($res2->numRows(),10000000,"width=".$_GET['width']."&sazka=".intval($_GET['sazka'])."&udalost=".intval($_GET['udalost'])."&cis_id=".$_GET['cis_id']."&obj=".$this->opener);
	
	$this->HeadFoot();
	
	$sql = "select sazka_id,platna_od,platna_do,text,risk_limit,alias,typ_id from sazky where platna_do>now() and typ_id in(22,19,32) and udalost_id=".intval($_GET['udalost']);
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");
	
	
	$this->vrat .= '<tr><th class="textleft">&nbsp;</th><th>Alias</th><th>ID</th><th>Text</th><th>Platna do</th></tr>';
	
	
	while ($row =& $res->fetchRow()){
	   
	    $this->vrat .= '<tr class="clickable">
			<td>
				<input
					type="checkbox"
					onclick="SetComb(this,'.$row['sazka_id'].',\''.It6_Date::fromDb($row['platna_od']).'\',\''.It6_Date::fromDb($row['platna_do']).'\',\''.Help::Script($row['text']).'\',\''.intval($row['risk_limit']).'\')"
					value="'.$row['sazka_id'].'"
					 value2="'.It6_Date::fromDb($row['platna_od']).'"
					 value3="'.It6_Date::fromDb($row['platna_do']).'"
					 value4="'.Help::Script($row['text']).'"
					 value5="'.Help::Script($row['risk_limit']).'"
					 name="combcheckbox" />
					 <input type="radio" name="combradio" onclick="ReturnComb(\''.$this->opener.'\','.$row['sazka_id'].','.intval($_GET['sazka']).',\''.It6_Date::fromDb($row['platna_od']).'\',\''.It6_Date::fromDb($row['platna_do']).'\',\''.Help::Script($row['text']).'\',\''.intval($row['risk_limit']).'\')" />
			</td>
			<td>'.$row['alias'].'/'.$row['typ_id'].'</td>
			<td>'.$row['sazka_id'].'</td>
			<td>'.Help::Html(Help::TranslateString($row['text'],CZ_LANG_ID,$this->dbGame)).'</td>
			<td>'.It6_Date::fromDb($row['platna_do']).'</td></tr>';
	 }
	
	//$this->vrat .= '<tr><td colspan="'.$this->colNum.'"><input type="button" onclick="ReturnComb(\''.$this->opener.'\')" value="Potvrdit" /></td></tr>';

	
	$this->dbGame->disconnect();

  }
  
 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function ShowResult(){
    
  	if(isset($_GET['multiple'])) $multiple = 1;else $multiple = 0;
  
    return "<input type=\"button\"  value=\"Potvrdit\" class=\"sbutton\" onclick=\"ReturnComb2('".$this->opener."',0,".$multiple.",".$_GET['sazka'].")\"   />  <form method=\"post\">".$this->FillTable()."</form>
     <input type=\"button\"  class=\"sbutton\" value=\"Potvrdit\" onclick=\"ReturnComb2('".$this->opener."',0,".$multiple.",".$_GET['sazka'].")\"   />                    

<script>
 var comb_ch_ar = new Array();
 var comb_ch_ar_od = new Array();
 var comb_ch_ar_do = new Array();
 var comb_ch_ar_text = new Array();
 var comb_ch_ar_risk = new Array();

function SetComb(t,id,od,doo,text,risk){    
	if(t.checked) {
		 comb_ch_ar[comb_ch_ar.length] = id;
		 /*comb_ch_ar_od[comb_ch_ar_od.length] = od;
		 comb_ch_ar_do[comb_ch_ar_do.length] = doo;*/
		 comb_ch_ar_text[comb_ch_ar_text.length] = text;
		 /*comb_ch_ar_risk[comb_ch_ar_risk.length] = risk;*/
	} else {
		for(var x=0;x<comb_ch_ar.length;x++) {
			if(comb_ch_ar[x] == id) comb_ch_ar[x] = 0;
		}
	}
}

</script>
            ";
	
  }
  
}

?>
