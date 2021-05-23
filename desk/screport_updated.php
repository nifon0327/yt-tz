<?php   
//$DataPublic.modulenexus 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="生产数量记录";		//需处理
$upDataSheet="$DataIn.sc1_cjtj";	//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	//检查是否已存在，是则更新；否则新增	
	if($scFrom==0) {   //要把生产完的状态改回到正在生产中
		$inRecode = "update	 $DataIn.yw1_ordersheet SET scFrom=2 
			WHERE PorderID='$POrderId' AND scFrom=$scFrom AND Estate=1 ";
		$Log1="更新";
		//echo "$inRecode";
		$inRes=@mysql_query($inRecode);
		if($inRes){
			$Log.=" PorderID号为: $PorderID".$Log1."生产状态为2成功! <br>";
			} 
		else{
			$Log.="PorderID号为: $PorderID".$Log1."生成状态为2失败! $inRecode </div><br>";
			$OperationResult="N";
			}
	}
	
	$inRecode = "delete  $upDataSheet from $upDataSheet 
		LEFT JOIN $DataIn.yw1_ordersheet  ON $DataIn.yw1_ordersheet.PorderID=$upDataSheet.PorderID
		WHERE 1 and $upDataSheet.Id=$Id AND $DataIn.yw1_ordersheet.scFrom=$scFrom AND $DataIn.yw1_ordersheet.Estate=1";
		$Log1="删除";
	//echo "$inRecode";	
	$inRes=@mysql_query($inRecode);
	if($inRes){
		$Log.=" Id号为: $Id".$Log1."生产数量记录成功! <br>";
		} 
	else{
		$Log.="Id号为: $Id".$Log1."生产数量记录失败! $inRecode </div><br>";
		$OperationResult="N";
		}
	}//end for
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
