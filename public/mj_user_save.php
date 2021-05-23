<?php  
//代码、数据共享-EWEN 2012-09-18
include "../model/modelhead.php";
//步骤2：
$Log_Item="门禁用户";			//需处理
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
$Password=MD5($Password);
$inRecode="INSERT INTO $DataPublic.accessguard_user SELECT NULL,Number,'$chkType','0','$Password','$DateTime','1','0','$Operator',0,'$Operator',NOW(),'$Operator',NOW() FROM $DataPublic.staffmain WHERE Name Like '$Name' AND Estate='1' AND cSign='$Login_cSign' LIMIT 1";
$inAction=@mysql_query($inRecode);
if ($inAction){
	$Log.="门禁用户 $Name 的 $TitleSTR 成功! <br>";
	}
else{
	$Log.="<div class=redB>门禁用户 $Name 的 $TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	}
//$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
//$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
