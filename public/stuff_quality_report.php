<?php
include "../model/modelhead.php";
include "CheckReport_Blue/config.php";  
require_once('../model/codefunjpg.php'); 
if($Id==""){  //其它地方生成时，可能直接送ID，不用加密
	$fArray=explode("|",$f);
	$RuleStr1=$fArray[0];
	$EncryptStr1=$fArray[1];
	$Id=anmaOut($RuleStr1,$EncryptStr1,"f");
}

if($Id>0){//单条记录的品捡报告
	include "stuff_quality_report1.php";
}
?>