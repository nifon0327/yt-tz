<?php 
//电信-joseph
//代码、数据库共享，加标识-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="功能模块";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);

//步骤3：需处理，执行动作
$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
	
//先删除关系
$DelSql= "DELETE A,B,C
FROM $DataPublic.funmodule A
LEFT JOIN $DataPublic.modulenexus B ON B.ModuleId=A.ModuleId OR B.dModuleId=A.ModuleId
LEFT JOIN $DataIn.upopedom C ON C.ModuleId=A.ModuleId
WHERE A.Id IN ($Ids)"; 
$DelResult = mysql_query($DelSql);
if($DelResult){
	$Log.="ID号在 $Ids 的 $TitleSTR 成功.<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $TitleSTR 失败. $DelSql </div><br>";
	$OperationResult="N";
	}

$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>