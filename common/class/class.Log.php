<?php

define("LOG_PATH_PHP_HACK", ROOT.'errorlog/');

class Log {
  const LOG_PATH      = LOG_PATH_PHP_HACK;
  const LOG_DEFAULT   = 'default.log';
	const LOG_SECURITY  = 'security.log';
  const LOG_CRONJOB   = 'cronjob.log';
  
  public static function warning($message, $logFile=self::LOG_DEFAULT){
    $file = fopen(self::LOG_PATH.$logFile, 'a');
    $string = It6_Date::dbNow().' '.$message."\n";
    fwrite($file, $string);
  }

  public static function error($message, $logFile=self::LOG_DEFAULT){
    $file = fopen(self::LOG_PATH.$logFile, 'a');
    $string = It6_Date::dbNow().' '.$message."\n";
    fwrite($file, $string);
  }

  public static function info($message, $logFile=self::LOG_DEFAULT){
    $file = fopen(self::LOG_PATH.$logFile, 'a');
    $string = It6_Date::dbNow().' '.$message."\n";
    fwrite($file, $string);
  }

}

