<?php 
//$DataPublic.modulenexus 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="产品分类排序";		//需处理
$upDataSheet="$DataIn.producttype";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$TypeId=$checkid[$i];
	//检查是否已存在，是则更新；否则新增
	$OrderId=$i+1;
		$inRecode = "UPDATE $upDataSheet SET OrderId='$OrderId',Date='$Date',Operator='$Operator' WHERE 1 and TypeId=$TypeId ";
		$Log1="更新";
	$inRes=@mysql_query($inRecode);
	if($inRes){
		$Log.="$TypeId 序号为: $OrderId".$Log1."成功! <br>";
		} 
	else{
		$Log.="$TypeId 序号为: $OrderId".$Log1."失败! $inRecode </div><br>";
		$OperationResult="N";
		}
	}//end for
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
