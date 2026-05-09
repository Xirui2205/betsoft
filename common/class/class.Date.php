<?php
/**
 * @package    service
 */



class Date {

  /**
   * ISO format datumu
   * @var string
   */
  const isoPattern = 'Y-m-d H:i:s';

  /**
   * vraci aktualni datum v ISO formatu
   * @return string datum napr. '2006-02-01 23:34:50'
   */
  public static function getIsoDateNow(){
    return self::getIsoDate(time());
  }

  /**
   * vraci datum v ISO formatu podle vlozeneho timestampu
   * @param int $timestamp UNIXovy timestamp (pocet vterin od ...)
   * @return string datum napr. '2006-02-01 23:34:50'
   */
  public static function getIsoDate($timestamp){
    return date(self::isoPattern,$timestamp);
  }

  /**
   * vraci datum/cas v UNIX formatu (pocet sekund)
   * @param int $dt datetime v iso formatu z db
   * @param char $splitter znak oddelujici datum od casu (v ASP to je napr. 'T' !!!)
   * @return int pocet sekund
   */
  public static function getUnixDate($dt,$splitter = ' ') {
    //ereg("([0-9]{4})-*([0-9]{1,2})-*([0-9]{1,2})".$splitter."*([0-9]{1,2}):*([0-9]{1,2}):*([0-9]{1,2})",$dt,$dat);
    if (1 == preg_match('/^(\\d{4})-(\\d{2})-(\\d{2})'.$splitter.'(\\d{2}):(\\d{2}):(\\d{2})$/', $dt, $dat))
    return mktime($dat[4],$dat[5],$dat[6],$dat[2],$dat[3],$dat[1]);
  }

 /**
   * vraci datum/cas formatovane podle zadaneho predpisu
   * @param int dt datetime v iso formatu z db
   * @param string predpis formatovaci predpis
   * @return string formatovane datum
   */
  public static function formatDate($dt,$predpis = "j.n.Y G:i:s") {
    //ereg("([0-9]{4})-*([0-9]{1,2})-*([0-9]{1,2}) *([0-9]{1,2}):*([0-9]{1,2}):*([0-9]{1,2})",$dt,$dat);
    if (1 == preg_match('/^(\\d{4})-(\\d{2})-(\\d{2}) (\\d{2}):(\\d{2}):(\\d{2})$/', $dt, $dat))
      return date($predpis,mktime($dat[4],$dat[5],$dat[6],$dat[2],$dat[3],$dat[1]));
    else
      return '';
  }

  /**
   * vraci vstup d.m.y h:m:s v ISO formatu
   * @param int dt vstupujici string ve formatu naseho data d.m.y h:m:s
   * @return string formatovane datum do ISO
   */
  public static function format2ISO($dt) {
    //ereg("([0-9]{1,2}).([0-9]{1,2}).([0-9]{2,4}) *([0-9]{1,2})*:*([0-9]{1,2})*:*([0-9]{1,2})*",$dt,$dat);
    //return empty($dt)?"":self::getIsoDate(mktime($dat[4],$dat[5],$dat[6],$dat[2],$dat[1],$dat[3]));
    if (1 == preg_match('/^(\\d?\\d)\\.(\\d?\\d)\\.(\\d{2,4}) (\\d?\\d):(\\d?\\d):(\\d?\\d)$/', $dt, $dat))
      self::getIsoDate(mktime($dat[4],$dat[5],$dat[6],$dat[2],$dat[1],$dat[3]));
    else
      return '';
  }

}
