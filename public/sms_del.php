<?php 
//$DataIn.smsdata 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="短消息";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){//删除条件：该消息已处理
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	

$delSql = "DELETE FROM $DataPublic.smsdata WHERE Estate=0 AND Id IN ($Ids)"; 
$delRresult = mysql_query($delSql);
if ($delRresult && mysql_affected_rows()>0){
	$Log.="&nbsp;&nbsp;ID号在 $Ids 的 $Log_Item 删除操作成功.<br>";
	}
else{
	$OperationResult="N";
	$Log.="<div class='redB'>&nbsp;&nbsp;ID号在 $Ids 的 $Log_Item 删除操作失败(检查是否已处理).</div><br>";
	}//end if ($Del_result)

$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>