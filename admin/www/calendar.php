<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{ISOLANG}" xml:lang="{ISOLANG}">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="Content-Language" content="{ISOLANG}" />
 <meta name="keywords" content="on-line casino, games, offshore, secure, legal, chance, licensed, regulated, on-line," />
 <meta name="description" content="Play on-line games for real money or for free. " />
 <meta name="abstract" content="" />
 <meta name="robots" content='index,follow' />
 <meta name="googlebot" content='index,follow,snippet,archive' />
 <meta name="Author" content="" />
 <!-- CACHE - MSIE /-->
 <meta http-equiv='Cache-Control' content='must-revalidate, post-check=0, pre-check=0' />
 <meta http-equiv='Pragma' content='public' />
 <!-- CACHE - MSIE end /-->
 <!-- CACHE - other browsers /-->
 <meta http-equiv='Cache-Control' content='no-cache' />
 <meta http-equiv='Pragma' content='no-cache' />
 <meta http-equiv='Expires' content='-1' />
 <!-- CACHE - other browsers end /-->
 <!-- BROWSER SPECIFIC FEATURES = ALL OFF /-->
 <!-- MSIE - 'helpful' features /-->
 <meta http-equiv='MSThemeCompatible' content='no' />
 <meta name='MSSmartTagsPreventParsing' content='TRUE' />
 <!-- OPERA - image resizing /-->
 <meta name='autosize' content='off' />
 <!-- BROWSER SPECIFIC FEATURES = end /-->
    <title>Kalendar </title>
 <link rel='shortcut icon' type='image/x-icon' href='_img/' />
<link rel="stylesheet" href="css/blueprint/screen.css?r=<?= RELEASE_REV ?>" type="text/css" media="screen, projection">
<link rel="stylesheet" href="css/blueprint/print.css?r=<?= RELEASE_REV ?>" type="text/css" media="print">
 <link rel="stylesheet" href="css/adm.css?r=<?= RELEASE_REV ?>" media="screen" type="text/css" />

 <link rel="stylesheet" href="/css/print.css?r=<?= RELEASE_REV ?>" media="print" type="text/css" />
</head>
<SCRIPT>

//prototyp tridy
function Calendar(){
 var node;
 var startDay = "Mo"; //Mo,Tu,We,Th,Fr,Sa,Su
 var days = new Array();
 var months = new Array();
 var datum = new Date();
 var monthsDayNum;
 var actMonth;
 var actYear;
 var actDay;
 var obj;
 var vrat;

 this.datum = new Date();
 this.actMonth = this.datum.getMonth();
 this.actYear = this.datum.getFullYear();
 this.actDay = this.datum.getDate();

}

//Nastavi objekt pro zapis kalendare
Calendar.prototype.setObj = function(o){
 if(document.getElementById) this.obj = document.getElementById(o);
 else if(document.all)       this.obj = document.all[o];
 else if(document.layers)    this.obj = document.layers[o];
 else                        this.obj = eval("document."+o);
}

//samotne vypsani kalendare do objektu
Calendar.prototype.writeIntoObj = function(){
 if(document.layers){
  this.obj.document.open();
  this.obj.document.write(this.vrat);
  this.obj.document.close();
 }else{
  this.obj.innerHTML = this.vrat;
 }
}

//posunuti o mesic doprava nebo doleva
Calendar.prototype.newM = function(m){

  if(m < 0) {this.actYear -= 1;this.actMonth = 11;}
  else if(m > 11) {this.actYear += 1;this.actMonth = 0;}
  else this.actMonth = m;

  this.writeCalendar();
}

//vybrani jineho roku ze selectu
Calendar.prototype.newY = function(m){

  this.actYear = m;

  this.writeCalendar();

}

//vypise zvolene datum
Calendar.prototype.returnDate = function(d,m,r){
 var cas = "";
 cas = d+"."+m+"."+r;

 if(document.getElementById('hodiny').value.length > 0)
    cas = cas+" "+document.getElementById('hodiny').value;

 window.opener.document.getElementById('<?php if(isset($_GET['cas']))echo $_GET['cas'];?>').value = cas

 self.close();
}

