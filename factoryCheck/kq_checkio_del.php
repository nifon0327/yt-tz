<?php 
//mc 内部模式:OK ewen2013-08-03
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="考勤记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$y=0;
for($i=0;$i<count($checkid);$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}

//删除数据库记录;条件当月记录没结付
//$OtherWhere="AND Number NOT IN (SELECT Number From $DataIn.kqdataother WHERE Month=left(d7check.checkinout.CheckTime,7) AND Number=d7check.checkinout.Number)";
$DelSql = "DELETE FROM d7check.fakecheckinout WHERE Id IN ($Ids)"; 
$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
	$Log.="ID号在 $Ids 的考勤记录删除成功!</br>";			
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的考勤记录删除失败! $DelSql</div></br>";
	$OperationResult="N";
	}
$Page=$IdCount==$y?1:$Page;
$chooseMonth=$IdCount==$y?"":$chooseMonth;
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CheckDate=$CheckDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
