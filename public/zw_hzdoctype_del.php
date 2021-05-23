<?php 
/*
$DataPublic.zw2_hzdoctype
$DataIn.zw2_hzdoc
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="行政资料分类";//需处理
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
$delSql = "DELETE FROM $DataPublic.zw2_hzdoctype WHERE Id='$Id' and Id NOT IN (SELECT TypeId FROM $DataIn.zw2_hzdoc GROUP BY TypeId UNION SELECT TypeId FROM $DataIn.zw2_hzdoc GROUP BY TypeId)"; 
$delRresult = mysql_query($delSql);
if ($delRresult && mysql_affected_rows()>0){
	$Log.="ID号在(".$Ids.")的".$TitleSTR."成功.<br>";
	//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.zw2_hzdoctype");
	}
else{
	$Log.="<div class='redB'>ID号在(".$Ids.")的".$TitleSTR."失败.</div><br>";
	$OperationResult="N";
	}//end if ($Del_result)
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>