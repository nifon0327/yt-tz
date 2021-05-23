<?php   
//电信-zxq 2012-08-01
/*
$DataIn.sys_clientfunmodule
二合一已更新
*/
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$outArray=explode("|",$Mid);
$RuleStr=$outArray[0];
$EncryptStr=$outArray[1];
$ModuleId=anmaOut($RuleStr,$EncryptStr);
$CheckSql= mysql_query("SELECT F.Parameter FROM $DataIn.sys_clientfunmodule F WHERE F.ModuleId='$ModuleId' LIMIT 1",$link_id);
if($CheckRow = mysql_fetch_array($CheckSql)){
	$Parameter=$CheckRow["Parameter"];
	echo"<meta http-equiv=\"Refresh\" content='0;url=".$Parameter."'>";
	}
else{
	echo "No This Page";
	}
?>