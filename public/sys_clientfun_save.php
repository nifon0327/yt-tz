<?php  
//电信---yang 20120801
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
$Log_Item="用户功能模块";			//需处理
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
$Sql = mysql_query("SELECT  MAX(ModuleId) AS abc FROM $DataIn.sys_clientfunmodule order by ModuleId DESC",$link_id);
$ModuleId=mysql_result($Sql,0,"abc");
if($ModuleId==""){
	$ModuleId=100000+1;
	}
else{
	$ModuleId=$ModuleId+1;
	}		
$inRecode="INSERT INTO $DataIn.sys_clientfunmodule (Id,ModuleId,ModuleName,Parameter,Remark,AutoName,Oby,Estate,Locks,Date,Operator) VALUES (NULL,'$ModuleId','$ModuleName','$Parameter','$Remark','0','$Oby','1','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
if($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
