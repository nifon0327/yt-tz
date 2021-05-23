<?php 
//电信-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="假日资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
for($i=0;$i<count($checkid);$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}
//删除数据库记录条件：该月份的考勤统计未出来
$delSql = "DELETE FROM $DataPublic.kqholiday WHERE Id IN ($Ids) AND left(Date,7) NOT IN (SELECT Month FROM $DataIn.kqdata GROUP BY Month)";
$delRresult = mysql_query($delSql);
if($delRresult && mysql_affected_rows()>0){
	$Log.="&nbsp;&nbsp;ID号在 $Ids 的假日资料删除成功!</br>";
	}
else{			
	$Log.="<div class='redB'>&nbsp;&nbsp;ID号在 $Ids 的假日资料删除失败! $delSql </div></br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>