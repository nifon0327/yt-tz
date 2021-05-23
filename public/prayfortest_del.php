<?php 
//步骤1：$DataIn.cwdyfsheet 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item="测试费用";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$y=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}

//删除数据库记录
$DelSql = "DELETE FROM $DataIn.cwdyfsheet WHERE Id='$Id' AND Estate=1";
$DelResult = mysql_query($DelSql);
if($DelResult){
	$Log="ID号在 $Ids 的 $TitleSTR 成功(操作后记录仍在则不符合删除条件)!<br>";
	}
else{
	$Log="<div class='redB'>&nbsp;&nbsp;$x-ID号为  $Id 的 $TitleSTR 失败!</div><br>";
	$OperationResult="N";
	}
//如果该页记录全删，则返回第一页
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>