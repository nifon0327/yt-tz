<?php 
//电信-joseph
//代码、数据库相同，记录独立-EWEN 2012-08-13
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item="特殊功能权限";//需处理
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
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}

//删除数据库记录
$Del = "DELETE FROM $DataIn.taskuserdata WHERE Id IN ($Ids)"; 
$result = mysql_query($Del);
if($result){
	$Log.="ID号在 $Ids 的 $TitleSTR 成功.<br>";
	$y++;
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $TitleSTR 失败.</div><br>";
	$OperationResult="N";			
	}//end if ($result)
$UserId=$IdCount==$y?"":$UserId;
$ALType="From=$From&UserId=$UserId";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>