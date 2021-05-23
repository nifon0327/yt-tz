<?php  
//电信---yang 20120801
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
$Log_Item="客户登记权限";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&UserId=$UserId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
//先删除该用户权限
$delRecode = "DELETE FROM $DataIn.sys_clientfunpower WHERE UserId='$UserId'"; 
$delAction =@mysql_query($delRecode);
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.sys_clientfunpower");
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
//重设权限
$inRecode="INSERT INTO $DataIn.sys_clientfunpower SELECT NULL,ModuleId,'$UserId','1','0','0','$Operator','$DateTime',null,null,null,null FROM $DataIn.sys_clientfunmodule WHERE Id IN ($Ids)";
$inAction=@mysql_query($inRecode);
if($inAction){ 
	$Log.="$i - $TitleSTR 成功!<br>";
	} 
else{
	$Log.="<div class=redB>$i - $TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>