<?php  
//代码、数据库共享-zx
//电信-ZX
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
$Log_Item="ipad功能模块";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Name=FormatSTR($Name);
$Symbol=FormatSTR($Symbol);
$Rate=FormatSTR($Rate);
$Sql = mysql_query("SELECT  MAX(ModuleId) AS abc FROM $DataPublic.sc4_funmodule order by ModuleId DESC",$link_id);
$ModuleId=mysql_result($Sql,0,"abc");
if($ModuleId==""){
	$ModuleId=100+1;
	}
else{
	$ModuleId=$ModuleId+1;
	}		
$inRecode="INSERT INTO $DataPublic.sc4_funmodule (Id,cSign,ModuleId,ModuleName,Parameter,Place,OrderId,Estate,Locks,Date,Operator) VALUES (NULL,'$cSign','$ModuleId','$ModuleName','$Parameter','$Place','$OrderId','1','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
if($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
