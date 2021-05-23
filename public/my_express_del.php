<?php 
//$DataPublic.my3_express 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="我的快递单";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
if($Ids!=""){
	//删除数据库记录
	$delSql = "DELETE FROM $DataPublic.my3_express WHERE Estate=1 AND Id IN ('$Ids')"; 
	$delRresult = mysql_query($delSql);
	if ($delRresult && mysql_affected_rows()>0){
		$Log.="&nbsp;&nbsp; ID号在 $Ids 的 $Log_Item 删除操作成功.<br>";
		}
	else{
		$OperationResult="N";
		$Log.="<div class='redB'>&nbsp;&nbsp;ID号在 $Ids 的 $Log_Item 删除操作失败. $delSql </div><br>";
		}//end if ($Del_result)
	}
else{
	$Log="<div class='redB'>没有选取要删除的记录.</div>";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.my3_express");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>