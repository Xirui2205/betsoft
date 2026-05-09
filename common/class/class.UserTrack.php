<?php


class UserTrack {

    private static $loaded_user_id = false;
    private static $userdata = array();

    public static function touch($user_id,$nick) {
        global $_COOKIE;
        global $_SERVER;
        // zapis do antifraud tabulky
        $sql = "insert into user_tracking
                (ts, persistent_id, session_id, remote_ip, xforward, useragent, user_id, nick)
                values (
                NOW(),
                '".Help::slash($_COOKIE["CLICKTRACKS_P"])."',
                '".Help::slash($_COOKIE["PHPSESSID"])."',
                '".Help::slash(It6_Php::getRemoteAddr())."',
                '".Help::slash(isset($_SERVER["HTTP_X_FORWARDED_FOR"])?$_SERVER["HTTP_X_FORWARDED_FOR"]:"")."',
                '".Help::slash($_SERVER["HTTP_USER_AGENT"])."',
                ".$user_id.",
                '".Help::slash($nick)."'
                );";
        try {
            Zend_Registry::get('zdb_game')->query($sql);
        } catch ( Exception $e ) {
            throw new ExHandler("user_track touch","game_ex_zeme");
        }
    }


    /**
     * hlavni metoda na check shod pro $user_id
     */
    public static function isShoda($user_id, $limit_shod = 1) {
        return ( self::isShodaPC($user_id, $limit_shod) || self::isShodaIP($user_id, $limit_shod) );
    }
    /**
     * shoda podle IP - atribut xforward
     */
    public function isShodaIP($user_id, $limit_shod = 1) {
        global $ipd; // TODO: nelze to udelat jinak ?!?!
        if(self::$loaded_user_id!=$user_id) self::load4user($user_id);
        $xf = self::buildArr(self::$userdata, "xforward");
        if(count($xf)<1) return false; // max jediny vyskyt je v poradku
        $sql = "select user_id from user_tracking where xforward  in ('','".implode("','",$xf)."') and xforward not in('',".implode(",",$ipd).") and user_id != ".intval($user_id)." group by user_id limit ".($limit_shod+1).""; // limit 1 is for optimization
        try {

            return count(Zend_Registry::get('zdb_game')->fetchAll($sql))>=$limit_shod ;
        } catch ( Exception $e ) {
            throw new ExHandler("user_track isShodaIP ".$res->getMessage(),"game_ex_zeme");
        }
        return false; // an error has occured
    }
    /**
     * shoda podle PC - atribut persistant_id
     */
    public function isShodaPC($user_id, $limit_shod = 1) {
        global $ipd; // TODO: nelze to udelat jinak ?!?!
        if(self::$loaded_user_id!=$user_id) self::load4user($user_id);
        $pid = self::buildArr(self::$userdata, "persistent_id");
        if(count($pid)<1) return false; // max jediny vyskyt je v poradku
        $sql = "select user_id from user_tracking where persistent_id<>'' and persistent_id in ('".implode("','",$pid)."') and user_id != ".intval($user_id)." and xforward not in('',".implode(",",$ipd).") group by user_id limit ".($limit_shod+1).""; // limit 1 is for optimization
        try {

            return count(Zend_Registry::get('zdb_game')->fetchAll($sql))>=$limit_shod ;
        } catch ( Exception $e ) {
            throw new ExHandler("user_track isShodaPC xxx $sql xxx","game_ex_zeme");
        }
        return false; // an error has occured
    }
   /**
     * shoda podle PC a IP Affiliate vs User
     */
    public function isShodaAffUser($user_id,$af_user_id) {
        global $ipd; // TODO: nelze to udelat jinak ?!?! UDELANO RYCHLEJI PRIMO VE SKRIPTU class.AffiliatePartners.php
        $compare['PC'] = false;
        $compare['IP'] = false;
        //$compare = array();
        //if(self::$loaded_user_id!=$user_id) self::load4user($user_id);
        //$pid = self::buildArr(self::$userdata, "persistent_id");
        $sql = "select a.persistent_id,a.xforward from user_tracking a where
        a.persistent_id<>'' and user_id = ".intval($user_id)."
        and xforward not in('',".implode(",",$ipd).")";
        try {

            $res = Zend_Registry::get('zdb_game')->fetchAll($sql);

            foreach($res as $h){

                $sql = "select a.persistent_id,a.xforward from user_tracking_af a where
                 a.persistent_id<>'' and user_id = ".intval($af_user_id)."
                 and xforward not in('',".implode(",",$ipd).")";
                try{
                 $res2 = Zend_Registry::get('zdb_game')->fetchAll($sql);

                 foreach($res2 as $h2){

                    if($h['persistent_id'] == $h2['persistent_id']) $compare['PC'] = true;
                    if($h['xforward'] == $h2['xforward']) $compare['IP'] = true;

                 }

                }catch(Exception $e){

                    throw new ExHandler("user_track isShodaAffUser xxx $sql xxx","game_ex_zeme");

                }


            }

            return $compare;

        } catch ( Exception $e ) {
            throw new ExHandler("user_track isShodaAffUser xxx $sql xxx","game_ex_zeme");
        }
        return false; // an error has occured
    }
    /**
     *
     */
    private function load4user($user_id) {
        global $ipd; // TODO: nelze to udelat jinak ?!?!
        self::$loaded_user_id = $user_id;
        $sql = "select user_id,xforward,persistent_id from user_tracking where user_id = ".intval($user_id)." and xforward not in('',".implode(",",$ipd).")";
        try {
            self::$userdata = Zend_Registry::get('zdb_game')->fetchAll($sql);
        } catch ( Exception $e ) {
            throw new ExHandler("user_track loaddata xxx $sql xxx","game_ex_zeme");
        }
    }
    /**
     *
     */
    private function buildArr($arr,$key) {
        $o = array();
        if(is_array($arr)) foreach($arr as $v) $o[] = Help::Slash($v[$key]);
        return $o;
    }

}

?>
