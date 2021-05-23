<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工受训记录";//需处理
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
$delSql = "DELETE A FROM $DataPublic.aqsc08 A WHERE A.Id IN($Ids) "; 
$delRresult = mysql_query($delSql);
if ($delRresult && mysql_affected_rows()>0){
	$Log.="ID号在(".$Ids.")的".$TitleSTR."成功.<br>";
	}
else{
	$Log.="<div class='redB'>ID号在(".$Ids.")的".$TitleSTR."失败. $delSql </div><br>";
	$OperationResult="N";
	}//end if ($Del_result)
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>