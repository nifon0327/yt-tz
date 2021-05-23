<?php 
//电信-zxq 2012-08-01
/*
$DataIn.sys4_gysfunmodule
二合一已更新
*/
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$outArray=explode("|",$Mid);
$RuleStr=$outArray[0];
$EncryptStr=$outArray[1];
$ModuleId=anmaOut($RuleStr,$EncryptStr);

$outArray1=explode("|",$IsPrice);
$RuleStr1=$outArray1[0];
$EncryptStr1=$outArray1[1];
$IsPrice=anmaOut($RuleStr1,$EncryptStr1);
$S_IsPrice=$IsPrice;   //注删过的变量

$CheckSql= mysql_query("SELECT F.Parameter FROM $DataIn.sys4_gysfunmodule F WHERE F.ModuleId='$ModuleId' LIMIT 1",$link_id);
if($CheckRow = mysql_fetch_array($CheckSql)){
	$Parameter=$CheckRow["Parameter"];
	if ($IsWeeks!="") $Parameter.="?IsWeeks=$IsWeeks";
	echo"<meta http-equiv=\"Refresh\" content='0;url=".$Parameter."'>";
	}
else{
	echo "页面不存在";
	}
?>