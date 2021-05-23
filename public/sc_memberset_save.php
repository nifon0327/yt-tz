<?php 
/*电信---yang 20120801
未使用
include "../model/modelhead.php";
//步骤2：
$Log_Item="车间小组每天成员";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_add";
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
	//写入数据
$inRecode="INSERT INTO $DataIn.sc1_memberset SELECT NULL,Leader,Number,'$DataTime','0','$Operator' FROM $DataIn.sc1_member";
$inResult=@mysql_query($inRecode);
if($inResult){
	$Log.="&nbsp;&nbsp;".$Log_Item.$Log_Funtion."成功!</br>";
	}
else{
	$Log.="<div class='redB'>&nbsp;&nbsp;".$Log_Item.$Log_Funtion."失败!</div></br>";
	$OperationResult="N";
	}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
*/
?>
