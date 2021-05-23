<?php

include_once ("configure.php");

class log{
	
	public static function e($msg, $type)
    {
        self::write($msg, $type);
    }
	
	private static function write($msg, $type)
    {
		//判断log.log的大小，超过60kb则新起一个log
		
       $filename =  ROOTPATH."weixin/log/debug.log";
	   
	   if(filesize($filename)>61440){
		   
		   rename($filename, ROOTPATH.'weixin/log/'.date('YmdHis').'.log');
		   
	   }
		
       $logFile = fopen($filename, "aw");
		
       fwrite($logFile, $type . "/" . date(" Y-m-d h:i:s") . "  " . $msg . "\n");
		
       fclose($logFile);
    }
}
