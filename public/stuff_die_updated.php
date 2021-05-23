<?php   
//电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 配件模具关系保存");
$fromWebPage="stuff_die_read";
$nowWebPage="stuff_die_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Log_Item="配件模具关系";
$Log_Funtion="保存";
$ALType="From=$From&StuffType=$StuffType";
$DelSql = "DELETE FROM $DataIn.cut_die WHERE StuffId='$StuffId'"; 
$DelResult = mysql_query($DelSql);
$dataArray=explode("|",$SIdList);
$Count=count($dataArray);
$x=1;
$Date=date("Y-m-d");
for ($i=0;$i<$Count;$i++){
	$GoodsId=$dataArray[$i];
	
	//插入新的关系	
	$IN_recodeN="INSERT INTO $DataIn.cut_die (Id, ProductId, StuffId, GoodsId) VALUES (NULL,'0','$StuffId','$GoodsId')";
	$resN=@mysql_query($IN_recodeN);
	if($resN){
		$Log.="&nbsp;&nbsp; $x -ID号为 $StuffId 的配件和ID号为 $GoodsId 的模具已加入关系表中!</br>";
		}
	else{
		$Log.="<div class='redB'>&nbsp;&nbsp; $x -ID号为 $StuffId 的配件和ID号为 $GoodsId 的模具已加入关系表中!</div></br>";
		}
  $x++;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>