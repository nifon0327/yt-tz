<?php 
/*电信---yang 20120801
$DataSharing.StuffType
$DataIn.stuffdata
$DataOut.stuffdata
二合一已更新
*/
//步骤1：
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="收件人列表";//需处理
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
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		$y++;
		}
	}
//删除数据库记录
//$Del = "DELETE FROM $DataPublic.my3_exadd WHERE Id IN($Ids) AND Company NOT IN ()"; 

$Del = "DELETE FROM $DataPublic.my3_exadd WHERE Id IN($Ids)"; 
$Del_result = mysql_query($Del);
if ($Del_result){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功. $Del<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. $Del </div><br>";
	$OperationResult="N";
	}//end if ($Del_result)

$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