//vyplni options pole na aktualni datum
Calendar.prototype.assignOptions = function(){
 /*ROKY*/
 if(document.getElementById) var obj = document.getElementById('roky');
 else if(document.all)       var obj = document.all['roky'];
 else if(document.layers)    var obj = document.layers['roky'];
 else                        var obj = eval("document.roky");


 obj.length = 0;
 for(var i=1920;i<=2099;i++){
  o = new Option(i,i,0,(this.actYear==i)?1:0);
  obj.options[obj.options.length] = o;
 }

 /*MESICE*/
 if(document.getElementById) var obj = document.getElementById('mesice');
 else if(document.all)       var obj = document.all['mesice'];
 else if(document.layers)    var obj = document.layers['mesice'];
 else                        var obj = eval("document.mesice");

 obj.length = 0;
 for(var i=0;i<this.months.length;i++){
  o = new Option(this.months[i],i,0,(this.actMonth==i)?1:0);
  obj.options[obj.options.length] = o;
 }

}

//samotne vypsani kalendare do promenne
Calendar.prototype.writeCalendar = function(){
  this.vrat = "";
  /*Hlavicka*/
  this.vrat += "<table id=\"calendar\"><thead><tr ><td colspan=\"7\" class=\"year\"><a href=\"javascript:c.newM(c.actMonth - 1);void(0);\">&lt;&lt;</a> "+this.months[this.actMonth]+" "+this.actYear+" <a href=\"javascript:c.newM(c.actMonth + 1);void(0);\">&gt;&gt;</a></td></tr> <tr >";
  for(var x=0;x<7;x++) this.vrat += "<td class=\"day\">"+this.days[x]+"</td>";
  this.vrat += "</tr></thead>";
  /*Telo*/
  this.datum.setFullYear(this.actYear,this.actMonth,1);
  this.vrat += "<tbody><tr>";

  first = this.datum.getDay()-1;
  if(first == -1) first = 6;
  pom = 0;
  begin = false;

  for(var i=0;i<42;){

    if(pom == first) begin = true;
    if(begin) i++;
    if(begin && i<=this.monthsDayNum[this.actMonth])this.vrat += "<td style=\""+(pom==6?"background:#7cb6a4;color:white":"")+"\"><a style=\""+(pom==6?"color:white":"")+"\" href=\"javascript:c.returnDate("+i+","+parseInt(this.actMonth+1)+","+this.actYear+");void(0);\">"+i+"</a></td>"; else this.vrat += "<td>&nbsp;</td>";

    pom ++;
    if(pom >= 7) pom = 0;
    if(pom == 0) this.vrat += "</tr><tr>";

    if(i>=this.monthsDayNum[this.actMonth] && pom == 0) break;
  }

  this.vrat += "</tr></tbody></table>";
  this.vrat += "<table style=\"color:#515B73;font-size:1.0em;\"><tr><td colspan=\"7\">Hodiny: <input type=\"text\" class=\"sinput\" style=\"width:83px;\" name=\"hodiny\" value=\"<?php echo (isset($_GET['hodiny']) ? $_GET['hodiny'] : '10:00:00'); ?>\" id=\"hodiny\" /></td></tr></table>";

  this.writeIntoObj();
}

window.onload = function(){

	
c = new Calendar();
c.node = document.body;
c.setObj("calendar");
c.days = ["Po","Ut","St","Ct","Pa","So","Ne"];
c.months = ["Leden","Únor","Březen","Duben","Květen","Červen","Červenec","Srpen","Září","Říjen","Listopad","Prosinec"];
c.monthsDayNum = [31,29,31,30,31,30,31,31,30,31,30,31];
c.assignOptions();
c.writeCalendar();
window.resizeTo(290,400);
}
</SCRIPT>
<body >
<table class="unitable">
<tr><td>Rok:</td><td>
<select style="color:#515B73;width:94px;" id="roky" onchange="c.newY(parseInt(this.options[this.selectedIndex].value));">

</select></td></tr>

<tr><td>Mesic:</td><td>
<select  id="mesice" style="color:#515B73;width:94px;" onchange="c.newM(parseInt(this.options[this.selectedIndex].value));">

</select></td></tr>
</table>

<div id="calendar"></div>




</body>
</html>
