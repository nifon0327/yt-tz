<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-17
include "../model/modelhead.php";
$fromWebPage=$funFrom."_list";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="非bom配件请款";
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		$y++;
		}
	}
$Del = "DELETE * from  $DataIn.nonbom11_qksheet  WHERE Id IN($Ids) AND Estate=1 "; 
$Del_result = mysql_query($Del);
if ($Del_result){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功(). $Del<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. $Del </div><br>";
	$OperationResult="N";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.StuffType");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
