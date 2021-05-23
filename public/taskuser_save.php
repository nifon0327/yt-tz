<?php  
//电信-joseph
//代码、数据库相同，记录独立-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
$Log_Item="特殊功能权限";			//需处理
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
//先删后建
$delRecode = "DELETE FROM $DataIn.taskuserdata WHERE UserId='$UserId'"; 
$delAction =@mysql_query($delRecode);
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.taskuserdata");
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$ItemId=$checkid[$i];
	if ($ItemId!=""){
		$inRecode="INSERT INTO $DataIn.taskuserdata (Id,ItemId,UserId) VALUES (NULL,'$ItemId','$UserId')";
		$inAction=@mysql_query($inRecode);
		if($inAction){ 
			$Log.="$i - $UserId $TitleSTR 成功!<br>";
			} 
		else{
			$Log.="<div class=redB>$i -$UserId $TitleSTR 失败! </div><br>";
			$OperationResult="N";
			} 
		}
	}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>